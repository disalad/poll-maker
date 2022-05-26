<?php
require_once '../config/db-connection.php';
require_once '../helpers/auth-functions.php';

start_session();

not_authed();

$message = $username = $password = $email = null;

function create_user($connection, $username, $email, $password, $nic)
{
    $email_regex = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
    $password_regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
    $alphanumeric_regex = '/[^A-Za-z0-9]/';

    // Validate user inputs
    if (preg_match($alphanumeric_regex, $username)) {
        return 'Username should contain only alphanumeric characters';
    }

    if (strlen($username) < 5) {
        return 'Username\'s length should be more than 4';
    }

    if (!preg_match($password_regex, $password)) {
        return 'Enter a strong password';
    }

    if (!preg_match($email_regex, $email)) {
        return 'Enter a valid email';
    }

    $query = "SELECT * FROM users WHERE username = '$username'";
    $username_query = mysqli_fetch_assoc($connection->query($query));
    if ($username_query) {
        return 'Username already exists! Choose another or log in';
    }

    $query = "SELECT * FROM users WHERE username = '$email'";
    $email_query = mysqli_fetch_assoc($connection->query($query));
    if ($email_query) {
        return 'Email already exists! Log in to proceed';
    }

    $query = "SELECT * FROM users WHERE nic_number = '$nic'";
    $nic_query = mysqli_fetch_assoc($connection->query($query));
    if ($nic_query) {
        return 'NIC number already exists! Log in to proceed';
    }

    // Generate a hash of the password
    $hash = crypt($password, '$6$10$QR7BxavGpWqUHwm$');

    // Insert data to tables
    $query = "INSERT INTO users (username, email, password, nic_number)
            VALUES ('$username', '$email', '$hash', '$nic')";
    $connection->query($query);
    
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
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $nic = mysqli_real_escape_string($connection, $_POST['nic']);

        $message = create_user($connection, $username, $email, $password, $nic);
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
    <title>Sign Up</title>
</head>

<body style="font-family:'Segoe UI'">
    <h2>PHP AUTH</h2>

    <h3><?php echo $message ?></h3>

    <form action="<?php echo "/auth/register.php" ?>" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo $username ?>"><br><br>
        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" value="<?php echo $email ?>"><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>
        <label for="nic">NIC Number:</label><br>
        <input type="text" id="nic" name="nic"><br><br>
        <input type="submit" value="Submit" name="submit">
    </form>
    <h3>Already have an account? <a href="/auth/login.php">Log In</a></h3>
</body>

</html>