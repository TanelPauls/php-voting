<?php
include_once("init_tables.php");

$result = mysqli_query($mysqli, "SELECT URL, H_alguse_aeg FROM PILDID");
$images = [];
while ($row = mysqli_fetch_assoc($result)) {
    $images[] = [
        'url' => $row['URL'],
        'start_time' => $row['H_alguse_aeg']
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
        <button class="guess-button" onclick="submitGuess('AI')">AI</button>
        <button class="guess-button" onclick="submitGuess('Päris')">Päris</button>
      </div>

	  <div class="vote-message-row" id="vote-message-row">
  		<span id="vote-message"></span>
  		<button id="start-vote-btn" class="guess-button" onclick="startVote()">Alusta</button>
	  </div>
	  <p id="vote-timer"></p>

      <div class="guess-info">
        <p>Kõik kasutajate arvamused:</p>
        <p>AI - 3 &nbsp;&nbsp;&nbsp; Päris - 7</p>
        <p>Tegelikult on see pilt: <span class="correct-answer">Päris</span></p>
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
  </body>
  <script>
  const images = <?php echo json_encode($images); ?>;
  let currentIndex = 0;
  let voterName = "";
  let pendingVote = null;

  const imgElement = document.querySelector(".guess-image");

  function showImage(index) {
    imgElement.src = images[index];
  }

  function nextImage() {
    currentIndex = (currentIndex + 1) % images.length;
    showImage(currentIndex);
  }

  function prevImage() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    showImage(currentIndex);
  }

  function openModal() {
    document.getElementById("nameModal").style.display = "block";
  }

  function closeModal() {
    document.getElementById("nameModal").style.display = "none";
  }

  function submitGuess(choice) {
  if (!voterName) {
    pendingVote = choice;
    openModal();
    return;
  }

  alert(`Valisid: ${choice} (${voterName})`);
  }

  function showImage(index) {
  imgElement.src = images[index].url;

  const voteMessage = document.getElementById("vote-message");
  const voteTimer = document.getElementById("vote-timer");
  const startButton = document.getElementById("start-vote-btn");

  voteMessage.textContent = "";
  voteTimer.textContent = "";
  startButton.style.display = "none";

  const startTimeStr = images[index].start_time;

  if (!startTimeStr) {
    voteMessage.textContent = "Hääletus pole veel alanud.";
    startButton.style.display = "inline-block";
    return;
  }

  const startTime = new Date(startTimeStr.replace(' ', 'T')); // Safe ISO format
  const now = new Date();
  const elapsed = (now - startTime) / 1000;

  if (elapsed > 5 * 60) {
    voteMessage.textContent = "Hääletus lõppenud.";
    return;
  }

  voteMessage.textContent = "Hääletus käib";
  updateTimer(300 - elapsed);
}


let voteCountdown;

function updateTimer(secondsLeft) {
  clearInterval(voteCountdown);

  function formatTime(s) {
    const m = Math.floor(s / 60);
    const s2 = Math.floor(s % 60);
    return `${m}:${s2.toString().padStart(2, '0')}`;
  }

  const voteTimer = document.getElementById("vote-timer");

  voteCountdown = setInterval(() => {
    if (secondsLeft <= 0) {
      clearInterval(voteCountdown);
      voteTimer.textContent = "Hääletus lõppenud.";
      document.getElementById("vote-message").textContent = "Hääletus lõppenud.";
      return;
    }

    voteTimer.textContent = "Aega jäänud: " + formatTime(secondsLeft);
    secondsLeft--;
  }, 1000);
}


  function confirmName() {
  const eesnimi = document.getElementById("eesnimi").value.trim();
  const perenimi = document.getElementById("perenimi").value.trim();

  if (!eesnimi || !perenimi) {
    alert("Palun sisesta nii eesnimi kui perenimi.");
    return;
  }

  // Send to database
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

      if (pendingVote) {
        alert(`Valisid: ${pendingVote} (${voterName})`);
        pendingVote = null;
      }
    } else {
      alert("Viga kasutaja salvestamisel: " + response);
    }
  })
  .catch(err => {
    alert("Võrguviga kasutaja salvestamisel.");
  });
}


  window.onload = function () {
    showImage(currentIndex);
  };
</script>

</html>
