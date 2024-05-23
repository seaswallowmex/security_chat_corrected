<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$sender = $_SESSION["username"];
$receiver = $_POST["username"];

// Check if the user exists
$sql = "SELECT username FROM users WHERE username = ? AND username != ? AND is_admin != '1'";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $receiver, $sender);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        // Check if a friend request already exists
        $sql_check = "SELECT id FROM friend_requests WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)";
        if ($stmt_check = mysqli_prepare($link, $sql_check)) {
            mysqli_stmt_bind_param($stmt_check, "ssss", $sender, $receiver, $receiver, $sender);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) == 0) {
                // Insert the friend request
                $sql_insert = "INSERT INTO friend_requests (sender, receiver) VALUES (?, ?)";
                if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt_insert, "ss", $sender, $receiver);
                    if (mysqli_stmt_execute($stmt_insert)) {
                        echo "Friend request sent.";
                    } else {
                        echo "Error: Could not send friend request.";
                    }
                }
                mysqli_stmt_close($stmt_insert);
            } else {
                echo "Friend request already sent.";
            }
            mysqli_stmt_close($stmt_check);
        }
    } else {
        echo "User does not exist or is not a valid contact.";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
