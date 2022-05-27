<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/auth-functions.php");

not_authed();

$message = $username = $password = null;

function login_user($connection, $username, $password)
{
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Heisenberge Polls | Log In</title>
</head>

<body>
    <div class="container">
        <h3 class="mt-5 mb-4">Heisenberge Polls</h3>
        <h6 class="mt-3 mb-4"><?php echo $message ?></h6>
        <form action="/auth/login" method="POST" class="row g-3 mb-4">
            <!-- Username -->
            <div class="col-md-12">
                <label class="form-label h6" for="inputUsername">Username</label>
                <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username" value="<?php echo $username ?>">
            </div>
            <!-- Password -->
            <div class="col-md-12">
                <label for="inputPassword4" class="form-label h6">Password</label>
                <input type="password" class="form-control" id="inputPassword4" name="password" placeholder="xxxxxx">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="submit">Register</button>
            </div>
        </form>
        <h4>Don't have an account? <a href="<?php echo "/auth/register" ?>">Register</a></h4>
    </div>
</body>

</html>