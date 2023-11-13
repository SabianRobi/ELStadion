<?php
include_once('../misc/teamStorage.php');
include_once('../misc/matchStorage.php');
include_once('../misc/userStorage.php');
include_once('../misc/auth.php');
include('./header.php');
include('./getMatches.php');

$teamStorage = new TeamStorage();
$matchStorage = new MatchStorage();
$auth = new Auth(new UserStorage());
$user = $auth->authenticated_user();

$teams = $teamStorage->findAll();

function isFavourite($teamId) {
  global $user;
  return in_array($teamId, $user['favourites']);
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <title>ELStadion - Főoldal</title>
</head>

<body>
  <?php headerHTML(); ?>
  <div class="row">
    <div class="col-12 p-0">
      <div class="kozepre content p-4">
        <h1>Üdvözöllek az ELStadion weboldalján!</h1>
        <p>Hosszú évek után, boldogan jelentjük be, hogy az Eötvös Loránd Stadion idén megnyitotta kapuit a nagyközönség számára is. Eljött a várva várt pillanat, amikor végre beülhet kedvenc csapata mérkőzésire, együtt drukkolhat a többiekkel. Az újonan megnyitott ELStadion a világ legmodernebb stadionok közé tartozik: hipergyors kiszolgálás a étkezőkben, nincs várakozás a mosdókhoz, kényelmes ülőszékek kartámlákkal, hatalmas kivetítőkkel, a legmodernebb technikai felszereléssel és mindezt megtetőzi a biztonságos, gigabites sávszélességű ingyenes WiFi szolgáltatás az egész stadion területén. Mire vár, jöjjön el és nézze meg kedvenc csapatának meccsét egy jó hideg itallal!</p>
      
        <h2>Csapatok</h2>
        <div class="row" id="teams">
          <?php foreach ($teams as $team) : ?>
          <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="teamcard">
              <a href="teamDetails.php?id=<?= $team['id'] ?>">
                <h3><?= $team['name'] ?></h3>
                <h4><?= $team['city'] ?></h4>
                <p>
                  <?php $players = implode(', ', $team['members']);
                  echo($players); ?>
                </p>
              </a>
              <?php if($auth->authorize(['admin'])) : ?>
              <div class="modify">
                <a href="./editTeam.php?id=<?=$team['id']?>" class="delete"><img src="../img/edit.webp" alt="Szerkesztés" height="20" width="20"></a>
              </div>
              <?php endif ?>
              <?php if($auth->is_authenticated()) : ?>
                <div class="fav">
                  <a href="./toggleFavourite.php?teamId=<?=$team['id']?>" class="favourite"><img src="<?= isFavourite($team['id']) ? '../img/fav_filled.webp' : '../img/fav.webp' ?>" alt="Kedvenc állítása"  height="25" width="25"></a>
                </div>
              <?php endif ?>
            </div>
          </div>
          <?php endforeach ?>
        </div>
        
        <div id="pastMatches"></div>
        <button class="btn btn-success btn-block" id="loadMorePast">Továbbiak betöltése</button>
        
        <div id="plannedMatches"></div>
        <p class="btn btn-success btn-block" id="loadMorePlanned">Továbbiak betöltése</p>

      </div>
    </div>
  </div>
  <script src="../js/loadMatches.js"></script>
</body>
</html>