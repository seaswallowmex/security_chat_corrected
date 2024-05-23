<?php
session_start();
require_once "config.php";

function getAuthorizationHeader() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getBearerToken() {
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized: Not logged in']);
    exit;
}

$token = getBearerToken();

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized: Token not provided']);
    exit;
}

$sql = "SELECT id FROM users WHERE token = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_token);
    $param_token = $token;
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) != 1) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: Invalid token']);
            exit;
        }
    } else {
        http_response_code(500); 
        echo json_encode(['message' => 'Internal Server Error: Could not validate token']);
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    http_response_code(500); 
    echo json_encode(['message' => 'Internal Server Error: Could not prepare statement']);
    exit;
}

$response = [
    'message' => 'You have accessed a secure endpoint',
];

echo json_encode($response);

mysqli_close($link);


