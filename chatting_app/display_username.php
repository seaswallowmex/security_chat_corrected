<?php
session_start();

if (isset($_GET['username'])) {
    $username = htmlspecialchars($_GET['username']);
} else {
    header("location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Username</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 50px auto;
            width: 300px;
            text-align: center;
        }
        .username-box {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Generated Username</h1>
    <div class="username-box">
        <?php echo $username; ?>
    </div>
    <p>Ready to log in? <a href="index.php">Login here</a>.</p>
    
</div>

</body>
</html>
