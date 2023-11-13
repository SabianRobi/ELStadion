const paM = document.querySelector('#pastMatches');
const plM = document.querySelector('#plannedMatches');
const div = document.createElement("div");
div.classList.add("row");

const loadMorePastObj = document.querySelector('#loadMorePast');
const loadMorePlannedObj = document.querySelector('#loadMorePlanned');

loadMorePastObj.addEventListener('click', loadMorePast);
loadMorePlannedObj.addEventListener('click', loadMorePlanned);

let loadedPast = 0;
let loadedPlanned = 0;

async function loadMorePast() {
  if(!paM.hasChildNodes()) {
    let h2 = document.createElement('h2');
    h2.innerHTML = "Eddigi mérkőzések";
    paM.appendChild(h2);
    paM.appendChild(div.cloneNode());
  }
  const pastMatchesObj = document.querySelector('#pastMatches div[class="row"]');

  const response = await fetch(`getMatches.php?ajax=true&fromPast=1&from=${loadedPast}`);
  const matches = await response.text();
  let realMatches = matches.split('SEPARATOR');

  pastMatchesObj.innerHTML += realMatches[1];
  loadedPast += parseInt(realMatches[0]);

  if(realMatches[0] < 5) {
    loadMorePastObj.remove();
  }
}

async function loadMorePlanned() {
  if(!plM.hasChildNodes()) {
  let h2 = document.createElement('h2');
    h2.innerHTML = "Tervezett mérkőzések";
    plM.appendChild(h2);
    plM.appendChild(div.cloneNode());
  }
  const plannedMatchesObj = document.querySelector('#plannedMatches div[class="row"]');

  const response = await fetch(`getMatches.php?ajax=true&fromPast=0&from=${loadedPlanned}`);
  const matches = await response.text();
  let realMatches = matches.split('SEPARATOR');

  plannedMatchesObj.innerHTML += realMatches[1];
  loadedPlanned += parseInt(realMatches[0]);

  if(realMatches[0] < 5) {
    loadMorePlannedObj.remove();
  }
}

loadMorePast();
loadMorePlanned();