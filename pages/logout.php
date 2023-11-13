<?php

include_once('../misc/userStorage.php');
include_once('../misc/auth.php');
include_once('../misc/redirect.php');

session_start();

$auth = new Auth(new UserStorage());
$auth->logout();
redirect('./index.php');
?>