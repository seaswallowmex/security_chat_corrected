<?php
require_once "config.php";

function generateRandomUsername($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

$password = $confirm_password = "";
$password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = generateRandomUsername();
  
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    $is_admin = isset($_POST['is_admin']) ? 1 : 0; // Check if the checkbox is checked
    
    if (empty($password_err) && empty($confirm_password_err)) {
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_token = generateToken();
        $param_created_at = date("Y-m-d H:i:s");
        
        $sql = "INSERT INTO users (username, password, created_at, token, is_admin) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $param_password, $param_created_at, $param_token, $is_admin);
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: display_username.php?username=" . $username);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; }
        .wrapper { width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="checkbox" name="is_admin" id="is_admin">
                <label for="is_admin">Are you an admin?</label>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="index.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
