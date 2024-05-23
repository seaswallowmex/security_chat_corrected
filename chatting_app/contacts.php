<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$logged_in_username = $_SESSION["username"];

// Fetch pending friend requests
$sql_pending = "SELECT sender FROM friend_requests WHERE receiver = ? AND status = 'pending'";
$pending_requests = [];
if ($stmt_pending = mysqli_prepare($link, $sql_pending)) {
    mysqli_stmt_bind_param($stmt_pending, "s", $logged_in_username);
    mysqli_stmt_execute($stmt_pending);
    $result_pending = mysqli_stmt_get_result($stmt_pending);

    while ($row_pending = mysqli_fetch_array($result_pending)) {
        $pending_requests[] = $row_pending['sender'];
    }
    mysqli_stmt_close($stmt_pending);
}

// Fetch friends list
$sql_friends = "SELECT username FROM users WHERE username != ? AND username IN (
                    SELECT CASE 
                        WHEN sender = ? THEN receiver 
                        WHEN receiver = ? THEN sender 
                    END AS friend
                    FROM friend_requests 
                    WHERE (sender = ? OR receiver = ?) AND status = 'accepted'
                )";
$friends = [];
if ($stmt_friends = mysqli_prepare($link, $sql_friends)) {
    mysqli_stmt_bind_param($stmt_friends, "sssss", $logged_in_username, $logged_in_username, $logged_in_username, $logged_in_username, $logged_in_username);
    mysqli_stmt_execute($stmt_friends);
    $result_friends = mysqli_stmt_get_result($stmt_friends);

    while ($row_friends = mysqli_fetch_array($result_friends)) {
        $friends[] = $row_friends['username'];
    }
    mysqli_stmt_close($stmt_friends);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Friends</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            margin-top: 50px;
        }
        .container {
            max-width: 600px;
            text-align: center;
            margin: 0 auto;
        }
        .username-btn {
            margin-bottom: 10px;
        }
        h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        #friend-request-form {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Friends</h2>
        <form id="friend-request-form" action="send_friend_request.php" method="post">
            <input type="text" name="username" placeholder="Enter username" required>
            <button type="submit" class="btn btn-primary">Send Friend Request</button>
        </form>
        <h3>Pending Friend Requests</h3>
        <?php
        if (empty($pending_requests)) {
            echo "<p>No pending friend requests.</p>";
        } else {
            foreach ($pending_requests as $request) {
                echo "<div>$request 
                        <a href='respond_to_request.php?action=accept&username=" . urlencode($request) . "'><button class='btn btn-success'>Accept</button></a>
                        <a href='respond_to_request.php?action=reject&username=" . urlencode($request) . "'><button class='btn btn-danger'>Reject</button></a>
                      </div>";
            }
        }
        ?>
        <h3>Your Friends</h3>
        <?php
        if (empty($friends)) {
            echo "<p>You have no friends added.</p>";
        } else {
            foreach ($friends as $friend) {
                echo "<a href='chats.php?username=" . urlencode($friend) . "'><button class='btn btn-primary username-btn'>" . htmlspecialchars($friend) . "</button></a>";
            }
        }
        ?>
    </div>
</body>
</html>
<?php
mysqli_close($link);
?>
