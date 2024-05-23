<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$logged_in_username = $_SESSION["username"];

if (isset($_GET['username'])) {
    $contact_username = $_GET['username'];
    
    $last_update_time = isset($_GET['last_update_time']) ? $_GET['last_update_time'] : 0;
    $formatted_time = date('Y-m-d H:i:s', $last_update_time / 1000); // Convert milliseconds to timestamp

    $sql = "SELECT * FROM messages 
            WHERE ((sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)) 
            AND timestamp > ? 
            ORDER BY timestamp";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $logged_in_username, $contact_username, $contact_username, $logged_in_username, $formatted_time);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $messages = array();
        $last_message_timestamp = $last_update_time; // Initialize with provided last update time

        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = "<div class='message'>
                              <span class='message-sender'>" . htmlspecialchars($row['sender']) . ":</span>
                              <span class='message-content'>" . htmlspecialchars($row['message']) . "</span>
                              <span class='message-timestamp'>" . date('Y-m-d H:i:s', strtotime($row['timestamp'])) . "</span>
                           </div>";
            $last_message_timestamp = max($last_message_timestamp, strtotime($row['timestamp']) * 1000); // Convert to milliseconds
        }

        echo json_encode(array("success" => true, "messages" => $messages, "last_message_timestamp" => $last_message_timestamp));
    } else {
        echo json_encode(array("success" => false, "error" => "Error preparing statement"));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Contact username not provided"));
}

mysqli_close($link);

