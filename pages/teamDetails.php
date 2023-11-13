<?php

include_once('../misc/teamStorage.php');
include_once('../misc/commentStorage.php');
include_once('../misc/matchStorage.php');
include_once('../misc/userStorage.php');
include_once('../misc/auth.php');
include('./header.php');
include('./getMatches.php');

$teamStorage = new TeamStorage();
$commentStorage = new CommentStorage();
$matchStorage = new MatchStorage();
$userStorage = new UserStorage();
$auth = new Auth($userStorage);

date_default_timezone_set('Europe/Budapest');

function validate($p, &$data, &$errors) {
  if(!isset($p['comment']) || trim($p['comment']) === "") {
    $errors['comment'] = "Hozzászólás nem lehet üres!";
	} else {
		$data['comment'] = $p['comment'];
	}
  return count($errors) === 0;
}

$team = $teamStorage->findById($_GET['id']);

$data = [];
$errors = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if($auth->is_authenticated()) {
      $authenticated_user = $auth->authenticated_user();
      $dateNow = new DateTime('now');

      $newComment['author'] = $authenticated_user['id'];
      $newComment['comment'] = $data['comment'];
      $newComment['team'] = $team['id'];
      $newComment['date'] = $dateNow->format("Y-m-d\TH:i:s");

      $commentStorage->add($newComment);
    }
  } else {
  }
}

$comments = $commentStorage->findMany(function($comment) use ($team) {
  return $comment['team'] === $team['id'];
});

function isFavourite($teamId) {
  global $authenticated_user;
  return in_array($teamId, $authenticated_user['favourites']);
}
?>


<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" href="../img/icon.png" type="image/png">
  <link rel="stylesheet" href="../css/master.css">
  <link rel="stylesheet" href="../css/cards.css">
  <link rel="stylesheet" href="../css/teamDetails.css">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  
  <title>ELStadion - Csapatok (<?=$team['name']?>)</title>
</head>

<body>
  <?php headerHTML(); ?>
  <div class="row">
    <div class="col-12">
      <div class="kozepre content p-4">
        <h1><?= $team['name'] ?>
        <?php if($auth->is_authenticated()) : ?>
          <a href="./toggleFavourite.php?teamId=<?=$team['id']?>&fromTeam=True" class="favourite"><img src="<?= isFavourite($team['id']) ? '../img/fav_filled.webp' : '../img/fav.webp' ?>" alt="Kedvenc állítása"  height="30" width="30"></a>
        <?php endif ?>
        <?php if($auth->authorize(['admin'])) : ?>
          <a href="./editTeam.php?id=<?=$team['id']?>" class="delete"><img src="../img/edit.webp" alt="Szerkesztés" height="20" width="20"></a>
        <?php endif ?></h1>
      
      <h2>Képviselt település</h2>
      <span class="city"><?=$team['city']?></span>

      <h2>Csapattagok</h2>
      <div class="row">
        <?php foreach($team['members'] as $member) : ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3"><span class="member"><?= $member ?></span></div>
        <?php endforeach ?>
      </div>

      <?= getMatches(true, $team['id'], 0, -1, SORT_ASC, $team['id']); ?>

      <?= getMatches(false, $team['id'], 0, -1, SORT_DESC, $team['id']); ?>

      <h2>Hozzászólások</h2>
      <div class="row">
        <?php if($auth->is_authenticated()) : ?>
        <div class="col-xs-12 col-sm-6 col-md-5">
          <form action="" method="post">
            <div class="form-group mb-2">
              <h3>Új hozzászólás írása:</h3>
              <label hidden for="comment">Új hozzászólás írása:</label>
              <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
              <?php if(count($errors) > 0) : ?>
              <div class="mb-2 hibak">
                <p><?php echo($errors['comment']); ?></p>
              </div>
              <?php endif ?>
            </div>
            <input type="submit" value="Elküldés" class="btn btn-success">
          </form>
        </div>
        <?php else : ?>
          <p>Hozzászólás írásához jelentkezz be!</p>
        <?php endif ?>
      </div>

      <div class="row">
        <?php foreach ($comments as $comment) : ?>
        <div class="col-12 col-md-6">
          <div class="commentcard">
            <p class="author mb-0 mt-2"><?= $userStorage->findById($comment['author'])['username'] ?></p>
            <div class="commentDate">
              <p class="comment mb-1"><?= $comment['comment']?> </p>
              <div>
                <p class="date"><?= readableDate($comment['date']) ?>
                <?php if($auth->authorize(['admin'])) : ?>
                  <a href="./deleteComment.php?id=<?=$comment['id']?>&team=<?=$team['id']?>" class="delete"><img src="../img/delete.webp" alt="Delte" height="15" width="15"></a></p>
                <?php endif ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach ?>
      </div>

    </div>
  </div>
<script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>
</body>
</html>