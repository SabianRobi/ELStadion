<?php
include_once('../misc/auth.php');
include_once('../misc/userStorage.php');
include_once('../misc/teamStorage.php');
include_once('../misc/matchStorage.php');
include_once('../misc/redirect.php');
include('./header.php');

$auth = new Auth(new UserStorage());
if(!$auth->authorize(['admin'])) redirect('./index.php');

function validate($p, &$data, &$errors) {
  //home team
  if(!isset($p['home']) || trim($p['home']) === "") {
		$errors['home'] = "Helyi csapat megadása kötelező!";
	} else {
		$data['home'] = $p['home'];
	}

  //away team
  if(!isset($p['away']) || trim($p['away']) === "") {
		$errors['away'] = "Vendég csapat megadása kötelező!";
	} else {
		$data['away'] = $p['away'];
	}

  //home =? away
  if(count($errors) === 0 && $p['home'] === $p['away']) {
    $errors['global'] = "Egy csapat sem játszhat saját magával!";
  }

  //date date("Y.m.d H:i:s"); 
  date_default_timezone_set('Europe/Budapest');
  $dateNow = null;
  $dateGiven = null;

  if(!isset($p['date']) || trim($p['date']) === "") {
		$errors['date'] = "Mérkőzés dátumának megadása kötelező!";
	} else {
    $d = explode('-',explode('T',$_POST['date'])[0]);
    if(!checkdate($d[1], $d[2], $d[0])) {
      $errors['date'] = "Érvénytelen dátum!";
    } else {
      $data['date'] = $p['date'];
      $dateNow = new DateTime('now');
      $dateGiven = new DateTime($data['date']);
    }
  }

  

  //homeScore
  if(isset($dateGiven) && $dateNow < $dateGiven) {
    $data['homeScore'] = null;
  } else if(!isset($p['homeScore']) || trim($p['homeScore']) === "") {
		$errors['homeScore'] = "Helyi csapat pontszámának megadása kötelező!";
	} else if($p['homeScore'] < 0) {
		$errors['homeScore'] = "Helyi csapat nem érhet el negatív pontszámot!";
	} else {
    $data['homeScore'] = $p['homeScore'];
  }

  //awayScore
  if(isset($dateGiven) && $dateNow < $dateGiven) {
    $data['awayScore'] = null;
  } else if(!isset($p['awayScore']) || trim($p['awayScore']) === "") {
		$errors['awayScore'] = "Vendég csapat pontszámának megadása kötelező!";
	} else if($p['awayScore'] < 0) {
		$errors['awayScore'] = "Vendég csapat nem érhet el negatív pontszámot!";
	} else {
    $data['awayScore'] = $p['awayScore'];
  }

  
  return count($errors) === 0;
}

$matchStorage = new MatchStorage();
$teamStorage = new TeamStorage();
$teams = $teamStorage->findAll();

$errors = [];
$data = [];
$success = false;

if (count($_POST) > 0) {
  if(validate($_POST, $data, $errors)) {
      $success = true;

      $match['home'] = $data['home'];
      $match['homeScore'] = $data['homeScore'];
      $match['away'] = $data['away'];
      $match['awayScore'] = $data['awayScore'];
      $match['date'] = $data['date'];
      
      $matchStorage->add($match);
  }
}

if($success) {
  $homeName = $teamStorage->findById($data['home'])['name'];
  $awayName = $teamStorage->findById($data['away'])['name'];
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
  <link rel="stylesheet" href="../css/auth.css">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  
  <title>ELStadion - Meccsrögzítés</title>
</head>
<body>
  <?php headerHTML(); ?>
  <div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center pt-5">
       <div class="col-md-6 col-lg-4 col-xl-4">
          <div class="card">
             <div class="card-body">
                <div class="text-center m-auto">
                   <h4 class="text-center">Mérkőzés adatainak rögzítése</h4>
                </div>
                
                <?php if($success) : ?>
                  <div class="mb-2">
                    <p><?=$homeName?> - <?=$awayName?> mérkőzés sikeresen hozzáadva!</p>
                  </div>
                <?php endif ?>

                <form action="" method="post" novalidate>
                  <div class="form-group mb-2">
                    <label for="date">Mérkőzés időpontja</label>
                    <input type="datetime-local" name="date" class="form-control" value="<?= $success ? '' : ($_POST['date'] ?? '') ?>">
                    <?php if(isset($errors['date'])) : ?>
                      <span class="hiba"><?= $errors['date'] ?></span>
                    <?php endif ?>
                  </div>

                   <div class="form-group mb-2">
                      <label for="home">Helyi csapat</label><br>
                      <select name="home" id="home">
                        <?php foreach($teams as $team) : ?>
                          <option value="<?= $team['id'] ?>" <?= count($_POST) > 0 && $team['id'] === $_POST['home'] ? (!$success ? 'selected':''):'' ?>><?= $team['name'] ?> (<?=$team['city']?>)</option>
                        <?php endforeach ?>
                      </select>
                      <?php if(isset($errors['home'])) : ?>
                        <span class="hiba"><?= "<br>".$errors['home'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-2">
                      <label for="homeScore">Elért pont</label>
                      <input type="number" name="homeScore" placeholder="3" min="0" class="form-control" value="<?= $success ? '0' : ($_POST['homeScore'] ?? '0') ?>">
                      <?php if(isset($errors['homeScore'])) : ?>
                        <span class="hiba"><?= $errors['homeScore'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-2">
                      <label for="away">Vendég csapat</label><br>
                      <select name="away" id="away">
                        <?php foreach($teams as $team) : ?>
                          <option value="<?= $team['id'] ?>" <?= count($_POST) > 0 && $team['id'] === $_POST['away'] ? (!$success ? 'selected':''):'' ?>><?= $team['name'] ?> (<?=$team['city']?>)</option>
                        <?php endforeach ?>
                      </select>
                      <?php if(isset($errors['away'])) : ?>
                        <span class="hiba"><?= "<br>".$errors['away'] ?></span>
                      <?php endif ?>
                      <?php if(isset($errors['global'])) : ?>
                        <span class="hiba"><?= "<br>".$errors['global'] ?></span>
                      <?php endif ?>
                   </div>
                   
                   <div class="form-group mb-2">
                      <label for="awayScore">Elért pont</label>
                      <input type="number" name="awayScore" placeholder="2" min="0" class="form-control" value="<?= $success ? '0' : ($_POST['awayScore'] ?? '0') ?>">
                      <?php if(isset($errors['awayScore'])) : ?>
                        <span class="hiba"><?= $errors['awayScore'] ?></span>
                      <?php endif ?>
                   </div>
                   
                   <div class="form-group mb-0 text-center">
                      <button class="btn btn-success btn-block" type="submit" name="submit">Mérkőzés hozzáadása</button>
                   </div>
                </form>
             </div>
          </div>
       </div>
    </div>
  </div>
  <script>
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
  <script src="../js/registerMatch.js"></script>
</body>
</html>