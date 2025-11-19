import os
import pandas as pd
import numpy as np
import joblib
from datetime import timedelta
from sqlalchemy import create_engine
from lightgbm.basic import Booster

# =====================================
# Paths
# =====================================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "sales_model.pkl")
OUTPUT_PATH = os.path.join(BASE_DIR, "forecast_output.csv")

# =====================================
# 1️⃣ Connect to MySQL and load data
# =====================================
DB_USER = "root"
DB_PASSWORD = ""
DB_HOST = "localhost"
DB_NAME = "inventorymgt"

engine = create_engine(f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}/{DB_NAME}")

# Pull sales and products tables
sales_query = """
SELECT s.date, s.totalAmount AS sales, p.pdt_id, p.pdt_name
FROM sales s
JOIN products p ON s.pdt_id = p.pdt_id
ORDER BY p.pdt_name, s.date
"""
data = pd.read_sql(sales_query, engine)
data['date'] = pd.to_datetime(data['date'])
data = data.sort_values(['pdt_id', 'date']).reset_index(drop=True)
data['sales_log'] = np.log1p(data['sales'])

# =====================================
# 2️⃣ Load LightGBM model
# =====================================
model = joblib.load(MODEL_PATH)
if hasattr(model, "booster_"):
    model = model.booster_
    is_booster = True
else:
    is_booster = isinstance(model, Booster)

# =====================================
# 3️⃣ Feature Engineering
# =====================================
def create_features(df, last_data=None):
    df = df.copy()
    for lag in [1,3,7,14,30,60,90]:
        if last_data is None:
            df[f'lag_{lag}'] = df['sales_log'].shift(lag)
        else:
            df[f'lag_{lag}'] = last_data['sales_log'].iloc[-lag]

    for window in [7,14,30]:
        if last_data is None:
            df[f'roll_{window}_mean'] = df['sales_log'].shift(1).rolling(window).mean()
            df[f'roll_{window}_std']  = df['sales_log'].shift(1).rolling(window).std()
            df[f'roll_{window}_median'] = df['sales_log'].shift(1).rolling(window).median()
        else:
            df[f'roll_{window}_mean'] = last_data['sales_log'].iloc[-window:].mean()
            df[f'roll_{window}_std']  = last_data['sales_log'].iloc[-window:].std()
            df[f'roll_{window}_median'] = last_data['sales_log'].iloc[-window:].median()

    df['day_of_week'] = df['date'].dt.dayofweek
    df['month'] = df['date'].dt.month
    df['week_of_year'] = df['date'].dt.isocalendar().week.astype(int)
    df['quarter'] = df['date'].dt.quarter
    df['is_weekend'] = df['day_of_week'].isin([5,6]).astype(int)
    df['day_of_month'] = df['date'].dt.day
    df['year'] = df['date'].dt.year

    return df

# =====================================
# 4️⃣ Prepare features
# =====================================
data = create_features(data)
data = data.dropna().reset_index(drop=True)
numeric_cols = data.select_dtypes(include=[np.number]).columns.tolist()
features = [col for col in numeric_cols if col not in ['sales', 'sales_log']]

X = data[features].astype(float)
if is_booster:
    data['predicted_sales'] = np.expm1(model.predict(X.values))
else:
    data['predicted_sales'] = np.expm1(model.predict(X))

# =====================================
# 5️⃣ Forecast next 30 days per product
# =====================================
forecast_list = []

for pdt_id, group in data.groupby('pdt_id'):
    last_data = group.copy()
    future_dates = pd.date_range(start=last_data['date'].max() + timedelta(days=1), periods=30)
    future_sales = []

    for date in future_dates:
        temp = pd.DataFrame({'date':[date]})
        temp = create_features(temp, last_data=last_data)
        for feat in features:
            if feat not in temp.columns:
                temp[feat] = 0
        X_future = temp[features].astype(float)
        pred_log = model.predict(X_future.values)[0] if is_booster else model.predict(X_future)[0]
        pred = np.expm1(pred_log)
        future_sales.append(pred)

        new_row = pd.DataFrame({'date':[date], 'sales_log':[pred_log], 'sales':[pred]})
        last_data = pd.concat([last_data, new_row], ignore_index=True)

    df_forecast = pd.DataFrame({
        'pdt_id': pdt_id,
        'forecast_date': future_dates,
        'predicted_sales': future_sales
    })

    # Filter out forecasts already in the DB for this product and future dates
    existing = pd.read_sql(f"""
        SELECT forecast_date FROM product_forecasts
        WHERE pdt_id = {pdt_id}
        AND forecast_date >= '{future_dates.min().date()}'
    """, engine)

    if not existing.empty:
        df_forecast = df_forecast[~df_forecast['forecast_date'].isin(existing['forecast_date'])]

    forecast_list.append(df_forecast)

forecast_df = pd.concat(forecast_list, ignore_index=True)

# =====================================
# 6️⃣ Save to CSV
# =====================================
forecast_df.to_csv(OUTPUT_PATH, index=False)
print(f"Forecast saved to {OUTPUT_PATH}")

# =====================================
# 7️⃣ Insert new forecasts into DB
# =====================================
if not forecast_df.empty:
    forecast_df.to_sql('product_forecasts', engine, if_exists='append', index=False)
    print("New forecasts appended to 'product_forecasts' table")
else:
    print("No new forecasts to insert (already exist in DB)")
