<?php
// Database connection settings
$host = 'localhost';
$db = 'student-loan';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $db);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

if (isset($_GET['location'])) {
    $location = $_GET['location'];

    // Fetch historical data from the database
    $query = "SELECT flood_date, rainfall_mm FROM flood_data WHERE location_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $location);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Save the data into a CSV file for Python to process
        $csvFile = 'prophet_data.csv';
        $handle = fopen($csvFile, 'w');
        fputcsv($handle, ['ds', 'y']); // Prophet expects 'ds' (date) and 'y' (value)

        foreach ($data as $entry) {
            fputcsv($handle, [$entry['flood_date'], $entry['rainfall_mm']]);
        }
        fclose($handle);

        // Run Python script for prediction
        $output = shell_exec("python3 predict.py $csvFile");
        $prediction = json_decode($output, true);

        if ($prediction) {
            echo json_encode([
                "success" => true,
                "prediction_date" => $prediction['date'],
                "severity" => $prediction['severity']
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to generate prediction."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No data available for the selected location."]);
    }
} 

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Prediction System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f7f9fc;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: none;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        #result {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary">Flood Prediction System</h1>
        <p class="text-center">Check future flood threats based on historical data.</p>

        <!-- Prediction Input Form -->
        <div class="card p-4">
    <form id="floodForm">
        <div class="mb-3">
            <label for="location" class="form-label">Select Location:</label>
            <select id="location" class="form-select" required>
                <option value="" disabled selected>Select a location</option>
                <option value="Cox's Bazar">Cox's Bazar</option>
                <option value="Khulna">Khulna</option>
                <option value="Barisal">Barisal</option>
                <option value="Patuakhali">Patuakhali</option>
                <option value="Satkhira">Satkhira</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary w-100" onclick="checkPrediction()">Check Prediction</button>
    </form>
</div>

        <!-- Prediction Results -->
        <div id="result" class="text-center">
            <h3 id="resultTitle" class="text-success"></h3>
            <p id="resultDetails" class="text-muted"></p>
        </div>
    </div>

    <script>
       function checkPrediction() {
    const location = document.getElementById("location").value;
    if (!location) {
        alert("Please select a location!");
        return;
    }

    fetch(`flood_predict.php?location=${location}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const resultTitle = document.getElementById("resultTitle");
            const resultDetails = document.getElementById("resultDetails");

            if (data.success) {
                resultTitle.innerText = `Prediction for ${location}`;
                resultDetails.innerText = `The next potential flood is predicted on ${data.prediction_date}. Severity: ${data.severity}.`;
            } else {
                resultTitle.innerText = `No Prediction Available`;
                resultDetails.innerText = data.message;
            }
        })
        .catch(err => {
            alert("An error occurred while fetching the data. Please check the console for details.");
            console.error("Fetch error:", err);
        });
}  function checkPrediction() {
        // Get the selected location
        var location = document.getElementById("location").value;

        // Check if a location is selected
        if (location) {
            // Redirect based on the selected location
            if (location === "Khulna") {
                window.location.href = "khulna.php";  // Replace with actual URL for Khulna
            } else if (location === "Barisal") {
                window.location.href = "barisalPredictionPage.html";  // Replace with actual URL for Barisal
            } else {
                window.location.href = "otherLocationPredictionPage.html";  // Replace with actual URL for other locations
            }
        } else {
            alert("Please select a location first.");
        }
    }

    </script>
</body>
</html>
