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

  //Email
	if (!isset($p['email']) || trim($p['email']) === "") {
    $errors['email'] = "Email cím megadása kötelező!";
  } else if (!filter_var($p['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Hibás email formátum!";
  } else {
    $data['email'] = $p['email'];
  }

  //Password
  if(!isset($p['password']) || trim($p['password']) === "") {
		$errors['password'] = "Jelszó megadása kötelező!";
	} else

  //Repassword
  if(!isset($p['repassword']) || trim($p['repassword']) === "") {
		$errors['repassword'] = "Jelszó mégegyszeri megadása kötelező!";
	} else if(strcmp($p['password'], $p['repassword']) != 0) {
    $errors['repassword'] = "A két jelszó nem egyezik!";
	} else {
    $data['password'] = $p['password'];
  }

  return count($errors) === 0;
}

$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$errors = [];
$data = [];

if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
      $errors['username'] = "A felhasználónév már foglalt!";
    } else {
      $auth->register($data);
      redirect('login.php');
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
  
  <title>ELStadion - Regisztráció</title>
</head>


<body>
<?php headerHTML(); ?>
  <div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center pt-5">
       <div class="col-md-6 col-lg-4 col-xl-4">
          <div class="card">
             <div class="card-body">
                <div class="text-center m-auto">
                   <h4 class="text-center">Regisztráció</h4>
                </div>

                <form action="" method="post" novalidate>
                   <div class="form-group mb-2">
                      <label for="username">Felhasználónév</label>
                      <input type="text" name="username" placeholder="ELTEUser456" class="form-control" value="<?= $_POST['username'] ?? '' ?>">
                      <?php if(isset($errors['username'])) : ?>
                        <span class="hiba"><?= $errors['username'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-2">
                      <label for="email">Email</label>
                      <input type="email" name="email" placeholder="user456@elte.hu" class="form-control" value="<?= $_POST['email'] ?? '' ?>">
                      <?php if(isset($errors['email'])) : ?>
                        <span class="hiba"><?= $errors['email'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-2">
                      <label for="password">Jelszó</label>
                      <input type="password" class="form-control" id="password" name="password"  placeholder="Miau?CicaLover#873!">
                      <?php if(isset($errors['password'])) : ?>
                        <span class="hiba"><?= $errors['password'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-2">
                      <label for="password">Jelszó mégegyszer</label>
                      <input type="password" class="form-control" id="repassword" name="repassword"  placeholder="Miau?CicaLover#873!">
                      <?php if(isset($errors['repassword'])) : ?>
                        <span class="hiba"><?= $errors['repassword'] ?></span>
                      <?php endif ?>
                   </div>

                   <div class="form-group mb-0 text-center">
                      <button class="btn btn-success btn-block" type="submit" name="submit">Regisztráció</button>
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
    document.querySelector("#password").value = "<?= $_POST['password'] ?>"
    document.querySelector("#writePW").remove()
  </script>
<?php endif ?>
<?php if(isset($_POST['repassword'])) : ?>
  <script id="rewritePW">
    document.querySelector("#repassword").value = "<?= $_POST['repassword'] ?>"
    document.querySelector("#rewritePW").remove()
  </script>
<?php endif ?>