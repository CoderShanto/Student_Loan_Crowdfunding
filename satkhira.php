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

// Fetch historical data for Satkhira
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
          WHERE location_name = 'Satkhira' 
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
        "message" => "No data available for Satkhira."
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satkhira Flood Prediction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
            body {
            background-color: #f5f5f5; /* Light background */
            font-family: 'Arial', sans-serif;
        }

        .header {
            background-color: #007bff; /* Primary blue */
            color: #fff;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        thead {
            background-color: #007bff; /* Table header color */
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Alternate row color */
        }

        .high-severity {
            background-color: #ffcccc; /* Light red */
            color: #b30000;
        }

        .low-severity {
            background-color: #ccffcc; /* Light green */
            color: #006600;
        }

        #result {
            padding: 20px;
            margin: 20px auto;
            max-width: 500px;
            text-align: center;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .warning-high {
            background-color: #ffcccc;
            color: #b30000;
            border: 2px solid #ff0000;
        }

        .warning-low {
            background-color: #ccffcc;
            color: #006600;
            border: 2px solid #009900;
        }
        .media-section {
    margin-top: 20px;
}

.media-section h5 {
    margin-bottom: 10px;
    color: #007bff;
    font-weight: bold;
}

.image-gallery, .video-gallery {
    margin-bottom: 20px;
}

.media-item {
    position: relative;
    display: inline-block;
    margin: 10px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.media-item img,
.media-item video {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.media-item:hover img,
.media-item:hover video {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.media-item .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    opacity: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    text-align: center;
    padding: 10px;
    transition: opacity 0.3s ease;
}

.media-item:hover .overlay {
    opacity: 1;
}

.media-item .overlay p {
    font-size: 14px;
    margin-bottom: 10px;
}

.view-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

.view-btn:hover {
    background: #0056b3;
}

.caption {
    margin-top: 8px;
    font-size: 14px;
    color: #555;
    text-align: justify;
}
:root {
    --bg-color: #f5f5f5;
    --text-color: #000;
    --header-bg: #007bff;
    --header-text: #fff;
    --table-header-bg: #007bff;
    --table-header-text: #fff;
    --row-alt-bg: #f9f9f9;
    --severity-high-bg: #ffcccc;
    --severity-high-text: #b30000;
    --severity-low-bg: #ccffcc;
    --severity-low-text: #006600;
}

.dark-mode {
    --bg-color: #121212;
    --text-color: #fff;
    --header-bg: #1e88e5;
    --header-text: #ffffff;
    --table-header-bg: #333333;
    --table-header-text: #ffffff;
    --row-alt-bg: #1e1e1e;
    --severity-high-bg: #d32f2f;
    --severity-high-text: #ff8a80;
    --severity-low-bg: #388e3c;
    --severity-low-text: #a5d6a7;
}

body {
    background-color: var(--bg-color);
    color: var(--text-color);
}

.header {
    background-color: var(--header-bg);
    color: var(--header-text);
}

table thead {
    background-color: var(--table-header-bg);
    color: var(--table-header-text);
}

tbody tr:nth-child(even) {
    background-color: var(--row-alt-bg);
}

.high-severity {
    background-color: var(--severity-high-bg);
    color: var(--severity-high-text);
}

.low-severity {
    background-color: var(--severity-low-bg);
    color: var(--severity-low-text);
}

.warning-high {
    background-color: var(--severity-high-bg);
    color: var(--severity-high-text);
}

.warning-low {
    background-color: var(--severity-low-bg);
    color: var(--severity-low-text);
}

    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Feni Flood Situation</h1>
        <label class="form-check-label">
            <input type="checkbox" id="darkModeToggle" class="form-check-input">
            Enable Dark Mode
        </label>
    </div>
</div>

<div class="container">
    <h3>Flood Details for Feni</h3>
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
                <td><?php echo $data['warning_issued']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    

    <?php if ($prediction['success']): ?>
        <div id="result" class="<?php echo ($prediction['severity'] == 'High') ? 'warning-high' : 'warning-low'; ?>">
    Flood Severity Prediction: <?php echo $prediction['severity']; ?>
</div id="result">
    <?php else: ?>
    <div id="result"><?php echo $prediction['message']; ?></div>
    <?php endif; ?>
</div>


<div class="container mt-5">
    <h4>Photos and Videos</h4>
    <div class="media-section">
        <!-- Image Section -->
        <div class="image-gallery">
            <h5>Photos</h5>
            <!-- Image 1 with Caption -->
            <div class="media-item">
                <img src="image/child.jpg" alt="Image 1">
                <p class="caption"><h2>Children wading through floodwaters in a remote village, showcasing the harsh realities of the disaster.</h2></p>
            </div>
            <!-- Image 2 with Caption -->
            <div class="media-item">
                <img src="image/family2.jpg" alt="Image 2">
                <p class="caption"><h2>A family taking shelter under makeshift tarps, desperately waiting for relief supplies.</h2></p>
            </div>
            <!-- Image 3 with Caption -->
            <div class="media-item">
                <img src="image/army.jpg" alt="Image 3">
                <p class="caption"><h1>Army distributing food and clean water to flood-affected people in a relief camp.</h1></p>
            </div>
        </div>

        <!-- Video Section -->
        <div class="video-gallery">
            <h5>Videos</h5>
            <!-- Video 1 with Caption -->
            <div class="media-item">
                <video controls>
                    <source src="vedios/feni.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <p class="caption"><h2>A video showing the devastation caused by the floods, with submerged homes and stranded families.</h2></p>
            </div>
            <!-- Video 2 with Caption -->
            <div class="media-item">
                <video controls>
                    <source src="vedios/feni2.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <p class="caption"><h1>Footage of rescue operations where teams are evacuating people using boats.</h1></p>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleSwitch = document.querySelector("#darkModeToggle");
        const body = document.body;

        // Check localStorage for dark mode preference
        const darkMode = localStorage.getItem("darkMode");
        if (darkMode === "enabled") {
            body.classList.add("dark-mode");
            toggleSwitch.checked = true;
        }

        // Toggle dark mode
        toggleSwitch.addEventListener("change", function () {
            if (this.checked) {
                body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
        });
    });
</script>


</body>
</html>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Voice Assistant with KNN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .voice-assistant-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #3a5adb;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .voice-assistant-btn:hover {
            background-color: #218838;
            transform: scale(1.1);
        }

        .voice-assistant-btn:active {
            background-color: #1e7e34;
            transform: scale(1);
        }

        #assistant-popup {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 250px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        #assistant-popup input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        #assistant-popup button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #assistant-popup button:hover {
            background-color: #218838;
        }

        #assistant-popup .close-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 18px;
            cursor: pointer;
            color: #888;
        }

        .stop-btn {
            background-color: #dc3545;
            padding: 6px 16px;
            font-size: 12px;
            border-radius: 50px;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 10px;
        }

        .stop-btn:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }

        .stop-btn:active {
            background-color: #bd2130;
            transform: scale(1);
        }
    </style>
</head>
<body>
    <button class="voice-assistant-btn" onclick="toggleAssistantPopup()">🤖</button>
    <div id="assistant-popup">
        <div class="close-btn" onclick="closeAssistantPopup()">×</div>
        <h3>AI Assistant</h3>
        <input type="text" id="assistant-input" placeholder="Ask me something..." />
        <button onclick="processInput()">Ask</button>
        <button class="stop-btn" onclick="stopListening()">Stop</button>
        <div id="response"></div>
    </div>

    <script>
        let assistantPopup = document.getElementById("assistant-popup");
        let assistantInput = document.getElementById("assistant-input");
        let responseDiv = document.getElementById("response");
    
        let synth = window.speechSynthesis;
        let voices = [];
    
        function populateVoiceList() {
            voices = synth.getVoices();
            let femaleVoice = voices.find(voice => voice.name.toLowerCase().includes('female'));
            synth.voice = femaleVoice || voices[0];
        }
    
        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = populateVoiceList;
        }
    
        function toggleAssistantPopup() {
            assistantPopup.style.display = assistantPopup.style.display === "block" ? "none" : "block";
            startListening();
        }
    
        function closeAssistantPopup() {
            assistantPopup.style.display = "none";
            stopListening();
        }
    
        // Friendly conversation dataset
        const conversationDataset = [
            { input: "hello", response: "Hello! How can I assist you today?" },
            { input: "how are you", response: "I'm just a program, but I'm feeling great! How about you?" },
            { input: "what's your name", response: "I'm your friendly food delivery assistant!" },
            { input: "thank you", response: "You're welcome! Happy to help!" },
            { input: "bye", response: "Goodbye! Have a great day!" },
            // Add more friendly responses here
        ];
    
        // Commands for redirection
        const commandDataset = [
            { input: "what is the situtaion in khulna right now?", response: "Currently, Khulna is facing severe flooding, with 370.60 mm of rainfall and a river flow rate of 1500.45 cubic meters per second. The photos and videos on this page vividly capture the devastating impact..", url: "khulna.php" },
            { input: "show me the situtaion in feni", response: "As of now, Feni is experiencing severe flooding, with rainfall of 360.80 mm and a river flow rate of 1300.80 cubic meters per second. The photos and videos on this page capture the impact vividly.", url: "satkhira.php" },
            { input: "go to my profile page", response: "Sure sir, Redirecting to your profile page.", url: "Profile_page.html" },
            { input: "show my orders", response: "Taking you to your orders page.", url: "users_area/profile.php?my_orders" },
            { input: "do you have mango? show me some variety if you have", response: "Absolutely! We're stocked with a variety of delicious mangoes. You can find Alphonso, Kesar, and Totapuri mangoes in our inventory.", url: "products_details.php?product_id=50" },
            { input: "i want orange, do you have? show me some variety if you have", response: "Indeed, we have oranges! We offer several varieties, including Valencia, Navel, and Blood oranges. You can learn more about each type here", url: "products_details.php?product_id=49" },
            { input: "we need egg", response: "Unfortunately, we don't have any eggs in stock at this time. However, we will be restocking soon and will let you know when they are back in stock", url: "sorry.html" },
            { input: "let me see the cart", response: "sure sir,here is your cart page", url: "cart.php" },
        ];
    
        // Main function to process input
        function processInput() {
            let query = assistantInput.value.toLowerCase().trim();
            if (query) {
                let command = knnClassifier(query, [...conversationDataset, ...commandDataset]);
                if (command.url) {
                    speakAnswer(command.response);
                    redirectToPage(command.url);
                } else {
                    speakAnswer(command.response);
                }
                assistantInput.value = "";
            } else {
                responseDiv.innerText = "Please ask something!";
            }
        }
    
        // KNN Algorithm for classification
        function knnClassifier(input, dataset) {
            const inputVector = input.split(" ");
            let closestDistance = Infinity;
            let predictedCommand = { response: "I don't understand the command." };
    
            dataset.forEach(item => {
                let commandVector = item.input.split(" ");
                let distance = cosineSimilarity(inputVector, commandVector);
                if (distance < closestDistance) {
                    closestDistance = distance;
                    predictedCommand = item;
                }
            });
    
            return predictedCommand;
        }
    
        function cosineSimilarity(vecA, vecB) {
            let intersection = 0;
            let normA = 0;
            let normB = 0;
            vecA.forEach(wordA => {
                let countA = vecA.filter(x => x === wordA).length;
                normA += Math.pow(countA, 2);
                if (vecB.includes(wordA)) {
                    intersection += countA * vecB.filter(x => x === wordA).length;
                }
            });
            vecB.forEach(wordB => {
                let countB = vecB.filter(x => x === wordB).length;
                normB += Math.pow(countB, 2);
            });
            return 1 - (intersection / (Math.sqrt(normA) * Math.sqrt(normB)));
        }
    
        function redirectToPage(url) {
            responseDiv.innerText = `Redirecting to ${url}`;
            window.location.href = url;
        }
    
        function speakAnswer(answer) {
            let speech = new SpeechSynthesisUtterance();
            speech.text = answer;
            synth.speak(speech);
        }
    
        let recognition;
        let isListening = false;
    
        function startListening() {
            if (!("webkitSpeechRecognition" in window)) {
                alert("Sorry, your browser doesn't support speech recognition.");
                return;
            }
    
            recognition = new webkitSpeechRecognition();
            recognition.lang = "en-US";
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;
    
            recognition.onstart = function () {
                isListening = true;
                responseDiv.innerText = "Listening...";
            };
    
            recognition.onresult = function (event) {
                let result = event.results[0][0].transcript.toLowerCase();
                assistantInput.value = result;
                processInput();
            };
    
            recognition.onerror = function (event) {
                responseDiv.innerText = "Error occurred: " + event.error;
            };
    
            recognition.start();
        }
    
        function stopListening() {
            if (recognition && isListening) {
                recognition.stop();
                isListening = false;
                responseDiv.innerText = "Stopped listening.";
            }
            if (synth.speaking) {
                synth.cancel();
            }
        }
    </script>
    
</body>
</html>
