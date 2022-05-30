<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/config/db-connection.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/auth-functions.php");

not_authed();

$message = null;
$user_inputs = array(
    "username" => null,
    "password" => null,
    "email" => null,
    "nic" => null,
    "age_range" => null,
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

    if (!$inputs["age_range"] || !$inputs["gender"]) {
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

    $query = "SELECT * FROM users WHERE nic_number = '" . $inputs['nic'] . "'";
    $nic_query = mysqli_fetch_assoc($connection->query($query));
    if ($nic_query) {
        return 'NIC number already exists! Log in to proceed';
    }
}

function create_user($connection, $inputs)
{
    // Generate a hash of the password
    $hash = crypt($inputs["password"], '$6$10$QR7BxavGpWqUHwm$');

    $username = $inputs["username"];
    $email = $inputs["email"];
    $nic = $inputs["nic"];
    $age_range = $inputs["age_range"];
    $gender = $inputs["gender"];

    // Insert data to tables
    $query = "INSERT INTO users (username, email, password, nic_number, age_range, gender)
            VALUES ('$username', '$email', '$hash', '$nic', '$age_range', '$gender')";
    $connection->query($query);

    // Set the username
    $_SESSION['username'] = $inputs["username"];

    // Redirect to root directory
    header("Location: /");
    exit();
}

if (isset($_POST['submit'])) {
    try {
        // Escape user inputs to prevent sqli
        $user_inputs["username"] = mysqli_real_escape_string($connection, $_POST['username']);
        $user_inputs["password"] = mysqli_real_escape_string($connection, $_POST['password']);
        $user_inputs["email"] = mysqli_real_escape_string($connection, $_POST['email']);
        $user_inputs["nic"] = mysqli_real_escape_string($connection, $_POST['nic']);
        $user_inputs["age_range"] = mysqli_real_escape_string($connection, $_POST['age_range']);
        $user_inputs["gender"] = mysqli_real_escape_string($connection, $_POST['gender']);

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
        <h3 class="mt-5">Heisenberge Polls</h3>
        <h3><?php echo $message ?></h3>
        <form action="/auth/register" method="POST" class="row g-3 mb-4 mt-4">
            <!-- Username -->
            <div class="col-md-6">
                <label class="form-label h6" for="inputUsername">Username</label>
                <div class="input-group">
                    <div class="input-group-text">@</div>
                    <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username" value="<?php echo $user_inputs["username"] ?>">
                </div>
            </div>
            <!-- Password -->
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label h6">Password</label>
                <input type="password" class="form-control" id="inputPassword4" name="password" placeholder="xxxxxx">
            </div>
            <!-- Email -->
            <div class="col-12">
                <label for="inputEmail" class="form-label h6">Email</label>
                <input type="text" class="form-control" id="inputEmail" name="email" placeholder="xxx@xxx.com" value="<?php echo $user_inputs["email"] ?>">
            </div>
            <!-- Age Range -->
            <div class="col-6">
                <div class="form-group">
                    <label for="age-range" class="form-label h6">Age Range</label>
                    <select class="form-control" id="age-range" name="age_range">
                        <option value="11-20">11-20</option>
                        <option value="21-30">21-30</option>
                        <option value="31-40">31-40</option>
                        <option value="41-50">41-50</option>
                        <option value="51-60">51-60</option>
                        <option value="61-70">61-70</option>
                        <option value="71-80">71-80</option>
                    </select>
                </div>
            </div>
            <!-- Gender -->
            <div class="col-md-6">
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
            <!-- NIC Number -->
            <div class="col-12">
                <label for="inputNic" class="form-label h6">NIC Number</label>
                <input type="text" class="form-control" id="inputNic" name="nic" placeholder="xxxxxxxxxxx" value="<?php echo $user_inputs["nic"] ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="submit">Register</button>
            </div>
        </form>
        <h4>Already have an account? <a href="/auth/login">Log In</a></h4>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>