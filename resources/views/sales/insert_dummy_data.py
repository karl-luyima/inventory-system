import pandas as pd
import numpy as np
import mysql.connector
from datetime import date, timedelta
import random

# --- Database Configuration (Must match your forecast script) ---
DB_CONFIG = {
    'host': '127.0.0.1', 
    'database': 'inventorymgt',
    'user': 'root',     
    'password': ''      
}

# --- Data Generation Configuration ---
# Products currently lacking sales data history (44 to 60)
TARGET_NEW_PDTS = list(range(44, 61)) # 17 products total

# Generating 18 weeks of data: 17 products * 18 weeks = 306 new sales records
WEEKS_TO_GENERATE = 18 

# =========================================================================
# ✅ CORRECTION HERE: Anchor the end date to the correct year (2025)
# Set the end date to last Saturday (Nov 15, 2025) and calculate backwards.
# NOTE: Using the current date (Nov 22, 2025) to calculate the last weekly date.

current_date_anchor = date(2025, 11, 22) # Current date is Nov 22, 2025
END_DATE = current_date_anchor - timedelta(days=current_date_anchor.weekday() + 2) 
# The above calculates the last Saturday (Nov 15) if you want a full week break.
# For simplicity, we can just use the current date anchor and subtract 1 week:
END_DATE = date.today() - timedelta(weeks=1) # Will set the end date to Nov 15, 2025
START_DATE = END_DATE - timedelta(weeks=WEEKS_TO_GENERATE)

print(f"Dummy data will be generated from: {START_DATE} to {END_DATE}")
# =========================================================================

# Range for dummy sales values
MIN_QUANTITY = 1
MAX_QUANTITY = 5
DUMMY_UNIT_PRICE = 50.00 

def generate_dummy_data():
    """Generates synthetic sales data for products 44-60."""
    print("Preparing dummy sales records...")
    
    sales_records = []
    current_date = START_DATE
    week_counter = 0

    while week_counter < WEEKS_TO_GENERATE:
        for pdt_id in TARGET_NEW_PDTS:
            
            # Simulate one sale for the current product on the current weekly date
            quantity = random.randint(MIN_QUANTITY, MAX_QUANTITY)
            total_amount = quantity * DUMMY_UNIT_PRICE
            
            # Record structured to match the columns in your 'sales' table
            sales_records.append({
                'pdt_id': pdt_id, 
                'quantity': quantity,
                'totalAmount': total_amount,
                'date': current_date.strftime('%Y-%m-%d')
            })
            
        current_date += timedelta(weeks=1) # Changed to +1 week to ensure 18 data points are spaced correctly
        week_counter += 1
        
    print(f"Generated {len(sales_records)} sales records for products {TARGET_NEW_PDTS[0]}-{TARGET_NEW_PDTS[-1]}.")
    return sales_records

def insert_dummy_data(config, sales_data):
    """Inserts the generated sales data into the database's single 'sales' table."""
    
    # ✅ FINAL CORRECTED SQL: Only inserts into the 'sales' table
    INSERT_SALE_SQL = "INSERT INTO sales (pdt_id, quantity, totalAmount, date) VALUES (%s, %s, %s, %s)"

    try:
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor()
        
        print("Starting insertion into the 'sales' table...")
        
        inserted_sales_count = 0
        
        for sale_record in sales_data:
            
            # 1. Insert into 'sales' table
            cursor.execute(INSERT_SALE_SQL, (
                sale_record['pdt_id'], 
                sale_record['quantity'], 
                sale_record['totalAmount'], 
                sale_record['date']
            ))
            
            inserted_sales_count += 1
            
        conn.commit()
        print(f"SUCCESS! {inserted_sales_count} sales records inserted into the database.")
        
    except mysql.connector.Error as err:
        print(f"Database Error: {err}")
        print("Insertion failed. Please verify your DB configuration.")
        
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()


if __name__ == '__main__':
    # 1. Generate the data
    sales_data = generate_dummy_data()
    
    # 2. Insert the data into MySQL
    insert_dummy_data(DB_CONFIG, sales_data)

    print("\nDummy data insertion complete. You can now run your full forecasting script.")