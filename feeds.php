<?php
// Connect to the database
$host = "localhost";
$username = "root";
$password = "";
$database = "student-loan"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted, insert new post into the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = 1; // Replace this with the actual logged-in user ID if available
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $media_path = '';

    // Handle file upload if media is provided
    if (!empty($_FILES['media']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["media"]["name"]);
        if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            $media_path = $target_file;
        } else {
            echo "Error uploading media.";
        }
    }

    // Insert post into the database
    $sql = "INSERT INTO posts (user_id, title, content, media_path) VALUES ('$user_id', '$title', '$content', '$media_path')";
    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }
}

// Fetch all posts from the database
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Page</title>
    <style>
       body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #eef2f5;
    color: #333;
}

h1 {
    text-align: center;
    margin-bottom: 40px;
    color: #ffffff;
    font-size: 3em;
    text-transform: uppercase;
    letter-spacing: 4px;
    font-weight: bold;
    padding: 20px;
    background: linear-gradient(135deg, #4CAF50, #45a049);
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

h1:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
}

.container {
    max-width: 900px;
    margin: 30px auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
    background-color: #f9f9f9;
    transition: 0.3s;
}

input[type="text"]:focus,
textarea:focus {
    border-color: #4CAF50;
    background-color: #fff;
    outline: none;
}

textarea {
    height: 120px;
    resize: none;
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 12px 25px;
    font-size: 1em;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
}

button:hover {
    background-color: #45a049;
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.3);
}

.post-container {
    margin-top: 20px;
    padding: 20px;
    border-radius: 10px;
    background-color: #ffffff;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.post-title {
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.post-content {
    font-size: 1em;
    margin-bottom: 15px;
    line-height: 1.6;
}

.post-media img {
    max-width: 100%;
    height: auto;
    margin-top: 15px;
    border-radius: 5px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

.post-media video {
    max-width: 100%;
    border-radius: 5px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

.post-date {
    color: #666;
    font-size: 0.9em;
    font-style: italic;
    margin-top: 10px;
    display: block;
}

textarea::placeholder,
input::placeholder {
    color: #aaa;
}

hr {
    margin: 20px 0;
    border: none;
    border-top: 2px solid #ddd;
}

footer {
    text-align: center;
    margin-top: 30px;
    font-size: 0.9em;
    color: #777;
    padding: 10px 0;
    background-color: #fff;
    box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px 10px 0 0;
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 2.5em;
    }

    button {
        width: 100%;
        padding: 15px;
    }
}
.action-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 15px;
}

.action-buttons form {
    margin: 0;
}

.action-buttons button {
    background-color: #4CAF50;
    color: white;
    padding: 12px 25px;
    font-size: 1em;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    width: 120px; /* Optional, adjust the size of the buttons */
}

.action-buttons button:hover {
    background-color: #45a049;
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.3);
}

.action-buttons button:focus {
    outline: none;
}
/* daynamically */
.post-container {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.post-title {
    font-size: 18px;
    font-weight: bold;
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.post-title img {
    margin-right: 10px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
}

.post-content {
    font-size: 16px;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.5;
}

.post-media img {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 15px;
}

.post-media video {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 15px;
}

.post-date {
    font-size: 14px;
    color: #777;
    display: block;
    margin-bottom: 15px;
}

.action-buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.action-buttons button {
    background-color: #007BFF;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.action-buttons button:hover {
    background-color: #0056b3;
}

.action-buttons form {
    display: inline;
}
video {
            width: 600px; /* Set desired width */
            height: auto; /* Maintain aspect ratio */
            max-width: 100%; /* Make video responsive */
            border: 2px solid #333; /* Optional: Add a border for styling */
            border-radius: 10px; /* Optional: Rounded corners */
        }
        .toggle-container {
      display: flex;
      justify-content: flex-end;
      padding: 10px 20px;
      background-color: #f5f5f5;
      border-bottom: 1px solid #ddd;
    }

    .toggle-button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .toggle-button:hover {
      background-color: #2980b9;
    }
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #eef2f5;
      color: #333;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    body.dark-mode {
      background-color: #333;
      color: #eef2f5;
    }
    .txt1{
        body-style: italic;
    }
     /* Default styles for the title container */
     .title-container {
        text-align: center; /* Centers the title */
        padding: 20px;
        margin: 0;
    }

    /* Default color for light mode */
    .title-container h2 {
        font-size: 2rem;
        font-weight: bold;
        color: #000; /* Black text for light mode */
        text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
        transition: color 0.3s ease, text-shadow 0.3s ease; /* Smooth transition */
    }

    /* Dark mode styles */
    @media (prefers-color-scheme: dark) {
        .title-container h2 {
            color: #fff; /* White text for dark mode */
            text-shadow: 2px 2px 6px rgba(255, 255, 255, 0.3); /* Lighter shadow for dark mode */
        }
    }

    /* Adding a glow effect to make it more attractive */
    .title-container h2 {
        background-image: linear-gradient(to left, #f7b42c, #fc4a1a); /* Gradient background */
        -webkit-background-clip: text; /* Clip background to text */
        color: transparent; /* Make text color transparent so gradient shows */
        font-size: 2.5rem; /* Larger font size */
        font-weight: 700; /* Bold weight */
        text-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1), 0px 0px 10px rgba(0, 0, 0, 0.2); /* Shadow for glow effect */
    }
    </style>
</head>
<body>

    <h1>Post Something</h1>
    <div class="toggle-container">
    <button id="toggle-button" class="toggle-button">Dark Mode</button>
  </div>

    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea name="content" id="content" required></textarea>
            </div>

            <div class="form-group">
                <label for="media">Upload Media (optional):</label>
                <input type="file" name="media" id="media" accept="image/*,video/*">
            </div>

            <button type="submit">Post</button>
        </form>

        <div class="title-container">
    <h2>All Posts</h2>
</div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post-container">
                <div class="post-title">
    <img src="image/1.jpg" alt="Profile Photo" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; vertical-align: middle;">
    <?= htmlspecialchars($row['title']); ?>
</div>

                   
                    <div class="post-content"><?= nl2br(htmlspecialchars($row['content'])); ?></div>

                    <?php if (!empty($row['media_path'])): ?>
                        <div class="post-media">
                            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $row['media_path'])): ?>
                                <img src="<?= htmlspecialchars($row['media_path']); ?>" alt="Post Media">
                            <?php else: ?>
                                <video controls>
                                    <source src="<?= htmlspecialchars($row['media_path']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <span class="post-date"><?= date('F d, Y h:i A', strtotime($row['created_at'])); ?></span>

                    <!-- Action buttons -->
                    <div class="action-buttons">
                        <form action="borrows.html" method="GET">
                            <button type="submit" name="post_id" value="<?= $row['id']; ?>">Borrow</button>
                        </form>
                        <form action="lend.html" method="GET">
                            <button type="submit" name="post_id" value="<?= $row['id']; ?>">Lend</button>
                        </form>
                       
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts yet. Be the first to post something!</p>
        <?php endif; ?>

        
<!-- Dynamically Designed Post 1 -->


<!-- Dynamically Designed Post 2 -->
<div class="post-container">
    <div class="post-title">
        <img src="image/jamil.jpg" alt="Profile Photo" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; vertical-align: middle;">
        Only 5% Interest? Let’s Make It Happen!<strong><h1 class="txt1">Mahmud Hasan Shanto</h1></strong>
    </div>
    <div class="post-content">
    Hey there, buddy! You know I trust you, right? So here’s the deal: I’ll give you the loan you need, and all I ask is just 5% interest.

No strings attached, no big formalities – it’s all about helping you achieve your goals. So, let’s shake on it, and you can get started right away. Deal? 😊


    </div>
    <div class="post-media">
        
    </div>
    <span class="post-date">January 07, 2025 01:15 PM</span>
    <div class="action-buttons">
        <form action="borrows.html" method="GET">
            <button type="submit">Borrow</button>
        </form>
        <form action="lend.html" method="GET">
            <button type="submit">Lend</button>
        </form>
      
    </div>
</div>

<!-- Add more dynamically styled posts as needed -->
<div class="post-container">
    <div class="post-title">
        <img src="image/cosmic.jpg" alt="Profile Photo" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px; vertical-align: middle;">
        We’ve Got This – 5.5% Interest for You.<strong><h1 class="txt1">Mahmud Hasan Shanto</h1></strong>
    </div>
    <div class="post-content">
    Friendship means trust, and I know we’ve got that. So here’s the deal: I’ll help you out with a loan at only 5.5% interest.

No worries, no complications – just a fair and simple way to support each other. Let’s make it work. What do you say?
    </div>
    
    <span class="post-date">January 08, 2025 02:30 PM</span>
    <div class="action-buttons">
        <form action="borrows.html" method="GET">
            <button type="submit">Borrow</button>
        </form>
        <form action="lend.html" method="GET">
            <button type="submit">Lend</button>
        </form>
       
    </div>
</div>

<!--another one -->


<!--another one -->









</div>

<script>
    const toggleButton = document.getElementById('toggle-button');
    const body = document.body;

    toggleButton.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      if (body.classList.contains('dark-mode')) {
        toggleButton.textContent = 'Light Mode';
      } else {
        toggleButton.textContent = 'Dark Mode';
      }
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
            { input: "what is the situtaion in khulna right now?", response: " ", url: "khulna.php" },
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
