<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
// $username = "root";
// $password = "";
$username = "skodr_pitchapitest";
$password = "skodr_pitchapitest";
$dbname = "skodr_pitchapitest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pitch_id = $_POST['pitch_id'];
    $name = $_POST['name'];
    $comment_text = $_POST['comment'];

    // Insert comment into database
    $stmt = $conn->prepare("INSERT INTO comments (pitch_id, name, comment) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
    }

    $stmt->bind_param("iss", $pitch_id, $name, $comment_text);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Comment added successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to add comment: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>
