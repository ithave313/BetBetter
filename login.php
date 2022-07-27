<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
	<h1 style="text-align:center; color:#34B1E2">BetBetter</h1>
</head>
<body>




<form action="login.php" method="post">
<center>
<b>Sign In</b><br><br>
Username: <input type="textbox" name="username"/> <a href="delete_account.php" >Delete Your Account</a><br><br>
Password: <input type="password" name="password"/> <a href="change_password.php" >Change Your Password</a><br><br>
<input type="submit" name="login" class="button" value="LOGIN"></button>
<center>
</form>


<?php
	//link database
	$link = mysqli_connect('localhost', 'aalberti', 'nd') or die ('Could not connect to DB' . mysql_error());
	mysqli_select_db($link, 'aalberti');
	session_start();
	if(array_key_exists('login', $_POST)) {
		login($link);
	}

	function login($link) {
		if ($_POST['username'] != null) {
			$_SESSION['name'] = $_POST['username'];
			if ($_POST['password'] != null) {
            	$username = $_POST['username'];
				$stmt = mysqli_prepare($link, 'select username from betbetter_users where username = (?)');
				mysqli_stmt_bind_param($stmt, 's', $username);
				mysqli_stmt_execute($stmt);
				$username_query = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($username_query) > 0) {
					$stmt = mysqli_prepare($link, 'select password, isAdmin from betbetter_users where username = (?)');
					mysqli_stmt_bind_param($stmt, 's', $username);
					mysqli_stmt_execute($stmt);
					$table = mysqli_stmt_get_result($stmt);
					$query = mysqli_fetch_row($table);
					$matching_password = $query[0];
                    $is_admin = $query[1];
					$entered_password = $_POST['password'];
					if ($matching_password == $entered_password) {
                        if($is_admin == 1) {
                            header("Location: main.php");
                            exit();
                        } else {
						    header("Location: main_users.php");
						    exit();
                        }
					}
					else {
						echo '<br> Incorrect Password';
					}
				}
				else {
					$stmt = mysqli_prepare($link, 'insert into betbetter_users (username, password) values (?, ?)');
					mysqli_stmt_bind_param($stmt, 'ss', $_POST['username'], $_POST['password']);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
					echo '<br> New Account Created';
				}
			}
			else {
				echo '<br> Please Enter a Password';
			}
		}
	}

?>

</body>
</html>
