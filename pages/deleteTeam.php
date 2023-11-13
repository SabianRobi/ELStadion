<?php
include_once('../misc/teamStorage.php');
include_once('../misc/matchStorage.php');
include_once('../misc/commentStorage.php');
include_once('../misc/userStorage.php');
include_once('../misc/redirect.php');
include_once('../misc/auth.php');

session_start();
$userStorage = new UserStorage();
$auth = new Auth($userStorage);
if(!$auth->authorize(['admin'])) redirect("index.php");

if(count($_GET) > 0 && isset($_GET['id'])) {
  $teamId = $_GET['id'];
  $teamStorage = new TeamStorage();
  $commentStorage = new CommentStorage();
  $matchStorage = new MatchStorage();

  //Remove team from favourites
  $usersFav = $userStorage->findMany(function($user) use ($teamId) {
    return in_array($teamId, $user['favourites']);
  });
  foreach ($usersFav as $user) {
    $key = array_search($teamId, $_SESSION['user']['favourites']);
    unset($_SESSION['user']['favourites'][$key]);
  }
  $userStorage->update($_SESSION['user']['id'], $_SESSION['user']);

  //remove team comments
  $commentStorage->deleteMany(function($comment) use ($teamId) {
    return $comment['team'] === $teamId;
  });

  //remove matches includes team
  $matchStorage->deleteMany(function($match) use ($teamId) {
    return $match['home'] === $teamId || $match['away'] === $teamId;
  });

  //remove team
  $teamStorage->delete($_GET['id']);
}
redirect("index.php");
?>
