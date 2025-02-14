<?php
// signup.php

include 'db_connection.php'; // Include the database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $username = $_POST['username'];

    try {
        // Check if email already exists in the database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $error = "Email already registered. Please login.";
        } else {
            // Insert the new user into the database
            $sql = "INSERT INTO users (email, password, username) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email, $password, $username]);
            header("Location: login.php?status=signup_success"); // Redirect to login page after signup
            exit();
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Student Loan Funding</title>
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Sign up container */
        .signup-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Heading */
        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Error message */
        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Form styles */
        .signup-form {
            display: flex;
            flex-direction: column;
        }

        .signup-form input {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .signup-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .signup-form button:hover {
            background-color: #45a049;
        }

        /* Login prompt */
        .login-prompt {
            margin-top: 20px;
            font-size: 14px;
        }

        .login-prompt a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-prompt a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Student Loan Funding Sign Up</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form method="POST" class="signup-form">
    <input type="text" name="username" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Enter your email" required pattern="[A-Za-z0-9._+\-\']+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$" title="Please enter a valid email address."><br>
    <input type="password" name="password" placeholder="Enter your password" required><br>
    <button type="submit">Sign Up</button>
</form>


        <p class="login-prompt">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
