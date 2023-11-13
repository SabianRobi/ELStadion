<?php
include_once('../misc/teamStorage.php');

include_once('../misc/userStorage.php');
include_once('../misc/redirect.php');
include_once('../misc/auth.php');
include('./header.php');

$auth = new Auth(new UserStorage());
if(!$auth->authorize(['admin'])) redirect('./index.php');

function validate($p, &$data, &$errors) {
  //Team name
  if(!isset($p['name']) || trim($p['name']) === "") {
		$errors['name'] = "Csapatnév megadása kötelező!";
	} else {
		$data['name'] = $p['name'];
	}

  //Team members
  if(!isset($p['members']) || trim($p['members']) === "") {
		$errors['members'] = "Csapattagok megadása kötelező!";
	} else {
		$data['members'] = $p['members'];
	}

  //City
  if(!isset($p['city']) || trim($p['city']) === "") {
		$errors['city'] = "Képviselt város megadása kötelező!";
	} else {
		$data['city'] = $p['city'];
	}

  return count($errors) === 0;
}

$teamStorage = new TeamStorage();
$errors = [];
$data = [];

if(isset($_GET['id'])) {
  $loadedTeam = $teamStorage->findById($_GET['id']);

  $_POST['name'] = $loadedTeam['name'];
  $_POST['city'] = $loadedTeam['city'];
  $_POST['members'] = implode(',', $loadedTeam['members']);
  
  $_SESSION['team'] = $_GET['id'];

} else if (count($_POST) > 0) {
  if(validate($_POST, $data, $errors)) {
    $sameNameTeam = $teamStorage->findOne(['name' => $data['name']]);

    if(isset($sameNameTeam) && $sameNameTeam['id'] !== $_SESSION['team']) {
      $errors['name'] = "Ilyen nevű csapat már létezik!";
    } else {
      $team['name'] = $data['name'];
      $team['city'] = $data['city'];
      $team['members'] = explode(',', $data['members']);
      $team['id'] = $_SESSION['team'];
      
      $teamStorage->update($team['id'], $team);
      redirect("teamDetails.php?id=".$team['id']);
    }
  }
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
  
  <title>ELStadion - Csapatszerkesztés</title>
</head>


<body>
<?php headerHTML(); ?>
  <div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center pt-5">
       <div class="col-md-6 col-lg-4 col-xl-4">
          <div class="card">
            <div class="card-body">
              <div class="text-center m-auto">
                <h4 class="text-center">Csapatszerkesztés</h4>
              </div>

              <form action="editTeam.php" method="post" novalidate>
                <div class="form-group mb-2">
                  <label for="name">Csapatnév</label>
                  <input type="text" name="name" placeholder="<?= $_POST['name'] ?? 'ELTE FC' ?>" class="form-control" value="<?= $_POST['name'] ?? '' ?>">
                  <?php if(isset($errors['name'])) : ?>
                    <span class="hiba"><?= $errors['name'] ?></span>
                  <?php endif ?>
                </div>

                <div class="form-group mb-2">
                  <label for="city">Képviselt település</label>
                  <input type="text" name="city" placeholder="<?= $_POST['city'] ?? 'Budapest' ?>" class="form-control" value="<?= $_POST['city'] ?? '' ?>">
                  <?php if(isset($errors['city'])) : ?>
                    <span class="hiba"><?= $errors['city'] ?></span>
                  <?php endif ?>
                </div>
                <div class="form-group mb-2">
                  <label for="members">Csapattagok</label>
                  <input type="text" name="members" placeholder="<?= $_POST['city'] ?? 'Béla,Jani,Kati,Emese' ?>" class="form-control" value="<?= $_POST['members'] ?? '' ?>">
                  <?php if(isset($errors['members'])) : ?>
                    <span class="hiba"><?= $errors['members'] ?></span>
                  <?php endif ?>
                </div>

                <div class="form-group mb-0 text-center">
                  <a href="teamDetails.php?id=<?=$_SESSION['team']?>"><span class="btn btn-primary btn-block">Vissza</span></a>
                  <a href="deleteTeam.php?id=<?=$_SESSION['team']?>"><span class="btn btn-danger btn-block">Csapat törlése</span></a>
                  <button class="btn btn-success btn-block" type="submit" name="submit">Csapat szerkesztése</button>
                </div>
              </form>
            </div>
          </div>
       </div>
    </div>
  </div>
</body>
</html>