<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$logged_in_username = $_SESSION["username"];
$action = $_GET["action"];
$request_sender = $_GET["username"];

if ($action == "accept") {
    $sql = "UPDATE friend_requests SET status = 'accepted' WHERE sender = ? AND receiver = ? AND status = 'pending'";
    $actioned = "accepted";
} elseif ($action == "reject") {
    $sql = "UPDATE friend_requests SET status = 'rejected' WHERE sender = ? AND receiver = ? AND status = 'pending'";
    $actioned = "rejected";
} else {
    echo "Invalid action.";
    exit;
}

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $request_sender, $logged_in_username);
    if (mysqli_stmt_execute($stmt)) {
        echo "Request $actioned.";
    } else {
        echo "Error: Could not update the request.";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error: Could not prepare the statement.";
}

mysqli_close($link);
