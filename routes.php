<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

get('/', 'index.php');

any('/auth/login', 'auth/login.php');

any('/auth/register', 'auth/register.php');

get('/auth/logout', 'auth/logout.php');

any('/auth/reset-password', 'auth/reset-password.php');

any('/auth/delete-account', 'auth/delete-account.php');

any('/poll/create', 'poll/create.php');

any('/404','404.php');