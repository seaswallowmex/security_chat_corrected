<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(array("success" => false, "error" => "User is not logged in"));
    exit;
}

require_once "config.php";

$sender = $_SESSION["username"];
$receiver = $_POST["receiver"];
$message = $_POST["message"];

if (empty($receiver) || empty($message)) {
    echo json_encode(array("success" => false, "error" => "Receiver or message is empty"));
    exit;
}

$sql = "INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "sss", $sender, $receiver, $message);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "error" => "Error executing query"));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Error preparing statement"));
}

mysqli_close($link);
