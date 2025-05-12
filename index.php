<?php
include_once("init_tables.php");

$result = mysqli_query($mysqli, "SELECT URL, Oige_vastus FROM PILDID");
$images = [];
while ($row = mysqli_fetch_assoc($result)) {
    $images[] = [
        'url' => $row['URL'],
        'correct' => $row['Oige_vastus']
    ];
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kas on tegu AI või päris pildiga?</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
<div class="container">
  <div class="image-wrapper">
    <button class="nav-button left" onclick="prevImage()">⟨</button>
    <img src="" alt="Guess if this is AI or Real" class="guess-image" />
    <button class="nav-button right" onclick="nextImage()">⟩</button>
  </div>

  <div class="button-group">
    <button id="ai-button" class="guess-button" onclick="submitGuess('AI')">AI</button>
    <button id="real-button" class="guess-button" onclick="submitGuess('Päris')">Päris</button>
  </div>

  <p id="vote-timer"></p>

  <div class="guess-info">
    <p>Kõik kasutajate arvamused:</p>
    <p id="vote-counts">AI - 0     Päris - 0     <span id="next-update">(Uuendamine 5s...)</span></p>
    <p>Tegelikult on see pilt: <span class="correct-answer"></span></p>
  </div>
</div>

<div id="nameModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Enne hääletamist sisesta oma nimi</h2>
    <div class="modal-fields">
      <input type="text" id="eesnimi" placeholder="Eesnimi" />
      <input type="text" id="perenimi" placeholder="Perenimi" />
    </div>
    <button onclick="confirmName()">Alusta hääletamist</button>
  </div>
</div>

<script>
const images = <?php echo json_encode($images); ?>;
let currentIndex = 0;
let voterName = "";
let pendingVote = null;
let voteCountdown;
const updateInterval = 5;
let secondsLeft = updateInterval;
const imgElement = document.querySelector(".guess-image");

function showImage(index) {
  clearInterval(voteCountdown);
  imgElement.src = images[index].url;
  document.getElementById("vote-timer").textContent = "";
  document.querySelector(".correct-answer").textContent = "";

  if (!voterName) return;

  fetch("get_vote_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name: voterName, imageIndex: currentIndex })
  })
    .then(res => res.json())
    .then(data => {
      const correctAnswerEl = document.querySelector(".correct-answer");

      if (data.status === "started") {
        const startTime = new Date(data.start_time.replace(' ', 'T'));
        const now = new Date();
        let elapsed = (now - startTime) / 1000;
        elapsed = Math.max(0, elapsed);

        if (elapsed < 300) {
          correctAnswerEl.textContent = "Oota hääletuse lõpuni";
          updateTimerUI(300 - elapsed);
        } else {
          correctAnswerEl.textContent = images[index].correct === "Paris" ? "Päris" : "AI";
          disableVoteButtons();
        }
      } else {
        correctAnswerEl.textContent = "Oota hääletuse algust";
      }
    });
}

function updateTimerUI(secondsLeft) {
  const voteTimer = document.getElementById("vote-timer");

  function formatTime(s) {
    const m = Math.floor(s / 60);
    const s2 = Math.floor(s % 60);
    return `${m}:${s2.toString().padStart(2, '0')}`;
  }

  voteTimer.textContent = formatTime(secondsLeft) + " aega, et muuta arvamust";

  clearInterval(voteCountdown);
  voteCountdown = setInterval(() => {
    secondsLeft--;
    if (secondsLeft <= 0) {
      clearInterval(voteCountdown);
      disableVoteButtons();
    } else {
      voteTimer.textContent = formatTime(secondsLeft) + " aega, et muuta arvamust";
    }
  }, 1000);
}

function disableVoteButtons() {
  document.getElementById("ai-button").disabled = true;
  document.getElementById("real-button").disabled = true;
  document.getElementById("vote-timer").textContent = "Aeg on läbi.";
}

function nextImage() {
  currentIndex = (currentIndex + 1) % images.length;
  showImage(currentIndex);
  fetchResults();
}

function prevImage() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  showImage(currentIndex);
  fetchResults();
}

function submitGuess(choice) {
  if (!voterName) {
    pendingVote = choice;
    openModal();
    return;
  }

  fetch("get_vote_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name: voterName, imageIndex: currentIndex })
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === "started") {
        sendGuess(choice);
      } else {
        const dbFriendlyChoice = (choice === "Päris") ? "Paris" : choice;
        fetch("start_user_vote.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ name: voterName, imageIndex: currentIndex, choice: dbFriendlyChoice })
        })
          .then(res => res.text())
          .then(resp => {
            if (resp === "OK") {
              alert(`Sinu hääl on salvestatud: ${choice}`);
              showImage(currentIndex);
            } else if (resp === "Already started") {
              sendGuess(choice);
            } else {
              alert("Viga hääletuse alustamisel.");
            }
          });
      }
    });
}

function sendGuess(choice) {
  const dbChoice = (choice === "Päris") ? "Paris" : choice;

  fetch("submit_guess.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name: voterName, imageIndex: currentIndex, choice: dbChoice })
  })
    .then(res => res.text())
    .then(response => {
      if (response === "OK") {
        alert(`Sinu hääl on salvestatud: ${choice}`);
        showImage(currentIndex);
        fetchResults();
      } else if (response === "TOO_LATE") {
        alert("Aeg selle pildi hääletamiseks on läbi.");
        showImage(currentIndex);
      } else {
        alert("Viga salvestamisel: " + response);
      }
    })
    .catch(() => {
      alert("Võrguviga hääletamisel.");
    });
}

function openModal() {
  document.getElementById("nameModal").style.display = "block";
}

function closeModal() {
  document.getElementById("nameModal").style.display = "none";
}

function confirmName() {
  const eesnimi = document.getElementById("eesnimi").value.trim();
  const perenimi = document.getElementById("perenimi").value.trim();

  if (!eesnimi || !perenimi) {
    alert("Palun sisesta nii eesnimi kui perenimi.");
    return;
  }

  fetch("register_user.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ eesnimi, perenimi })
  })
    .then(res => res.text())
    .then(response => {
      if (response === "OK") {
        voterName = `${eesnimi} ${perenimi}`;
        closeModal();
        showImage(currentIndex);
        if (pendingVote) {
          submitGuess(pendingVote);
          pendingVote = null;
        }
      } else {
        alert("Viga kasutaja salvestamisel: " + response);
      }
    })
    .catch(() => {
      alert("Võrguviga kasutaja salvestamisel.");
    });
}

function fetchResults() {
  fetch("get_results.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ imageIndex: currentIndex })
  })
    .then(res => res.json())
    .then(data => {
      if (data && typeof data.AI !== 'undefined') {
        document.getElementById("vote-counts").innerHTML =
          `AI - ${data.AI}     Päris - ${data.Paris}     <span id="next-update">(Uuendamine ${updateInterval}s...)</span>`;
        secondsLeft = updateInterval;
      }
    });
}

function startAutoUpdate() {
  setInterval(() => {
    secondsLeft--;
    if (secondsLeft <= 0) {
      fetchResults();
    } else {
      const updateSpan = document.getElementById("next-update");
      if (updateSpan) updateSpan.textContent = `(Uuendamine ${secondsLeft}s...)`;
    }
  }, 1000);
}

window.onload = function () {
  showImage(currentIndex);
  fetchResults();
  startAutoUpdate();
};
</script>
</body>
</html>
