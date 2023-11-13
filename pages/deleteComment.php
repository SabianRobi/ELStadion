<?php
include_once('../misc/commentStorage.php');
include_once('../misc/userStorage.php');
include_once('../misc/redirect.php');
include_once('../misc/auth.php');

session_start();
$auth = new Auth(new UserStorage());
if(!$auth->authorize(['admin'])) redirect('./index.php');

if(count($_GET) > 0 && isset($_GET['id'])) {
  $commentStorage = new CommentStorage();
  $commentStorage->delete($_GET['id']);
}
redirect('./teamDetails.php?id='.$_GET['team'])
?>