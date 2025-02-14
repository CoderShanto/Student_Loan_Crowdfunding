import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, classification_report
import numpy as np

# Sample historical flood data
# You can replace this with your actual data (CSV, Database, etc.)
data = {
    'rainfall_mm': [120, 150, 80, 200, 300, 50, 110, 180, 130, 90],
    'river_flow_rate': [600, 750, 500, 900, 1100, 450, 620, 880, 700, 550],
    'river_water_level': [15, 20, 10, 25, 30, 8, 18, 22, 16, 12],
    'temperature_celsius': [30, 32, 28, 35, 40, 25, 33, 36, 31, 27],
    'humidity_percentage': [85, 80, 75, 90, 95, 60, 82, 88, 84, 78],
    'wind_speed_kmph': [50, 60, 40, 80, 90, 30, 55, 70, 65, 50],
    'soil_moisture': [45, 50, 40, 60, 70, 35, 48, 55, 50, 42],
    'flood_severity': ['High', 'High', 'Low', 'High', 'High', 'Low', 'Low', 'High', 'Low', 'Low']
}

# Create DataFrame
df = pd.DataFrame(data)

# Feature columns and target variable
X = df.drop('flood_severity', axis=1)  # Features
y = df['flood_severity']  # Target (Flood severity)

# Encode target variable (Flood severity) to numeric values (0: Low, 1: High)
y = y.map({'Low': 0, 'High': 1})

# Split the data into training and testing sets (80% train, 20% test)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train a Random Forest Classifier
rf_model = RandomForestClassifier(n_estimators=100, random_state=42)
rf_model.fit(X_train, y_train)

# Predict on the test set
y_pred = rf_model.predict(X_test)

# Evaluate the model
accuracy = accuracy_score(y_test, y_pred)
report = classification_report(y_test, y_pred)

print("Model Accuracy: ", accuracy)
print("Classification Report:")
print(report)

# Making predictions on new data (for example, a new weather scenario)
new_data = {
    'rainfall_mm': [150],
    'river_flow_rate': [700],
    'river_water_level': [18],
    'temperature_celsius': [33],
    'humidity_percentage': [85],
    'wind_speed_kmph': [60],
    'soil_moisture': [50]
}

# Convert the new data into a DataFrame
new_df = pd.DataFrame(new_data)

# Predict flood severity for the new data
new_prediction = rf_model.predict(new_df)

# Convert prediction back to flood severity ('Low' or 'High')
flood_severity = 'High' if new_prediction[0] == 1 else 'Low'
print(f"The predicted flood severity for the new data is: {flood_severity}")
