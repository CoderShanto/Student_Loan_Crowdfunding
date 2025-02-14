<?php
// Database connection settings
$host = 'localhost';
$db = 'student-loan';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $db);

// Check the database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch historical data for Cox's Bazar
$query = "SELECT 
            flood_date, 
            rainfall_mm, 
            river_flow_rate, 
            river_water_level, 
            temperature_celsius, 
            humidity_percentage, 
            wind_speed_kmph, 
            soil_moisture, 
            
            damage_cost_estimate, 
            evacuations, 
            deaths, 
            affected_area_sqkm, 
            warning_issued 
          FROM flood_data 
          WHERE location_name = 'Cox\'s Bazar'
          ORDER BY flood_date DESC";

$result = $conn->query($query);

$floodData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $floodData[] = $row;
    }
}

$conn->close();

// Random Forest Algorithm
function randomForestPredict($data, $userInput, $numTrees = 10) {
    // Generate decision trees and collect predictions
    $treePredictions = [];
    for ($i = 0; $i < $numTrees; $i++) {
        // Sample data with replacement for each tree (Bootstrap Aggregating)
        $sampleData = array_map(function() use ($data) {
            return $data[array_rand($data)];
        }, $data);

        // Build a simple decision tree model based on this sample data (simple prediction rule)
        $prediction = decisionTreePredict($sampleData, $userInput);
        $treePredictions[] = $prediction;
    }

    // Majority voting for final prediction (for classification)
    $predictionCounts = array_count_values($treePredictions);
    arsort($predictionCounts);  // Sort by frequency, descending
    return array_key_first($predictionCounts);  // Return the most frequent prediction
}

function decisionTreePredict($sampleData, $userInput) {
    // A very basic decision tree rule for flood severity prediction (simplified)
    $rainfallThreshold = 100; // Example threshold for rainfall
    $flowThreshold = 500; // Example threshold for river flow rate

    // Decision tree logic (just a basic example for illustration)
    if ($userInput['rainfall_mm'] > $rainfallThreshold && $userInput['river_flow_rate'] > $flowThreshold) {
        return 'High'; // Severe flood
    } else {
        return 'Low'; // No severe flood
    }
}

// Example input rainfall (user-provided or for testing)
$userRainfall = 120; // Example input in mm
$userRiverFlow = 600; // Example river flow rate (in cubic meters per second)
$userWaterLevel = 15; // Example water level (in meters)
$userTemperature = 30; // Example temperature in °C
$userHumidity = 85; // Example humidity percentage
$userWindSpeed = 50; // Example wind speed in km/h
$userSoilMoisture = 45; // Example soil moisture percentage
$userAffectedArea = 100; // Example affected area in sq km

$userData = [
    'rainfall_mm' => $userRainfall,
    'river_flow_rate' => $userRiverFlow,
    'river_water_level' => $userWaterLevel,
    'temperature_celsius' => $userTemperature,
    'humidity_percentage' => $userHumidity,
    'wind_speed_kmph' => $userWindSpeed,
    'soil_moisture' => $userSoilMoisture,
    'affected_area_sqkm' => $userAffectedArea
];

// Predict flood severity using Random Forest
if (!empty($floodData)) {
    $predictedSeverity = randomForestPredict($floodData, $userData);
    $prediction = [
        "success" => true,
        "rainfall" => $userRainfall,
        "severity" => $predictedSeverity
    ];
} else {
    $prediction = [
        "success" => false,
        "message" => "No data available for Cox's Bazar."
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cox's Bazar Flood Prediction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
    background-color: #f7f9fc;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.container {
    margin-top: 50px;
    padding: 20px;
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
    border: none;
    padding: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: scale(1.05); /* Slightly enlarges the card */
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2); /* Larger shadow on hover */
}

.card-header {
    background-color: #007bff;
    color: #fff;
    font-size: 1.5rem;
    padding: 20px;
    border-radius: 8px 8px 0 0;
    text-align: center;
    transition: background-color 0.3s ease;
}

.card-header:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.card-body {
    font-size: 1rem;
}

h3 {
    color: #007bff;
    font-weight: 600;
    text-align: center;
    margin-top: 20px;
    transition: color 0.3s ease;
}

h3:hover {
    color: #0056b3; /* Darker blue on hover */
}

.form-group label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #555;
    margin-bottom: 10px;
}

.form-control {
    border-radius: 8px;
    padding: 12px 15px;
    border: 1px solid #ccc;
    font-size: 1rem;
    margin-bottom: 20px;
    width: 100%;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
}

.form-control:hover {
    border-color: #0056b3; /* Darker border on hover */
}

.btn-primary {
    background-color: #007bff;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 1.1rem;
    width: 100%;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: scale(1.05); /* Button enlarges on hover */
    cursor: pointer;
}

.card-footer {
    text-align: center;
    margin-top: 20px;
}

#result {
    margin-top: 20px;
    text-align: center;
    font-size: 1.2rem;
    font-weight: 600;
    color: #007bff;
}

#result:hover {
    color: #0056b3; /* Darker blue on hover */
}

@media (max-width: 768px) {
    .container {
        margin-top: 30px;
        padding: 15px;
    }

    .card {
        padding: 20px;
    }

    .form-control {
        font-size: 0.9rem;
    }

    .btn-primary {
        font-size: 1rem;
    }
}

    </style>
</head>
<body>
<div class="container">
    <h3>Flood Details for Cox's Bazar</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Rainfall (mm)</th>
                <th>River Flow Rate</th>
                <th>Water Level</th>
                <th>Temperature (°C)</th>
                <th>Humidity (%)</th>
                <th>Wind Speed (km/h)</th>
                <th>Soil Moisture</th>
                <th>Severity</th>
                <th>Damage Cost</th>
                <th>Evacuations</th>
                <th>Deaths</th>
                <th>Affected Area (sq km)</th>
                <th>Warning Issued</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($floodData as $data): ?>
            <tr>
                <td><?php echo $data['flood_date']; ?></td>
                <td><?php echo $data['rainfall_mm']; ?></td>
                <td><?php echo $data['river_flow_rate']; ?></td>
                <td><?php echo $data['river_water_level']; ?></td>
                <td><?php echo $data['temperature_celsius']; ?></td>
                <td><?php echo $data['humidity_percentage']; ?></td>
                <td><?php echo $data['wind_speed_kmph']; ?></td>
                <td><?php echo $data['soil_moisture']; ?></td>
               
                <td><?php echo $data['damage_cost_estimate']; ?></td>
                <td><?php echo $data['evacuations']; ?></td>
                <td><?php echo $data['deaths']; ?></td>
                <td><?php echo $data['affected_area_sqkm']; ?></td>
                <td><?php echo $data['warning_issued'] ? 'Yes' : 'No'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="result" class="alert alert-info">
        <?php
            if ($prediction['success']) {
                echo "Predicted Flood Severity: " . $prediction['severity'] . " for Rainfall: " . $prediction['rainfall'] . " mm.";
            } else {
                echo $prediction['message'];
            }
        ?>
    </div>
</div>
</body>
</html>
