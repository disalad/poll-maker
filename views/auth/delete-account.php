<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

$message = null;

function delete_acc($connection, $username, $password)
{
    $password_regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_fetch_assoc($connection->query($query));
    $hash = crypt($password, '$6$10$QR7BxavGpWqUHwm$');

    if (!$result || $result['password'] != $hash) {
        return 'Wrong password';
    }

    $query = "DELETE FROM users WHERE id=" . $result['id'];
    $connection->query($query);

    session_unset();
    session_destroy();

    redirectTo("/auth/register");
}

if (isset($_POST['submit'])) {
    try {
        // Escape user inputs to prevent sqli
        $password = esc_str($connection, $_POST['password']);
        $username = $_SESSION['username'];

        $message = delete_acc($connection, $username, $password);
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
    <link rel="stylesheet" href="/dist/css/nav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Heisenberge Polls | Delete Account</title>
</head>

<body style="font-family:'Segoe UI'">
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <div class="container">
        <h3 class="mt-5 mb-4">Heisenberge Polls</h3>
        <h6 class="mt-3 mb-4"><?php echo $message ?></h6>
        <form action="/auth/delete-account" method="POST" class="row g-3 mb-4">
            <!-- Password -->
            <div class="col-md-12">
                <label class="form-label h6" for="inputPassword">Password</label>
                <input type="text" class="form-control" id="inputPassword" name="password" placeholder="xxxxxx">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="submit">Delete Account</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>