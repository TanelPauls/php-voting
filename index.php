<?php
include_once("init_tables.php");

$result = mysqli_query($mysqli, "SELECT * FROM KUSIMUSED");
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>VALIMISED</title>
    <link rel="stylesheet" href="styles.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;700&display=swap"
      rel="stylesheet"
    />
</head>
<body>
    <h1>VALIMISED !</h1>

    <div class="container">
        <?php if (mysqli_num_rows($result) === 0): ?>
            <p>Hetkel pole ühtegi küsimust saadaval.</p>
        <?php else: ?>
            <div class="dropdown">
                <select size="10">
                    <?php $first = true; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?= htmlspecialchars($row['Kusimus_id']) ?>" <?= $first ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['Kusimus']) ?>
                        </option>
                        <?php $first = false; ?>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
