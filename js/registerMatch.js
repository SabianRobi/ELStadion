//if the registerMatch form sent with score values, php file will set the scores to null, so this is just for UX 

const dateObj = document.querySelector("input[name='date']");
const homeScore = document.querySelector("input[name='homeScore']");
const awayScore = document.querySelector("input[name='awayScore']");
dateObj.addEventListener('change', dateCheck);

function dateCheck() {
  if(dateObj.value === '') return;

  const dateGiven = new Date(dateObj.value);
  const dateNow = new Date();

  if(dateGiven > dateNow) {
    homeScore.disabled = true;
    homeScore.value = 0;
    awayScore.disabled = true;
    awayScore.value = 0;
  } else {
    homeScore.disabled = false;
    awayScore.disabled = false;
  }
}
dateCheck();