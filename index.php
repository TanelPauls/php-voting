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
        <button class="guess-button">AI</button>
        <button class="guess-button">Päris</button>
      </div>

      <div class="guess-info">
        <p>Teiste inimeste arvamused:</p>
        <p>AI - 3 &nbsp;&nbsp;&nbsp; Päris - 7</p>
        <p>Tegelikult on see pilt:</p>
        <p class="correct-answer">Päris</p>
      </div>
    </div>
  </body>
  <script>
    const images = <?php echo json_encode($imageUrls); ?>;

    let currentIndex = 0;
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
    window.onload = () => showImage(currentIndex);
  </script>
</html>
