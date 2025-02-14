<?php
// PHP Code for Logging Failed Attempts with Mock Locations

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'student-loan');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Decode received JSON data
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if verification failed
    if (isset($data['verificationCode']) && $data['verificationCode'] !== $data['generatedCode']) {
        // Get the user's IP address
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
    
        // Convert ::1 to 127.0.0.1 for localhost testing
        if ($ipAddress === '::1') {
            $ipAddress = '127.0.0.1';
        }

        // Mock locations array
        $locations = [
            "Dhaka, Bangladesh",
            "Chittagong, Bangladesh",
            "Sylhet, Bangladesh",
            "Khulna, Bangladesh",
            "Rajshahi, Bangladesh",
            "Barisal, Bangladesh",
            "Rangpur, Bangladesh",
            "Comilla, Bangladesh"
        ];

        // Randomly select a location
        $location = $locations[array_rand($locations)];

        // Sanitize inputs
        $studentId = $conn->real_escape_string($data['studentId']);
        $email = $conn->real_escape_string($data['email']);
        $verificationCode = $conn->real_escape_string($data['verificationCode']);
        $captcha = $conn->real_escape_string($data['captcha']);
        $timestamp = date('Y-m-d H:i:s');

        // Insert failed attempt with mock location into the database
        $sql = "INSERT INTO failed_verifications (student_id, email, verification_code, captcha, ip_address, timestamp, location) 
                VALUES ('$studentId', '$email', '$verificationCode', '$captcha', '$ipAddress', '$timestamp', '$location')";

        if ($conn->query($sql) === TRUE) {
            echo "Record inserted successfully with mock location.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIU Fraud Detection System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: #002855;
            color: #ffffff;
            text-align: center;
            padding: 20px 10px;
        }

        .header .logo {
            width: 80px;
            margin-bottom: 10px;
        }

        .verification-form {
            padding: 20px;
        }

        .verification-form h2 {
            margin-bottom: 20px;
            color: #002855;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .captcha-image {
            display: block;
            margin: 10px 0;
            max-width: 800px;
            height: 100px;
            width: 200px;
        }

        .btn-primary, .btn-secondary {
            display: inline-block;
            background: #002855;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s;
        }

        .btn-primary:hover, .btn-secondary:hover {
            background: #004080;
        }

        #verification-result {
            padding: 20px;
            text-align: center;
            color: #002855;
        }

        #verification-result #status-text {
            color: green;
            font-weight: bold;
        }

        .btn-chat {
            background-color: #28a745;
            display: inline-block;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
        }

        .btn-chat:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="image/borrow.png" alt="UIU Logo" class="logo">
            <h1>Fraud Detection Security System</h1>
        </div>
        <form id="fraud-check-form" class="verification-form" method="POST" action="">
            <h2>Verify Your Identity</h2>

            <div class="form-group">
                <label for="student-id">Student ID:</label>
                <input type="text" id="student-id" name="student-id" placeholder="e.g., 011223344" required>
            </div>

            <div class="form-group">
                <label for="uiu-email">UIU Email Address:</label>
                <input type="email" id="uiu-email" name="uiu-email" placeholder="e.g., email@bscse.uiu.ac.bd" required>
                <button type="button" class="btn-secondary" id="send-code-btn">Send Code</button>
            </div>

            <div class="form-group">
                <label for="verification-code">Verification Code:</label>
                <input type="password" id="verification-code" name="verification-code" placeholder="Enter Code Sent to Email" required>
            </div>

            <div class="form-group">
                <label for="captcha">CAPTCHA:</label>
                <img src="image/captcha.jpg" alt="CAPTCHA Image" class="captcha-image">
                <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primary">Verify Identity</button>
            </div>
        </form>

        <div id="verification-result" style="display: none;">
            <h2>Verification Result</h2>
            <p><strong>Status:</strong> <span id="status-text">Verification Successful</span></p>
            <button class="btn-chat" id="chat-button" style="display: none;" onclick="redirectToChatPage()">Go to Chat Page</button>
        </div>
    </div>

    <script>
        let generatedCode = null;

        // Send verification code
        document.getElementById("send-code-btn").addEventListener("click", function () {
            const email = document.getElementById("uiu-email").value;
            if (!email.endsWith("@bscse.uiu.ac.bd")) {
                alert("Please enter a valid UIU email address.");
                return;
            }
            generatedCode = Math.floor(100000 + Math.random() * 900000).toString();
            alert(`Verification code sent to ${email}: ${generatedCode}`);
        });

        // Handle form submission
        document.getElementById("fraud-check-form").addEventListener("submit", async function (e) {
            e.preventDefault();

            const studentId = document.getElementById("student-id").value;
            const email = document.getElementById("uiu-email").value;
            const verificationCode = document.getElementById("verification-code").value;
            const captcha = document.getElementById("captcha").value;
            const captchaValue = "12345";

            if (studentId.length !== 9 || isNaN(studentId)) {
                alert("Student ID must be exactly 9 digits.");
                return;
            }

            if (!generatedCode) {
                alert("Please request a verification code first.");
                return;
            }

            const ipAddress = await fetch('https://api.ipify.org?format=json')
                .then(response => response.json())
                .then(data => data.ip)
                .catch(() => "Unknown IP");

            if (verificationCode === generatedCode && captcha === captchaValue) {
                document.getElementById("verification-result").style.display = "block";
                document.getElementById("status-text").innerText = "Verification Successful";
                document.getElementById("chat-button").style.display = "inline-block";
            } else {
                alert("Verification failed. For security, this attempt has been documented in our system");

                const logData = {
                    studentId,
                    email,
                    verificationCode,
                    captcha,
                    ipAddress,
                    timestamp: new Date().toISOString()
                };

                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(logData)
                }).then(response => {
                    if (response.ok) {
                        console.log("Failure logged successfully.");
                    } else {
                        console.error("Failed to log failure.");
                    }
                });
            }
        });

        function redirectToChatPage() {
            window.location.href = "chatting.html";
        }
    </script>
</body>
</html>
