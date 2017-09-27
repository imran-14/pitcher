<?php
session_start();

if(!isset($_SESSION['user']))
{
	header("Location:  http://localhost/Pitcher/");
}
else if(isset($_SESSION['user'])!="")
{
	header("Location:  http://localhost/Pitcher/home");
}

if(isset($_GET['logout']))
{
	session_destroy();
	unset($_SESSION['user']);
	header("Location:  http://localhost/Pitcher/");
}
?>
