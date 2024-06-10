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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM pitch";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $pitches = [];

        while ($row = $result->fetch_assoc()) {
            $pitches[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $pitches]);
    } else {
        echo json_encode(['success' => true, 'data' => []]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>
