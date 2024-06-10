<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
// $username = "root";
// $password = "";
$username = "raisahmed_apitest";
$password = "raisahmed_apitest";
$dbname = "apitest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $title = $_POST['title'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Handle file upload
    if (isset($_FILES['video']) && $_FILES['video']['error'] == UPLOAD_ERR_OK) {
        $videoTmpPath = $_FILES['video']['tmp_name'];
        $videoName = $_FILES['video']['name'];
        $videoSize = $_FILES['video']['size'];
        $videoType = $_FILES['video']['type'];
        $videoExtension = pathinfo($videoName, PATHINFO_EXTENSION);
        $allowedExtensions = ['mp4', 'avi', 'mov'];

        if (!in_array($videoExtension, $allowedExtensions)) {
            die(json_encode(['error' => 'Invalid file type.']));
        }

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO pitch (name, phone, title, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $name, $phone, $title, $rating, $comment);
        if ($stmt->execute()) {
            $lastInsertId = $stmt->insert_id;
            $stmt->close();

            // Define the target file path
            $targetDir = "videos/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $targetFilePath = $targetDir . $lastInsertId . '.' . $videoExtension;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($videoTmpPath, $targetFilePath)) {
                // Update the video file path in the database
                $stmt = $conn->prepare("UPDATE pitch SET video = ? WHERE id = ?");
                $stmt->bind_param("si", $targetFilePath, $lastInsertId);
                if ($stmt->execute()) {
                    echo json_encode(['success' => 'Data and video file uploaded successfully.']);
                } else {
                    echo json_encode(['error' => 'Failed to update video path in database.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['error' => 'Failed to move uploaded file.']);
            }
        } else {
            echo json_encode(['error' => 'Failed to insert data into database.']);
        }
    } else {
        echo json_encode(['error' => 'No video file uploaded or file upload error.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>
