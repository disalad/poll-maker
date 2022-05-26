<?php
require_once '../helpers/auth-functions.php';

start_session();

authed();

session_unset();

session_destroy();

header("Location: /auth/login.php");

exit();
