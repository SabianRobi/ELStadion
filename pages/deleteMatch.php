<?php
include_once('../misc/matchStorage.php');
include_once('../misc/userStorage.php');
include_once('../misc/redirect.php');
include_once('../misc/auth.php');

session_start();
$auth = new Auth(new UserStorage());
if(!$auth->authorize(['admin'])) redirect("index.php");

if(count($_GET) > 0 && isset($_GET['matchId'])) {
  $matchStorage = new MatchStorage();
  $matchStorage->delete($_GET['matchId']);
}

redirect($_GET['fromTeam'] !== "" ? "teamDetails.php?id=".$_GET['fromTeam'] : "index.php");
?>