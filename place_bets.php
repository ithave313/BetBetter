<!DOCTYPE html>
<html>
<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
	<h1 style="text-align:center; color:#34B1E2; background-color:#1d2951; font-family:'Georgia'">BetBetter</h1>	
</head>

<body>
	<div class="sidenav">
		<a href="main.php">Home</a><br>
		<a href="your_bets.php">Your Bets</a><br>
		<a href="add_game.php">Add Games</a> <br>
		<a href="hedging.php">Hedging Calculator</a> <br>
		<a href="index.php">Logout</a> <br>
	</div>
</body>

<form action="main.php" method="post">
<br><center><h3>Your bet has been placed!</h3><center>

<?php
	//link to database
	$link = mysqli_connect('localhost', 'aalberti', 'nd') or die ('Could not connect to DB' . mysql_error());
	mysqli_select_db($link, 'aalberti');
	session_start();
	$amount = $_SESSION['amount'];
	$team = $_SESSION['team'];
	$week = $_SESSION['week'];

?>
<br><center>Your bet is: <center>
$<?=$amount?> betted on the <?=$team?> during NFL Week <?=$week?><br><br>

<br><center>May the odds be ever in your favor.<center>




<a href="main.php" >Place Another Bet</a>&emsp;
<a href="index.php" >Logout</a><br><br>

</form>
