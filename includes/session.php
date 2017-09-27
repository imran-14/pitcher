<?php
session_start();
    require_once('database.php');

    if(!isset($_SESSION['user']))
    {
        header("Location: http://localhost/Pitcher/");
    }
?>