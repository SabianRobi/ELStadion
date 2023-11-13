<?php
include_once('../misc/auth.php');
include_once('../misc/userStorage.php');

session_start();
$auth = new Auth(new UserStorage());
$authenticated_user = $auth->authenticated_user();

function headerHTML(){
  global $auth;
  global $authenticated_user;

 echo('
  <header>
  <nav class="kozepre navbar navbar-expand-sm navbar-light">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <a class="navbar-brand" href="./index.php">
          <img src="../img/icon.png" atl="Logó" width="30" height="30" class="d-inline-block align-top">ELStadion
        </a>
        
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item mr-1">
            <a class="nav-link inactive" aria-current="page" href="./index.php">Kezdőlap</a>
          </li>');
          if($auth->authorize(['admin'])) {
            echo('<li class="nav-item">
              <div class="dropdown nav-link inactive" aria-current="page">
                <span class="dropdown-toggle mb-0 p-2" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Új</span>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="dropdownList">
                  <li><a class="dropdown-item" href="./addMatch.php">Új mérkőzés rögzítése</a></li>
                  <li><a class="dropdown-item" href="./addTeam.php">Új csapat hozzáadása</a></li>
                </ul>
              </div>
            </li>');
            } echo('

          <li class="nav-item">
            <div class="dropdown nav-link inactive" aria-current="page">
              <span class="dropdown-toggle mb-0 p-2" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">Fiók</span>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="dropdownList">');
              if($auth->is_authenticated()) {
                echo('<p class="dropdown-item disabled" id="username">Üdv, '.$authenticated_user['username'].'!</p>'.
                '<li><a class="dropdown-item" href="./logout.php">Kijelentkezés</a></li>');
              } else {
                echo('<li><a class="dropdown-item" href="./login.php">Bejelentkezés</a></li>
                <li><a class="dropdown-item" href="./register.php">Regisztráció</a></li>');
              } echo('
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div id="borito"></div>
</header>
');
};
?>

