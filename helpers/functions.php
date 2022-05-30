<?php
// UTILS
function esc_str($connection, $str) {
    return mysqli_real_escape_string($connection, $str);
}

function redirectTo($path) {
    header("Location: $path");
    exit(); // prevent further execution, should there be more code that follows
}

// AUTHENTICATION
function get_username($con) {
    $username = $_SESSION["username"];
    $query = "SELECT * FROM users
            WHERE username='$username'";
    $res = $con->query($query);
    $rowcount = mysqli_num_rows($res);
    if ($rowcount != 1) {
        redirectTo("/404");
    }
    return mysqli_fetch_assoc($res)["id"];
}

function authed() {
    if (!isset($_SESSION["username"])) {
        redirectTo("/auth/login");
    }
}

function not_authed() {
    if (isset($_SESSION["username"])) {
        redirectTo("/");
    }
}

function start_session() {
    session_start([
        'name' => 'Session',
        'cookie_lifetime' => 86000,
        'cookie_httponly' => 1,
        'cookie_samesite' => "Strict",
    ]);
}
?>