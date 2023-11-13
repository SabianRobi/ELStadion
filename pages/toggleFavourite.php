<?php
include_once('../misc/userStorage.php');
include_once('../misc/auth.php');
include_once('../misc/redirect.php');

session_start();
$userStorage = new UserStorage();
$auth = new Auth($userStorage);

if(!$auth->is_authenticated()) redirect("index.php");
if(!isset($_GET['teamId'])) redirect("index.php");

$user = $auth->authenticated_user();
$teamId = $_GET['teamId'];

if(in_array($teamId, $_SESSION['user']['favourites'])) {
  $key = array_search($teamId, $_SESSION['user']['favourites']);
  unset($_SESSION['user']['favourites'][$key]);
} else {
  $_SESSION['user']['favourites'][] = $teamId;
}

$userStorage->update($_SESSION['user']['id'], $_SESSION['user']);

redirect((bool)$_GET['fromTeam'] ? 'teamDetails.php?id='.$_GET['teamId'] : 'index.php');
?>