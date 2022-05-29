<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

get('/', 'index.php');

get('/auth/login', 'views/auth/login.php');
post('/auth/login', 'views/auth/login.php');

get('/auth/register', 'views/auth/register.php');
post('/auth/register', 'views/auth/register.php');

get('/auth/logout', 'views/auth/logout.php');

get('/auth/reset-password', 'views/auth/reset-password.php');
post('/auth/reset-password', 'views/auth/reset-password.php');

get('/auth/delete-account', 'views/auth/delete-account.php');
post('/auth/delete-account', 'views/auth/delete-account.php');

get('/poll/create', 'views/poll/create.php');
post('/poll/create', 'views/poll/create.php');

get('/poll/$id', 'views/poll/poll.php');
post('/poll/$id', 'views/poll/poll.php');

get('/poll/$id/success', 'views/poll/vote_success.php');

any('/404','404.php');