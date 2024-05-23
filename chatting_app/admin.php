<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["is_admin"] != 1) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user"])) {
    $user_id_to_delete = $_POST["user_id"];
    
    $sql_delete = "DELETE FROM users WHERE id = ?";
    
    if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_delete, "i", $user_id_to_delete);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
    }
}

$sql = "SELECT id, username, is_admin FROM users";
$result = mysqli_query($link, $sql);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; }
        .wrapper { width: 720px; padding: 20px; }
        .table-wrapper { max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>. You can manage users below.</p>
        
        <div class="table-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["username"]); ?></td>
                        <td><?php echo $row["is_admin"] ? "Admin" : "User"; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="user_id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <p><a href="logout.php" class="btn btn-secondary">Log Out</a></p>
    </div>
</body>
</html>
