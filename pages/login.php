<?php
include_once('../misc/auth.php');
include_once('../misc/userStorage.php');
include_once('../misc/redirect.php');
include('./header.php');

function validate($p, &$data, &$errors) {
  //Username
  if(!isset($p['username']) || trim($p['username']) === "") {
		$errors['username'] = "Felhasználónév megadása kötelező!";
	} else {
		$data['username'] = $p['username'];
	}

  //Password
  if(!isset($p['password']) || trim($p['password']) === "") {
		$errors['password'] = "Jelszó megadása kötelező!";
	} else {
    $data['password'] = $p['password'];
  }

  return count($errors) === 0;
}

$errors = [];
$data = [];

if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "Hibás felhasználónév vagy jelszó!";
    } else {
      $auth->login($auth_user);
      redirect('./index.php');
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
  <title>ELStadion - Bejelentkezés</title>
</head>

<body>
  <?php headerHTML() ?>
  <div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center pt-5">
       <div class="col-md-6 col-lg-4 col-xl-4">
          <div class="card">
             <div class="card-body">
                <div class="text-center m-auto">
                   <h4 class="text-center">Bejelentkezés</h4>
                </div>

                <?php if(isset($errors['global'])) : ?>
                  <span class="hiba"><?= $errors['global'] ?></span>
                <?php endif ?>

                <form action="" method="post" novalidate>
                   <div class="form-group mb-2">
                      <label for="username">Felhasználónév</label>
                      <input type="text" name="username" placeholder="ELTEUser456" class="form-control" value="<?= $_POST['username'] ?? '' ?>">
                      <?php if(isset($errors['username'])) : ?>
                        <span class="hiba"><?= $errors['username'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-2">
                      <label for="password">Jelszó</label>
                      <input type="password" class="form-control" id="password" name="password" placeholder="Miau?CicaLover#873!">
                      <?php if(isset($errors['password'])) : ?>
                        <span class="hiba"><?= $errors['password'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-0 text-center">
                      <button class="btn btn-success btn-block" type="submit" name="submit">Bejelentkezés</button>
                   </div>
                   
                </form>
             </div>
          </div>
       </div>
    </div>
  </div>
</body>
</html>
<?php if(isset($_POST['password'])) : ?>
  <script id="writePW">
    document.querySelector("#password").value = "<?= $data['password'] ?>"
    document.querySelector("#writePW").remove()
  </script>
<?php endif ?>