<?php
include_once('../misc/auth.php');
include_once('../misc/userStorage.php');
include_once('../misc/teamStorage.php');
include_once('../misc/redirect.php');
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
$success = false;

if (count($_POST) > 0) {
  if(validate($_POST, $data, $errors)) {
    $find = $teamStorage->findOne(['name' => $data['name']]);

    if(isset($find)) {
      $errors['name'] = "Ilyen nevű csapat már létezik!";
    } else {
      $success = true;
      $team['name'] = $data['name'];
      $team['city'] = $data['city'];
      $team['members'] = explode(',', $data['members']);
      
      $teamStorage->add($team);
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
  
  <title>ELStadion - Csapatregisztráció</title>
</head>


<body>
<?php headerHTML(); ?>
  <div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center pt-5">
       <div class="col-md-6 col-lg-4 col-xl-4">
          <div class="card">
             <div class="card-body">
                <div class="text-center m-auto">
                   <h4 class="text-center">Csapatregisztráció</h4>
                </div>

                <?php if($success) : ?>
                  <div class="mb-2">
                    <p><?= $_POST['name']?> csapat sikeresen regisztrálva!</p>
                  </div>
                <?php endif ?>

                <form action="" method="post" novalidate>
                  <div class="form-group mb-2">
                    <label for="name">Csapatnév</label>
                    <input type="text" name="name" placeholder="ELTE FC" class="form-control" value="<?= $success ? '' : ($_POST['name'] ?? '') ?>">
                    <?php if(isset($errors['name'])) : ?>
                      <span class="hiba"><?= $errors['name'] ?></span>
                    <?php endif ?>
                  </div>
                  <div class="form-group mb-2">
                    <label for="city">Képviselt település</label>
                    <input type="text" name="city" placeholder="Budapest" class="form-control" value="<?= $success ? '' : ($_POST['city'] ?? '') ?>">
                    <?php if(isset($errors['city'])) : ?>
                      <span class="hiba"><?= $errors['city'] ?></span>
                    <?php endif ?>
                  </div>
                  <div class="form-group mb-2">
                    <label for="members">Csapattagok</label>
                    <input type="text" name="members" placeholder="Béla,Jani,Kati,Emese" class="form-control" value="<?= $success ? '' : ($_POST['members'] ?? '') ?>">
                    <?php if(isset($errors['members'])) : ?>
                      <span class="hiba"><?= $errors['members'] ?></span>
                    <?php endif ?>
                  </div>

                  <div class="form-group mb-0 text-center">
                    <button class="btn btn-success btn-block" type="submit" name="submit">Csapat hozzáadása</button>
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
</body>
</html>