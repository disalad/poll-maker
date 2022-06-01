<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

get('/', 'index.php');

get('/auth/login', 'layout/auth/login.php');
post('/auth/login', 'layout/auth/login.php');

get('/auth/register', 'layout/auth/register.php');
post('/auth/register', 'layout/auth/register.php');

get('/auth/logout', 'layout/auth/logout.php');

get('/auth/reset-password', 'layout/auth/reset-password.php');
post('/auth/reset-password', 'layout/auth/reset-password.php');

get('/auth/delete-account', 'layout/auth/delete-account.php');
post('/auth/delete-account', 'layout/auth/delete-account.php');

get('/poll/create', 'layout/poll/create.php');
post('/poll/create', 'layout/poll/create.php');

get('/poll/$id', 'layout/poll/poll.php');
post('/poll/$id', 'layout/poll/poll.php');

get('/poll/$id/success', 'layout/poll/vote_success.php');

get('/poll/$id/results', 'layout/poll/results.php');

any('/404','404.php');