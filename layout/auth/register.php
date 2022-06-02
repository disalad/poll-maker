<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

not_authed();

$message = null;
$user_inputs = array(
    "username" => null,
    "password" => null,
    "email" => null,
    "gender" => null
);

function validate_inputs($connection, $inputs)
{
    $email_regex = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
    $password_regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
    $alphanumeric_regex = '/[^A-Za-z0-9]/';

    // Validate user inputs
    if (preg_match($alphanumeric_regex, $inputs["username"])) {
        return 'Username should contain only alphanumeric characters';
    }

    if (strlen($inputs["username"]) < 5) {
        return 'Username\'s length should be more than 4';
    }

    if (!preg_match($password_regex, $inputs["password"])) {
        return 'Enter a strong password';
    }

    if (!preg_match($email_regex, $inputs["email"])) {
        return 'Enter a valid email';
    }

    if (!$inputs["gender"]) {
        return 'Fill all the required fields';
    }

    $query = "SELECT * FROM users WHERE username = '" . $inputs['username'] . "'";
    $username_query = mysqli_fetch_assoc($connection->query($query));
    if ($username_query) {
        return 'Username already exists! Choose another or log in';
    }

    $query = "SELECT * FROM users WHERE email = '" . $inputs['email'] . "'";
    $email_query = mysqli_fetch_assoc($connection->query($query));
    if ($email_query) {
        return 'Email already exists! Log in to proceed';
    }
}

function create_user($connection, $inputs)
{
    // Generate a hash of the password
    $hash = crypt($inputs["password"], '$6$10$QR7BxavGpWqUHwm$');

    $username = $inputs["username"];
    $email = $inputs["email"];
    $gender = $inputs["gender"];

    // Insert data to tables
    $query = "INSERT INTO users (username, email, password, gender)
            VALUES ('$username', '$email', '$hash', '$gender')";
    $connection->query($query);

    // Set the username
    $_SESSION['username'] = $inputs["username"];

    // Redirect to root directory
    redirectTo("/");
}

if (isset($_POST['submit'])) {
    try {
        // Escape user inputs to prevent sqli
        $user_inputs["username"] = esc_str($connection, $_POST['username']);
        $user_inputs["password"] = esc_str($connection, $_POST['password']);
        $user_inputs["email"] = esc_str($connection, $_POST['email']);
        $user_inputs["gender"] = esc_str($connection, $_POST['gender']);

        $message = validate_inputs($connection, $user_inputs);

        // Create the user if there is no error message
        if (!$message) {
            create_user($connection, $user_inputs);
        }
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
    <title>Heisenberge Polls | Register</title>
</head>

<body style="font-family:'Segoe UI'">
    <?php include("{$_SERVER['DOCUMENT_ROOT']}/includes/nav.php") ?>
    <div class="container">
        <h3><?php echo $message ?></h3>
        <form action="/auth/register" method="POST" class="row g-3 mb-4 mt-5 form">
            <!-- Username -->
            <div class="col-md-12">
                <label class="form-label h6" for="inputUsername">Username</label>
                <div class="input-group">
                    <div class="input-group-text">@</div>
                    <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username" value="<?php echo $user_inputs["username"] ?>">
                </div>
            </div>
            <!-- Password -->
            <div class="col-md-12">
                <label for="inputPassword4" class="form-label h6">Password</label>
                <input type="password" class="form-control" id="inputPassword4" name="password" placeholder="xxxxxx">
            </div>
            <!-- Email -->
            <div class="col-md-12">
                <label for="inputEmail" class="form-label h6">Email</label>
                <input type="text" class="form-control" id="inputEmail" name="email" placeholder="xxx@xxx.com" value="<?php echo $user_inputs["email"] ?>">
            </div>
            <!-- Gender -->
            <div class="col-md-12">
                <label class="form-label mb-3 h6" style="display: block;">Gender</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="maleGender" value="male" checked="checked">
                    <label class="form-check-label" for="maleGender">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="femaleGender" value="female">
                    <label class="form-check-label" for="femaleGender">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="otherGender" value="other">
                    <label class="form-check-label" for="otherGender">Other</label>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" name="submit">Register</button>
            </div>
            <h5>Already have an account? <a href="/auth/login">Log In</a></h5>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>