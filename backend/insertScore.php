<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validate and use the score
    if (isset($data['score']) && isset($_SESSION['username'])) {
        $_SESSION['score'] = $data['score'];

        $db = new mysqli("localhost", "pixeladmin", "changeme", "pixelrun");

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        $username = $_SESSION['username'];
        $score = $_SESSION['score'];

        // Check if the user already exists
        $stmt = $db->prepare("SELECT score FROM scores WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 0) {
            // User doesn't exist, insert a new score
            $stmt = $db->prepare("INSERT INTO scores (username, score) VALUES (?, ?)");
            $stmt->bind_param("si", $username, $score);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to insert score']);
            }
            $stmt->close();
        } else {
            // User exists, check if new score is higher
            $prevScoreData = $result->fetch_assoc();
            if ($score > $prevScoreData['score']) {
                // Update the score if the new score is higher
                $stmt = $db->prepare("UPDATE scores SET score = ? WHERE username = ?");
                $stmt->bind_param("is", $score, $username);

                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update score']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Score not updated. Current score is higher.']);
            }
        }

        $db->close();

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Score or username not provided']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
