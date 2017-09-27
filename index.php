<?php

session_start();

require_once('includes/database.php');

if(isset($_SESSION['user'])!="")
{
	header("Location: http://localhost/Pitcher/home");
}
if(isset($_POST['signup-btn']))
{
	$username = $_POST['username'];
	$uname = $_POST['name'];
	$uemail = $_POST['email'];
	$uphone = $_POST['phone'];
	$upwd = $_POST['password'];
	$upwdconfirm = $_POST['confirm-password'];


	
	if($upwd !== $upwdconfirm)
	{
		?>
		<script>alert('Password does not match !!!');</script>
		<?php
	}
	
	else
	{
		$client->run("CREATE (user:USERS) SET user += {infos}", ['infos' => ['username' => $username, 'name' => $uname, 'email' => $uemail, 'phone' => $uphone, 'password' => $upwd]]);
			?>
			<script>alert('Successfully Registered !!!!');</script>
			<?php $_SESSION['user'] = $username; ?>
			<script>window.location = "http://localhost/Pitcher/enterdetails";</script>
			<?php
		/*}
		else
		{
			?>
			<script>alert('error while registering you...');</script>
			<?php
		}*/
	}
}

if(isset($_POST['signin-btn']))
{
	$field1 = $_POST['loginEmail'];
	$field2 = $_POST['loginPassword'];
	$query = "MATCH (user:USERS) WHERE ((user.email='$field1' OR user.phone='$field1') AND user.password='$field2') RETURN user.username as username";
	$result = $client->run($query);
	$record = $result->getRecord();

	if(isset($record))
	{
		$_SESSION['user'] = $record->value('username'); ?>
		<script>window.location = "http://localhost/Pitcher/home";</script>
		<?php
	}
	else
		?><script>
			alert('Incorrect details or Account doesn\'t exist !!!!');
		</script>
		<?php
}
		
	


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<meta name="description" content="Pitcher is a social platform where one can share his/her images with specified permissions">
		<meta name="author" content="Pitcher-admin">
		<title>Pitcher</title>
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
		<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="sidebar-left">
				<div class="logo">
					<img src="img/logo-white.png" class="logo">
				</div>
				<div class="login">
					<p class="head1">Login</p>
					<form method="POST">
						<div class="form-group">
							<label >Email or Phone</label><input name="loginEmail" id="loginEmail" type="text" class="form-control" placeholder="Email or Phone" required>
						</div>
						<div class="form-group">
							<label >Password</label><input  name="loginPassword" id="loginPassword" type="password" class="form-control" placeholder="Password" required>
						</div>
						<input type="submit" value="Login" class="loginButton btn" name="signin-btn">
					</form>
				</div>
			</div>
			<div class="content">
				<h1 class="quote"></h1>
			</div>
			<div class="sidebar-right">
				<div class="login">
					<p class="head1">Sign Up</p>
					<form method="POST">
					<div class="form-group">
						<label>Username</label><input name="username" id="username" type="text" class="form-control" placeholder="Username" required>
					</div>
					<div class="form-group">
						<label>Password</label><input name="password" id="password" type="password" class="form-control" placeholder="Password" required>
					</div>
					<div class="form-group">
						<label>Confirm Password</label><input name="confirm-password" id="confirmPassword" type="password" class="form-control" placeholder="Confirm Password" required>
					</div>
					<div class="form-group">
						<label>Name</label><input name="name" id="name" type="text" class="form-control" placeholder="Name" required>
					</div>
					<div class="form-group">
						<label>Email</label><input name="email" id="email" type="email" class="form-control" placeholder="Email" required>
					</div>
					<div class="form-group">
						<label>Phone</label><input name="phone" id="phone" type="text" class="form-control" placeholder="Phone" required>
					</div>
					<input type="submit" value="Sign Up" class="loginButton btn" name="signup-btn">
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
