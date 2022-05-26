<?php
require_once '../config/db-connection.php';
require_once '../helpers/auth-functions.php';

start_session();

not_authed();

$message = $username = $password = null;

function login_user($connection, $username, $password) {
    // Fetch data from the database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_fetch_assoc($connection->query($query));

    // Generate a hash of the password
    $hash = crypt($password, '$6$10$QR7BxavGpWqUHwm$');

    // Compare hashed password and the existing hash
    if (!$result || $result['password'] != $hash) {
        return 'Incorrect username or password';
    }

    // Set the username
    $_SESSION['username'] = $username;

    // Redirect to root directory
    header("Location: /");
    exit();
}

if (isset($_POST['submit'])) {
    try {
        // Escape user inputs to prevent sqli
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);

        $message = login_user($connection, $username, $password);
    } catch (Exception $e) {
        die("Internal Server Error" . $e);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>

<body style="font-family:'Segoe UI'">
    <h2>PHP AUTH</h2>

    <h3><?php echo $message ?></h3>

    <form action="<?php echo "/auth/login.php" ?>" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo $username ?>"><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Submit" name="submit">
    </form>
    <h3>Don't have an account? <a href="<?php echo "/auth/register.php" ?>">Register</a></h3>
</body>

</html>