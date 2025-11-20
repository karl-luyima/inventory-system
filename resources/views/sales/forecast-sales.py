import pandas as pd
import numpy as np
from statsmodels.tsa.api import ExponentialSmoothing
import matplotlib.pyplot as plt
import pickle 
import mysql.connector 
from sqlalchemy import create_engine, text 

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
MODEL_FILENAME = 'holt_winters_weekly_model.pkl'
# --- Aggregate Forecast ID ---
# MUST be an existing ID in your 'products' table to satisfy the foreign key constraint
AGGREGATE_PDT_ID = 41 
# ---------------------

def fetch_sales_data(config):
    """Connects to MySQL, extracts, and aggregates daily sales data."""
    
    QUERY = """
    SELECT
        DATE(date) AS SaleDate,
        SUM(totalAmount) AS TotalSales
    FROM sales
    GROUP BY SaleDate
    ORDER BY SaleDate ASC;
    """
    
    try:
        conn = mysql.connector.connect(**config)
        sales_df = pd.read_sql(QUERY, conn, index_col='SaleDate')
        conn.close()
        
        sales_df.rename(columns={'TotalSales': 'sales'}, inplace=True)
        sales_df.index.name = 'date'
        
        sales_series = sales_df['sales'].copy()
        sales_series.index = pd.to_datetime(sales_series.index)
        
        return sales_series

    except mysql.connector.Error as err:
        print(f"Database Error: {err}")
        print("Please ensure your MySQL server is running and the credentials are correct.")
        return None

def prepare_data(sales_series):
    """Aggregates daily sales data into weekly totals for the model."""
    
    y_weekly = sales_series.resample('W').sum()
    
    if not y_weekly.empty and y_weekly.iloc[0] == 0:
        y_weekly = y_weekly.iloc[1:]
        
    return y_weekly

def train_and_forecast(y_train, periods, seasonal_periods):
    """Trains the Holt-Winters model, saves it, and generates a future forecast."""
    
    print("Training Holt-Winters Model with robust initialization...")
    hw_model = ExponentialSmoothing(
        y_train,
        seasonal_periods=seasonal_periods, 
        trend='add',
        seasonal='add',
        initialization_method="heuristic" 
    ).fit()
    
    try:
        with open(MODEL_FILENAME, 'wb') as f:
            pickle.dump(hw_model, f)
        print(f"New model successfully saved as: {MODEL_FILENAME}")
    except Exception as e:
        print(f"Warning: Could not save model file. Error: {e}")

    future_forecast = hw_model.forecast(steps=periods)
    
    future_forecast_clipped = np.clip(future_forecast.values, a_min=0, a_max=None)
    
    return future_forecast.index, future_forecast_clipped

def plot_forecast(historical_data, forecast_dates, forecast_values):
    """Plots the historical data and the future forecast."""
    
    plt.figure(figsize=(14, 6))
    historical_data.plot(label='Historical Weekly Total Sales', color='blue')
    
    forecast_series = pd.Series(forecast_values, index=forecast_dates)
    forecast_series.plot(label=f'{FORECAST_WEEKS}-Week Forecast', color='green', linestyle='--')
    
    plt.title('Weekly Total Retail Sales Forecast (Holt-Winters)')
    plt.xlabel('Date')
    plt.ylabel('Total Sales Amount (Weekly)')
    plt.legend()
    plt.grid(True)
    
    plot_file = 'final_weekly_forecast.png'
    plt.savefig(plot_file)
    print(f"\nForecast plot saved as: {plot_file}")

# =======================================================================
# MAIN EXECUTION BLOCK 
# =======================================================================
if __name__ == '__main__':
    print("Starting weekly sales forecast using live database data...")
    
    # 1. Fetch Daily Sales Data from MySQL
    daily_sales_series = fetch_sales_data(DB_CONFIG)
    
    if daily_sales_series is None or daily_sales_series.empty:
        print("Could not retrieve sales data or data is empty. Exiting.")
        exit()
        
    # 2. Aggregate Daily Data to Weekly Series
    y_weekly = prepare_data(daily_sales_series)
    
    weekly_data_length = len(y_weekly)
    print(f"Successfully aggregated {len(daily_sales_series.index.unique())} days of data into {weekly_data_length} weeks.")
    
    # 3. CRITICAL CHECK: Ensure enough data exists for model initialization
    if weekly_data_length < MINIMUM_DATA_WEEKS:
        print("... ERROR: INSUFFICIENT DATA HISTORY ...")
        exit()
        
    # 4. Train and Forecast
    forecast_dates, predicted_sales = train_and_forecast(
        y_weekly, 
        FORECAST_WEEKS, 
        SEASONAL_PERIODS
    )
    
    # 5. Create DataFrame and INSERT Results into Database 
    forecast_df = pd.DataFrame({
        # *** FIX: Using AGGREGATE_PDT_ID = 41 which is a valid product ID ***
        'pdt_id': [AGGREGATE_PDT_ID] * len(predicted_sales), 
        'forecast_date': forecast_dates, 
        'predicted_sales': predicted_sales.round(2)
    })
    
    try:
        # Create the SQLAlchemy Engine
        engine = create_engine(DB_URL)
        
        # Clear old aggregate forecasts (pdt_id=41) before inserting new ones
        with engine.connect() as connection:
             connection.execute(text(f"DELETE FROM product_forecasts WHERE pdt_id = {AGGREGATE_PDT_ID}"))
             connection.commit()
             
        # Insert the new forecast data
        forecast_df.to_sql(
            'product_forecasts', 
            con=engine, 
            if_exists='append', 
            index=False
        )
        print(f"\nSUCCESS: 30-Week Aggregate Forecast successfully saved to 'product_forecasts' table (using pdt_id={AGGREGATE_PDT_ID}).")
        
    except Exception as e:
        print(f"\nDATABASE INSERTION ERROR: Failed to insert forecast data. {e}")
        
    # 6. Plot Results
    plot_forecast(y_weekly, forecast_dates, predicted_sales)