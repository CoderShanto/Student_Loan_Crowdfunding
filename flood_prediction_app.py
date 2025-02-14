from flask import Flask, request, jsonify, render_template_string
import sqlite3
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import tensorflow as tf
from statsmodels.tsa.arima.model import ARIMA

from prophet import Prophet



app = Flask(__name__)

# Database connection
def get_db_connection():
    conn = sqlite3.connect('flood_data.db')
    conn.row_factory = sqlite3.Row
    return conn

# Create database table if not exists
def create_table():
    conn = get_db_connection()
    conn.execute('''
        CREATE TABLE IF NOT EXISTS flood_data (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            date TEXT NOT NULL,
            rainfall FLOAT NOT NULL,
            river_level FLOAT NOT NULL,
            flood_status TEXT NOT NULL
        )
    ''')
    conn.commit()
    conn.close()

create_table()

# HTML Template
html_template = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Prediction System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #002855; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; }
        button { padding: 10px 20px; background: #002855; color: white; border: none; cursor: pointer; }
        button:hover { background: #004080; }
    </style>
</head>
<body>
    <h1>Flood Prediction System</h1>

    <h2>Add Historical Data</h2>
    <form id="add-data-form">
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="rainfall">Rainfall (mm):</label>
            <input type="number" id="rainfall" name="rainfall" required>
        </div>
        <div class="form-group">
            <label for="river_level">River Level (m):</label>
            <input type="number" id="river_level" name="river_level" required>
        </div>
        <div class="form-group">
            <label for="flood_status">Flood Status (Yes/No):</label>
            <input type="text" id="flood_status" name="flood_status" required>
        </div>
        <button type="submit">Add Data</button>
    </form>

    <h2>Predictions</h2>
    <button id="predict-btn">Predict Future Floods</button>
    <div id="predictions"></div>

    <h2>Historical Data</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Rainfall</th>
                <th>River Level</th>
                <th>Flood Status</th>
            </tr>
        </thead>
        <tbody id="data-table"></tbody>
    </table>

    <script>
        async function loadData() {
            const response = await fetch('/data');
            const data = await response.json();
            const tableBody = document.getElementById('data-table');
            tableBody.innerHTML = '';
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${row.id}</td><td>${row.date}</td><td>${row.rainfall}</td><td>${row.river_level}</td><td>${row.flood_status}</td>`;
                tableBody.appendChild(tr);
            });
        }

        document.getElementById('add-data-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            await fetch('/add', {
                method: 'POST',
                body: new URLSearchParams(formData),
            });
            loadData();
        });

        document.getElementById('predict-btn').addEventListener('click', async () => {
            const response = await fetch('/predict');
            const predictions = await response.json();
            document.getElementById('predictions').innerText = JSON.stringify(predictions, null, 2);
        });

        loadData();
    </script>
</body>
</html>
"""

@app.route('/')
def home():
    return render_template_string(html_template)

@app.route('/data', methods=['GET'])
def get_data():
    conn = get_db_connection()
    data = conn.execute('SELECT * FROM flood_data').fetchall()
    conn.close()
    return jsonify([dict(row) for row in data])

@app.route('/add', methods=['POST'])
def add_data():
    date = request.form['date']
    rainfall = request.form['rainfall']
    river_level = request.form['river_level']
    flood_status = request.form['flood_status']

    conn = get_db_connection()
    conn.execute(
        'INSERT INTO flood_data (date, rainfall, river_level, flood_status) VALUES (?, ?, ?, ?)',
        (date, rainfall, river_level, flood_status)
    )
    conn.commit()
    conn.close()
    return jsonify({'message': 'Data added successfully!'})

@app.route('/predict', methods=['GET'])
def predict():
    conn = get_db_connection()
    data = pd.read_sql_query('SELECT * FROM flood_data', conn)
    conn.close()

    if data.empty:
        return jsonify({'error': 'No data available for prediction'})

    data['date'] = pd.to_datetime(data['date'])
    data = data.sort_values('date')

    try:
        # ARIMA Model
        arima_model = ARIMA(data['river_level'], order=(5, 1, 0))
        arima_fit = arima_model.fit()
        arima_prediction = arima_fit.forecast(steps=5)

        # Prophet Model
        prophet_data = data[['date', 'river_level']].rename(columns={'date': 'ds', 'river_level': 'y'})
        prophet_model = Prophet()
        prophet_model.fit(prophet_data)
        future = prophet_model.make_future_dataframe(periods=5)
        prophet_forecast = prophet_model.predict(future)
        prophet_predictions = prophet_forecast[['ds', 'yhat']].tail(5).to_dict(orient='records')

        predictions = {
            'arima': arima_prediction.tolist(),
            'prophet': prophet_predictions,
        }

        return jsonify({'success': True, 'predictions': predictions})

    except Exception as e:
        return jsonify({'error': str(e)})
