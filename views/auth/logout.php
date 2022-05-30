<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/helpers/functions.php");

authed();

session_unset();

session_destroy();

redirectTo("/auth/login");