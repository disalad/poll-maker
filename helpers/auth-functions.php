<?php
function authed(){
    if (!isset($_SESSION["username"])) {
        header("Location: /auth/login");
        exit(); // prevent further execution, should there be more code that follows
    }
}

function not_authed(){
    if (isset($_SESSION["username"])) {
        header("Location: /");
        exit(); // prevent further execution, should there be more code that follows
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
