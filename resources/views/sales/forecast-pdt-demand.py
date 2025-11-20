import mysql.connector
import json
import random
from datetime import date, timedelta
import sys
import logging


DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root', 
    'password': '', 
    'database': 'inventory_system' 
}

# Define the number of weeks to forecast into the future
FORECAST_WEEKS = 30
# Start the forecast one week from the current date
START_DATE = date.today() + timedelta(weeks=1)

# Set up logging to print error messages to stderr (which Laravel captures)
logging.basicConfig(stream=sys.stderr, level=logging.INFO,
                    format='[Python Forecast] %(levelname)s: %(message)s')

def get_db_connection():
    """Establishes and returns a database connection."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except mysql.connector.Error as err:
        logging.error(f"Database connection error: {err}")
        sys.exit(1) # Exit with error code

def fetch_product_data(conn):
    """Fetches all product IDs and names."""
    query = "SELECT pdt_id, pdt_name FROM products"
    cursor = conn.cursor(dictionary=True)
    cursor.execute(query)
    products = cursor.fetchall()
    cursor.close()
    return products

def generate_xai_explanation(product_name):
    """
    Simulates the generation of Explainable AI (XAI) data.
    
    In a real system, this would come from a library like SHAP or LIME 
    analyzing the model's prediction for the next period.
    """
    factors = [
        "Price History",
        "Competitor Price Changes",
        "Promotional Events",
        "Seasonal Trends",
        "Inventory Stock Level",
        "Historical Sales Volatility"
    ]
    
    explanation = []
    
    # Generate random importance scores and signs for a subset of factors
    for factor in random.sample(factors, k=random.randint(3, 5)):
        # Generate importance between 0.05 and 0.5, ensuring sum is not critical here
        importance = round(random.uniform(0.05, 0.5), 2)
        # Randomly assign positive or negative sign to the impact
        sign = random.choice(['positive', 'negative'])
        
        explanation.append({
            "feature": factor,
            "importance": importance if sign == 'positive' else -importance,
            "sign": sign
        })
        
    return json.dumps(explanation)

def generate_forecast_and_xai(product_id, conn):
    """
    Simulates running a full time-series forecast (e.g., ARIMA/Prophet) 
    and generating the corresponding XAI data for the next N weeks.
    """
    # NOTE: In a real scenario, you would fetch sales data for product_id here,
    # train a model, and predict. For this script, we simulate the output.
    
    forecast_records = []
    
    # Simulate a base demand (e.g., based on average historical sales)
    base_demand = random.randint(100, 500)
    
    for i in range(FORECAST_WEEKS):
        forecast_date = START_DATE + timedelta(weeks=i)
        
        # Simulate fluctuation (e.g., +/- 25% of base demand)
        fluctuation = random.uniform(0.75, 1.25)
        predicted_sales = round(base_demand * fluctuation, 2)

        # Generate XAI explanation for this specific forecast record
        # For simplicity, we assume the XAI explanation is the same for the whole period 
        # in this simulation, but ideally, it would be unique per forecast date.
        if i == 0:
            explanation_json = generate_xai_explanation(product_id)
        
        forecast_records.append({
            'pdt_id': product_id,
            'forecast_date': forecast_date.strftime('%Y-%m-%d'),
            'predicted_sales': predicted_sales,
            'explanation_json': explanation_json if i == 0 else None, # Only store XAI once per product for simplicity
        })
        
    return forecast_records

def save_forecast_to_db(conn, forecast_records):
    """Saves the generated forecasts to the product_forecasts table."""
    
    # Clear old forecasts for simplicity, keeping the table clean
    product_id = forecast_records[0]['pdt_id']
    delete_query = "DELETE FROM product_forecasts WHERE pdt_id = %s"
    
    insert_query = """
    INSERT INTO product_forecasts 
    (pdt_id, forecast_date, predicted_sales, explanation_json) 
    VALUES (%s, %s, %s, %s)
    """
    
    cursor = conn.cursor()
    
    try:
        # Delete existing forecasts for this product
        cursor.execute(delete_query, (product_id,))
        
        # Insert new forecasts
        for record in forecast_records:
            cursor.execute(insert_query, (
                record['pdt_id'],
                record['forecast_date'],
                record['predicted_sales'],
                record['explanation_json']
            ))
        
        conn.commit()
    except mysql.connector.Error as err:
        conn.rollback()
        logging.error(f"Error saving forecast for product {product_id}: {err}")
    finally:
        cursor.close()

def main():
    """Main execution function."""
    conn = get_db_connection()
    if conn is None:
        return
    
    products = fetch_product_data(conn)
    
    if not products:
        logging.warning("No products found in the database. Cannot generate forecasts.")
        conn.close()
        return

    # Delete all old forecasts before generating new ones (optional, but ensures clean slate)
    logging.info("Clearing all previous forecasts from product_forecasts table...")
    clear_cursor = conn.cursor()
    clear_cursor.execute("TRUNCATE TABLE product_forecasts")
    conn.commit()
    clear_cursor.close()
    
    logging.info(f"Starting forecast generation for {len(products)} products...")
    
    for product in products:
        pdt_id = product['pdt_id']
        pdt_name = product['pdt_name']
        
        logging.info(f"Generating forecast for: {pdt_name} (ID: {pdt_id})")
        
        # 1. Generate the forecast data (simulated)
        forecast_records = generate_forecast_and_xai(pdt_id, conn)
        
        # 2. Save the data to the database
        save_forecast_to_db(conn, forecast_records)
        
    logging.info("Forecast generation complete!")
    conn.close()

if __name__ == "__main__":
    try:
        main()
    except Exception as e:
        # Catch any unexpected Python errors and log them
        logging.critical(f"A critical error occurred during script execution: {e}")
        sys.exit(1)