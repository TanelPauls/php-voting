<?php
include_once("init_tables.php");

$result = mysqli_query($mysqli, "SELECT * FROM KUSIMUSED");
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>MariaDB VALIMISED</title>
	<link rel="stylesheet" href="styles.css" />
	<style>
		.dropdown {
			width: 300px;
			max-height: 220px; /* approximately 10 options depending on font size */
			overflow-y: auto;
			border: 1px solid #ccc;
			padding: 5px;
			font-size: 16px;
		}
		select {
			width: 100%;
			font-size: 16px;
			padding: 5px;
		}
	</style>
</head>
<body>
	<h1>Küsimused</h1>

	<div class="dropdown">
		<select size="10"> <!-- visible options without opening dropdown -->
			<?php while ($row = mysqli_fetch_assoc($result)): ?>
				<option value="<?= htmlspecialchars($row['id']) ?>">
					<?= htmlspecialchars($row['Kusimus']) ?>
				</option>
			<?php endwhile; ?>
		</select>
	</div>
</body>
</html>
