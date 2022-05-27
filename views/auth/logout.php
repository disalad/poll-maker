<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/auth-functions.php");

authed();

session_unset();

session_destroy();

header("Location: /auth/login");

exit();
