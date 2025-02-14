<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'student-loan';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['postTitle']);
    $content = $conn->real_escape_string($_POST['postContent']);
    $media_path = null;

    // Handle file upload
    if (isset($_FILES['postMedia']) && $_FILES['postMedia']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        
        // Check if the uploads directory exists, if not create it
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);  // Creates the directory with proper permissions
        }

        // Sanitize the file name
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\-_\.]/", "", basename($_FILES['postMedia']['name']));
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['postMedia']['tmp_name'], $target_path)) {
            $media_path = $target_path;
        } else {
            die("Error uploading media file.");
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO posts (user_id, title, content, media_path) VALUES (1, '$title', '$content', '$media_path')";
    if ($conn->query($sql) === TRUE) {
        echo "Post created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
