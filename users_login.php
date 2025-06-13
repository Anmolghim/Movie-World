<?php
include 'google-login.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
  
    $mysqli = new mysqli('localhost', 'root', '', 'gshare',3307);
    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }

    function cleanInput($data, $mysqli) {
        return mysqli_real_escape_string($mysqli, trim($data));
    }

    $email = cleanInput($_POST['email'], $mysqli);
    $password = cleanInput($_POST['password'], $mysqli);

    $query = "SELECT email,password FROM users WHERE email = ?";
    $statement = $mysqli->prepare($query);
    $statement->bind_param('s', $email);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows == 1) {
       
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
  
            $_SESSION['email'] = $email;
            echo "<script>
            alert('Login Successful');
            window.location.href = 'index.php';
          </script>";
    exit();
} else {
    echo "<script>alert('Login unsuccessful, please try again');</script>";
}
} else {
echo "<script>alert('Email and password do not match');</script>";
}
    $statement->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="form.css">
</head>
<style>
       .google-btn {
        background-color: #4285F4;
        color: white;
        padding: 15px;
        border: none;
        border-radius: 20px;
        margin-top: 15px;
        cursor: pointer;
        display: inline-block;
        text-decoration: none;
    }
    .google-btn img {
        vertical-align: middle;
        margin-right: 10px;
    }
</style>
<body>
    <!-- <img src="form.jpg" alt="form"> -->
    

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h2>Login</h2>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" name="login" value="Login">
        <p id="p">Don't have an account? <a href="signup.php">Sign Up</a></p>

        <a href="<?= $client->createAuthUrl(); ?>" class="google-btn">
    <img src="images/google.png" alt="google" style="height: 20px; width: 20px;">
    Log in with Google
</a>
    </form>

   
</body>
</html>
