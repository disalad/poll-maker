<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

get('/', 'index.php');

any('/auth/login', 'views/auth/login.php');

any('/auth/register', 'views/auth/register.php');

get('/auth/logout', 'views/auth/logout.php');

any('/auth/reset-password', 'views/auth/reset-password.php');

any('/auth/delete-account', 'views/auth/delete-account.php');

any('/poll/create', 'views/poll/create.php');

any('/poll/$id', 'views/poll/poll.php');

any('/404','404.php');