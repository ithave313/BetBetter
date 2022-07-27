<!DOCTYPE html>
<html>
<head>
    <title>Add Game</title>
    <link rel="stylesheet" href="main.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
	<h1 style="text-align:center; color:#34B1E2; background-color:#1d2951; font-family:'Georgia'">BetBetter</h1>	
<form action="add_game.php" method="post">

<body>
	<div class="sidenav">
		<a href="main.php">Home</a><br><br><br>
		<a href="your_bets.php">Your Bets</a><br><br><br>
		<a href="add_game.php" class="active">Add Games</a> <br><br><br>
		<a href="hedging.php">Hedging Calculator</a> <br><br><br>
		<a href="index.php">Logout</a> <br><br><br>
	</div>
</body>

<center><h3>Add New Game</h3><center>
</head>

Date (D/M/YYYY): <input type="textbox" name="date"/></a><br><br>
NFL Season: <input type="textbox" name="season"/><br><br>
NFL Schedule Week: <input type="textbox" name="week"/><br><br>
Home Team: <input type="textbox" name="home_team"/><br><br>
Away Team: <input type="textbox" name="away_team"/><br><br>
Home Team's Score: <input type="textbox" name="hscore"/><br><br>
Away Team's Score: <input type="textbox" name="ascore"/><br><br>
<input type="submit" name="add" class="button" value="Add Game"></button>
</form>

<body>
<?php

	$link = mysqli_connect('localhost', 'aalberti', 'nd') or die ('Could not connect to DB' . mysql_error());
	mysqli_select_db($link, 'aalberti');
 
	if (array_key_exists('add', $_POST)) {
		add($link);
	}
	
	function add($link) {
		if ($_POST['date'] == NULL || $_POST['season'] == NULL || $_POST['week'] == NULL || $_POST['home_team'] == NULL || $_POST['away_team'] == NULL || $_POST['hscore'] == NULL || $_POST['ascore'] == NULL) {
			echo '<br> Please enter all fields.';
		} else {
			$stmt = mysqli_prepare($link, 'insert into spreadspoke_scores (schedule_date, schedule_season, schedule_week, team_home, score_home, score_away, team_away) values (?, ?, ?, ?, ?, ?, ?)');
			mysqli_stmt_bind_param($stmt, 'siisiis', $_POST['date'], $_POST['season'], $_POST['week'], $_POST['home_team'], $_POST['away_team'], $_POST['hscore'], $_POST['ascore']);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			echo '<br> New Game Added';	
		}
	}

?>

</body>
</html>
