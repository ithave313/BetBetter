<!DOCTYPE html>
<html>
<head>
	<title>BetBetter</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
	<h1 style="text-align:center; color:#34B1E2; background-color:#1d2951; font-family: 'Georgia'">BetBetter</h1>	
</head>

<body>
	<div class="sidenav">
		<a href="main.php" class="active">Home</a><br><br><br>
		<a href="your_bets.php">Your Bets</a><br><br><br>
		<a href="add_game.php">Add Games</a> <br><br><br>
		<a href="hedging.php">Hedging Calculator</a> <br><br><br>
		<a href="index.php">Logout</a> <br><br><br>
			<i class="fa fa-bars"></i>
	</div>
</body>


<form action="main.php" method="post">
<?php
	//link to database
	$link = mysqli_connect('localhost', 'aalberti', 'nd') or die ('Could not connect to DB' . mysql_error());
	mysqli_select_db($link, 'aalberti');
	session_start();
?>

<br>

<center><h3>This Week's Match Ups:</h3><center>
<select name="select" size="10" style="width: 500px;" HorizontalAlignment="Stretch" VerticalAlignment="Stretch" padding-right: 300px>
<?php
    if (($handle = fopen("predicted_matchups_week14.csv", "r")) !== FALSE) {
        fgetcsv($handle, 1000, ",");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            ?> 
            <option value="<?=$data[1], $data[6]?>"</option> 
            <?php
            if($data[5] == 1) {
                echo $data[2] ." at ". $data[1] ." - ".$data[1] ." (". $data[3] .")";
                 
            } else {
                echo $data[2] ." at ". $data[1] ." - ". $data[2] ." (". $data[3] .")";
            }
        }
    fclose($handle);
    }
?> 
</select>
<center><br><input type="submit" name="submit" value="Show Prediction"/></center>
</p>

<?php
	#To print Selected Match Up
	if (isset($_POST['select'])) {
        $choice = $_POST['select'];
		$prediction = $choice[(strlen($choice) - 1)];
        $choice = substr_replace($choice, "", -1);
    }
?>

<h3>Our Prediction: </h3>
<?php
    if(isset($prediction) && isset($choice) && array_key_exists('submit', $_POST)){
        if($prediction == 1){
            echo $choice ." will win \n";
        } else {
            echo $choice ." will lose \n";
        }
    }
?>
<br><br>

<?php
	if (array_key_exists('your_bets', $_POST)) {
		header("Location: your_bets.php");
		exit();
	}
?>	

<br>NFL Week Number:  <input type="textbox" name="week"/> <br>
<br>Team Name:  <input type="textbox" name="team"/> <br>
<br>Betting Amount ($):  <input type="textbox" name="amount"/> <br>

<br><br><center><input type="submit" name="place_bet" class="button" value="Place Your Bet!"><center></button>

<?php
	if (array_key_exists('place_bet', $_POST)) {
		make_bet($link);
	}

	function make_bet($link) {
		if ($_POST['week'] != NULL || $_POST['team'] != NULL || $_POST['amount'] != NULL) {
			$query = mysqli_query($link, 'select * from bets');
			$id = mysqli_num_rows($query) + 1;
			#echo $id;
				
			$name = $_SESSION['name'];
			$_SESSION['week'] = $_POST['week'];
			$_SESSION['team'] = $_POST['team'];
			$_SESSION['amount'] = $_POST['amount'];

			$stmt = mysqli_prepare($link, 'insert into date (id, bet_date) values (?, ?)');
			mysqli_stmt_bind_param($stmt, 'is', $id, date("Y-m-d"));
			mysqli_stmt_execute($stmt);

			$stmt = mysqli_prepare($link, 'insert into bets (id, user, week, team_name, amount) values (?, ?, ?, ?, ?)');
			mysqli_stmt_bind_param($stmt, 'isisd', $id, $name, $_POST['week'], $_POST['team'], $_POST['amount']);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			header("Location: place_bets.php");
			exit();

		} else {
			echo '<br> Please fill all categories.';
		}
	}
?>

</form>
</html>
