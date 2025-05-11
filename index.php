<?php
include_once("init_tables.php");

$result = mysqli_query($mysqli, "SELECT URL FROM PILDID");
$imageUrls = [];
while ($row = mysqli_fetch_assoc($result)) {
    $imageUrls[] = $row['URL'];
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

      <div class="guess-info">
        <p>Kõik arvamused:</p>
        <p>AI - 3 &nbsp;&nbsp;&nbsp; Päris - 7</p>
        <p>Tegelikult on see pilt:</p>
        <p class="correct-answer">Päris</p>
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
  const images = <?php echo json_encode($imageUrls); ?>;
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
