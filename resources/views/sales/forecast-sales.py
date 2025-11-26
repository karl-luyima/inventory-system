import pandas as pd
import numpy as np
from statsmodels.tsa.api import ExponentialSmoothing
import matplotlib.pyplot as plt
import pickle 
import mysql.connector 
from sqlalchemy import create_engine, text 
import datetime
import os # For managing the log file

# --- Configuration ---
# Your MySQL Database Credentials
DB_CONFIG = {
    'host': '127.0.0.1', 
    'database': 'inventorymgt',
    'user': 'root',     
    'password': ''      
}
# Define the connection string for SQLAlchemy
DB_URL = (
    f'mysql+mysqlconnector://{DB_CONFIG["user"]}:'
    f'{DB_CONFIG["password"]}@{DB_CONFIG["host"]}:3306/{DB_CONFIG["database"]}'
)

FORECAST_WEEKS = 30
SEASONAL_PERIODS = 13 # Quarterly Cycle in Weeks
MINIMUM_DATA_WEEKS = SEASONAL_PERIODS + 1 
MODEL_FILENAME_BASE = 'holt_winters_weekly_model_pdt_' 
LOG_FILENAME = 'forecast_status_log.txt'

# --- Target Product IDs (The full range we confirmed has data) ---
TARGET_PDT_IDS = list(range(41, 61)) 
# ---------------------

def log_status(pdt_id, status, details=""):
    """Appends a status entry to the log file."""
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_message = f"[{timestamp}] PDT ID {pdt_id:<3} | STATUS: {status:<10} | DETAILS: {details}\n"
    
    with open(LOG_FILENAME, 'a') as f:
        f.write(log_message)

def fetch_sales_data(config, pdt_id):
    """Connects to MySQL and aggregates daily sales data from the single 'sales' table."""
    
    # ✅ CRITICAL CHANGE: Query now sums the 'quantity' column for accurate inventory forecasting
    QUERY = f"""
    SELECT
        DATE(date) AS SaleDate,
        SUM(quantity) AS TotalQuantitySold  
    FROM sales
    WHERE pdt_id = {pdt_id}
    GROUP BY SaleDate
    ORDER BY SaleDate ASC;
    """
    
    try:
        conn = mysql.connector.connect(**config)
        sales_df = pd.read_sql(QUERY, conn, index_col='SaleDate')
        conn.close()
        
        # Rename the column to 'sales' so the rest of the script works
        sales_df.rename(columns={'TotalQuantitySold': 'sales'}, inplace=True)
        sales_df.index.name = 'date'
        sales_series = sales_df['sales'].copy()
        sales_series.index = pd.to_datetime(sales_series.index)
        
        return sales_series

    except mysql.connector.Error as err:
        log_status(pdt_id, "DB_ERROR", f"MySQL Error: {err}")
        return None

def prepare_data(sales_series):
    """Aggregates daily sales data into weekly totals for the model."""
    
    # Resample daily data to weekly sums
    y_weekly = sales_series.resample('W').sum()
    
    # Remove initial zero if the first entry is zero
    if not y_weekly.empty and y_weekly.iloc[0] == 0:
        y_weekly = y_weekly.iloc[1:]
        
    return y_weekly

def train_and_forecast(y_train, periods, seasonal_periods, pdt_id):
    """Trains the Holt-Winters model, saves it, and generates a future forecast."""
    
    model_filename = f"{MODEL_FILENAME_BASE}{pdt_id}.pkl"
    
    try:
        hw_model = ExponentialSmoothing(
            y_train,
            seasonal_periods=seasonal_periods, 
            trend='add',
            seasonal='add',
            initialization_method="heuristic" 
        ).fit()
        
        with open(model_filename, 'wb') as f:
            pickle.dump(hw_model, f)
        
        future_forecast = hw_model.forecast(steps=periods)
        future_forecast_clipped = np.clip(future_forecast.values, a_min=0, a_max=None)
        
        return future_forecast.index, future_forecast_clipped
        
    except Exception as e:
        log_status(pdt_id, "MODEL_FAIL", f"Training/Pickle Error: {e}")
        return None, None

def plot_forecast(historical_data, forecast_dates, forecast_values, pdt_id):
    """Plots the future forecast and saves the image."""
    
    plt.figure(figsize=(14, 6))
    
    forecast_series = pd.Series(forecast_values, index=forecast_dates)
    forecast_series.plot(label=f'{FORECAST_WEEKS}-Week Forecast', color='green', linestyle='--')
    
    plt.title(f'Weekly Quantity Forecast for Product {pdt_id} (Holt-Winters)') # ⬅️ Updated Title
    plt.xlabel('Date')
    plt.ylabel('Total Units Sold (Weekly)') # ⬅️ Updated Y-Axis Label
    plt.legend()
    plt.grid(True)
    
    plot_file = f'weekly_forecast_pdt_{pdt_id}.png'
    plt.savefig(plot_file)
    plt.close() 

def insert_forecast_to_db(pdt_id, forecast_dates, predicted_sales, db_url):
    """Inserts the forecast data into the product_forecasts table."""
    
    forecast_df = pd.DataFrame({
        'pdt_id': [pdt_id] * len(predicted_sales), 
        'forecast_date': forecast_dates, 
        'predicted_sales': predicted_sales.round(2)
    })
    
    try:
        engine = create_engine(db_url)
        
        # Clear old forecasts for this pdt_id before inserting new ones
        with engine.connect() as connection:
             connection.execute(text(f"DELETE FROM product_forecasts WHERE pdt_id = {pdt_id}"))
             connection.commit()
             
        # Insert the new forecast data
        forecast_df.to_sql(
            'product_forecasts', 
            con=engine, 
            if_exists='append', 
            index=False
        )
        log_status(pdt_id, "DB_SAVE", "Forecast data saved to DB.")
        return True
        
    except Exception as e:
        log_status(pdt_id, "DB_ERROR", f"Insertion Failed: {e}")
        return False

def run_single_forecast(pdt_id):
    """Encapsulates the full forecasting process for a single product ID."""
    print(f"\n--- Starting Forecast for Product ID: {pdt_id} ---")
    
    # 1. Fetch Daily Sales Data 
    daily_sales_series = fetch_sales_data(DB_CONFIG, pdt_id)
    
    if daily_sales_series is None or daily_sales_series.empty:
        log_status(pdt_id, "SKIPPED", "No sales data found in DB.")
        return
        
    # 2. Aggregate Daily Data to Weekly Series
    y_weekly = prepare_data(daily_sales_series)
    weekly_data_length = len(y_weekly)
    
    # 3. CRITICAL CHECK: Ensure enough data exists
    if weekly_data_length < MINIMUM_DATA_WEEKS:
        log_status(pdt_id, "SKIPPED", f"Insufficient data ({weekly_data_length} weeks < {MINIMUM_DATA_WEEKS} required).")
        return
    
    # ⚠️ CHECK: Ensure aggregated data is not all zeros
    if y_weekly.sum() == 0:
        log_status(pdt_id, "SKIPPED", "Aggregated sales are all zero.")
        return
        
    # 4. Train and Forecast
    forecast_dates, predicted_sales = train_and_forecast(
        y_weekly, 
        FORECAST_WEEKS, 
        SEASONAL_PERIODS,
        pdt_id
    )
    
    if forecast_dates is None:
        return # Error already logged in train_and_forecast
        
    # 5. Insert Results into Database 
    db_success = insert_forecast_to_db(pdt_id, forecast_dates, predicted_sales, DB_URL)
    
    if db_success:
        # 6. Plot Results (only plot if the database save was successful)
        plot_forecast(y_weekly, forecast_dates, predicted_sales, pdt_id)
        log_status(pdt_id, "SUCCESS", "Model trained, forecast saved, and plot created.")


# =======================================================================
# MAIN EXECUTION BLOCK 
# =======================================================================
if __name__ == '__main__':
    # Initialize the log file
    if os.path.exists(LOG_FILENAME):
        os.remove(LOG_FILENAME)
    
    start_time = datetime.datetime.now()
    log_status(0, "START", f"Forecasting process initiated for {len(TARGET_PDT_IDS)} products ({TARGET_PDT_IDS[0]} to {TARGET_PDT_IDS[-1]}).")
    
    print(f"Starting sales forecast for products: {TARGET_PDT_IDS[0]} to {TARGET_PDT_IDS[-1]}")
    print(f"A detailed log will be saved to: {LOG_FILENAME}")

    # Loop through all target product IDs 
    for product_id in TARGET_PDT_IDS:
        run_single_forecast(product_id)
    
    end_time = datetime.datetime.now()
    duration = end_time - start_time
    
    log_status(0, "END", f"Forecasting complete. Total time: {duration.total_seconds():.2f} seconds.")
    print("\nAll product forecasts completed. Check the log file for detailed status.")