<?php
include_once('../misc/teamStorage.php');
include_once('../misc/matchStorage.php');
include_once('../misc/userStorage.php');
include_once('../misc/auth.php');

$teamStorage = new TeamStorage();
$matchStorage = new MatchStorage();
$auth = new Auth(new UserStorage());
$drawnCards = 0;

function readableDate($date) {
  $dateTime = explode('T', $date);
  $newDate = implode('.', explode('-', $dateTime[0])); //2021.12.24
  return $newDate." ".$dateTime[1];
}

function getMatches($fromPast = true, $teamId = null, $from = 0, $count = -1, $order = SORT_ASC, $team = null, $wholeHTML = true) {
  global $drawnCards;
  global $matchStorage;
  global $teamStorage;
  global $auth;
  $matches = null;

  //Get matches
  if($fromPast) {
    if ($teamId === null) {
      $matches =
      $matchStorage->findMany(function ($match) {
        return $match['homeScore'] !== null && $match['awayScore'] !== null;
      });
    } else {
      $matches =
      $matchStorage->findMany(function ($match) use ($teamId) {
        return($match['homeScore'] !== null && $match['awayScore'] !== null) &&
        ($match['home'] === $teamId || $match['away'] === $teamId);
      });
    }
  } else {
    if($teamId === null) {
      $matches =
      $matchStorage->findMany(function ($match) {
        return $match['homeScore'] === null && $match['awayScore'] === null;
      });
    } else {
      $matches =
      $matchStorage->findMany(function ($match) use ($teamId) {
        return($match['homeScore'] === null && $match['awayScore'] === null) &&
        ($match['home'] === $teamId || $match['away'] === $teamId);
      });
    }
  }

  //Sort the matches by date
  if($order === SORT_ASC) {
    usort($matches, function($a, $b) {
      return $b['date'] <=> $a['date'];
    });
  } else {
    usort($matches, function($a, $b) {
      return $a['date'] <=> $b['date'];
    });
  }

  //Get the first $count of matches
  if($count >= 0) $matches = array_slice($matches, $from, $count);

  //Write out the 'cards'
  $cards = "";
  if($wholeHTML) {
  $cards .= '
    <h2>'. ($fromPast ? 'Eddigi mérkőzések' : 'Tervezett mérkőzések').'</h2>';
    $cards .= '<div class="row">'; }
      if(count($matches) === 0) $cards .= '<p>Összes meccs betöltve!</p>';
      foreach ($matches as $match) {
        $drawnCards++;
        $homeTeam = $teamStorage->findById($match['home']);
        $awayTeam = $teamStorage->findById($match['away']);
        $winner = $match['homeScore'] > $match['awayScore'] ? $match['home'] : ($match['homeScore'] === $match['awayScore'] ? null : $match['away']);

        $cards .= '
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="row matchcard">
            <div class="col-5">
            <h3 class="'.(isset($match['homeScore']) ? ($winner===$homeTeam['id']?'winner':($winner === null?'draw':'loser')):'').'"><a href="./teamDetails.php?id='.$homeTeam['id'].'">'.$homeTeam['name'].'</a></h3>
              <h4>'.$homeTeam['city'].'</h4>
              <h3>'.$match['homeScore'].'</h3>
            </div>
            <div class="col-2">
              <br><p>vs</p>
            </div>
            <div class="col-5">
              <h3 class="'.(isset($match['awayScore']) ? ($winner===$awayTeam['id']?'winner':($winner === null?'draw':'loser')):'').'"><a href="./teamDetails.php?id='.$awayTeam['id'].'">'.$awayTeam['name'].'</a></h3>
              <h4>'.$awayTeam['city'].'</h4>
              <h3>'.$match['awayScore'].'</h3>
            </div>
            <p>'.readableDate($match['date']).'</p>';
    
            if(isset($_SESSION['user']) ? in_array("admin", $_SESSION['user']['roles']) : false) { $cards .= '
            <div class="modify">
              <a href="./editMatch.php?matchId='.$match['id'].(isset($team) ? '&fromTeam='.$team : '').'" class="edit"><img src="../img/edit.webp" alt="Edit" height="20" width="20"></a>
              <a href="./deleteMatch.php?matchId='.$match['id'].(isset($team) ? '&fromTeam='.$team : '&fromTeam=').'" class="delete"><img src="../img/delete.webp" alt="Delte" height="20" width="20"></a>
            </div>';
            } $cards .= '
            
          </div>
        </div>';
      } $cards .= '
    </div>';

    if($wholeHTML) { echo($cards); }
    else { return $cards; }
}

//load the next 5 matches
if(isset($_GET['ajax'])) {
  session_start();
  global $drawnCards;

  $sort = (bool)$_GET['fromPast'] ? SORT_ASC : SORT_DESC;
  $matches = getMatches((bool)$_GET['fromPast'], null, (int)$_GET['from'], 5, $sort, null, false);
  echo($drawnCards."SEPARATOR".$matches); //give back the count of the cards & cards
}
?>