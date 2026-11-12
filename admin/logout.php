<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
start_session();
$_SESSION = [];
session_destroy();
redirect('login.php');
