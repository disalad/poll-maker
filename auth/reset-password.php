<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/auth-functions.php");

authed();

$message = null;

function reset_password($connection, $username, $old_password, $new_password)
{
    $password_regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_fetch_assoc($connection->query($query));
    $old_hash = crypt($old_password, '$6$10$QR7BxavGpWqUHwm$');

    if (!$result || $result['password'] != $old_hash) {
        return 'Wrong password';
    }
    if (!preg_match($password_regex, $new_password)) {
        return 'Enter a strong password';
    }

    $new_hash = crypt($new_password, '$6$10$QR7BxavGpWqUHwm$');

    $query = "UPDATE users SET password='$new_hash' WHERE id=" . $result['id'];
    $connection->query($query);

    session_unset();

    header("Location: /auth/login");
    exit();
}

if (isset($_POST['submit'])) {
    $old_password = mysqli_real_escape_string($connection, $_POST['old-password']);
    $new_password = mysqli_real_escape_string($connection, $_POST['new-password']);
    $username = $_SESSION['username'];

    $message = reset_password($connection, $username, $old_password, $new_password);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Heisenberge Polls | Reset Password</title>
</head>

<body style="font-family:'Segoe UI'">
    <div class="container">
        <h3 class="mt-5 mb-4">Heisenberge Polls</h3>
        <h6 class="mt-3 mb-4"><?php echo $message ?></h6>
        <form action="/auth/reset-password" method="POST" class="row g-3 mb-4">
            <!-- Old Password -->
            <div class="col-md-12">
                <label class="form-label h6" for="inputOldPassword">Old Password</label>
                <input type="text" class="form-control" id="inputOldPassword" name="old-password" placeholder="xxxxxx">
            </div>
            <!-- New Password -->
            <div class="col-md-12">
                <label for="inputNewPassword4" class="form-label h6">New Password</label>
                <input type="password" class="form-control" id="inputNewPassword4" name="new-password" placeholder="xxxxxx">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="submit">Reset Password</button>
            </div>
        </form>
    </div>
</body>

</html>