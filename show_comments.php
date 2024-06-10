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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['pitch_id'])) {
    $pitch_id = $_GET['pitch_id'];

    // Retrieve comments from database
    $stmt = $conn->prepare("SELECT * FROM comments WHERE pitch_id = ?");
    if ($stmt === false) {
        die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
    }

    $stmt->bind_param("i", $pitch_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $comments = [];

        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode(['success' => true, 'comments' => $comments]);
    } else {
        echo json_encode(['error' => 'Failed to retrieve comments: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method or missing pitch_id parameter.']);
}

$conn->close();
?>
