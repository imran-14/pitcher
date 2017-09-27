<?php

require_once('includes/session.php');

$user = $_SESSION['user'];

$query = "MATCH (user {username:'$user'}) RETURN user.name as name,user.profilepic as profilepic,user.coverpic as coverpic";
$result = $client->run($query);
$record = $result->getRecord();
$name = $record->value('name');
$profilepic = $record->value('profilepic');
$coverpic = $record->value('coverpic');
global $searchedname;
if(isset($_POST['search-btn']))
{
	$searchname = $_POST['searchname'];
	$query1 = "MATCH (user:USERS) WHERE user.username='$searchname' RETURN user.username as username, user.profilepic as profilepic";
	$result1 = $client->run($query1);
	$record1 = $result1->getRecord();
	$searchedname = $record1->value('username');
	$searchedprofile = $record1->value('profilepic');
	
	if(isset($record1))
	{
		//$query2 = "MATCH (user:USERS) WHERE user.username='$searchname' RETURN user.username as username, user.profilepic as profilepic";
		//$result2 = $client->run($query2);
		//$record2 = $result2->getRecord();
		
		$currentuser=$record1->value('username');
		$query3 = "MATCH (user{username:'$user'})-[r:FRIENDSHIP]->(friend{username:'$currentuser'}) RETURN CASE r WHEN r IS NULL THEN 1 ELSE 0 END as r";
		$result3 = $client->run($query3);
		$record3 = $result3->getRecord();
		
		echo '<style type="text/css">
        #display-result {
            display: block;
        }
        </style>';
		 ?>
		 <script>
		 	alert('Successfull Search !!!!');
		</script>
		<?php
	}
	else{
		?><script>alert('Incorrect details or Account doesn\'t exist !!!!');</script>
	<?php
	}
}

if(isset($_POST['addfriend-btn']))
{
	$pes = $_POST['user-name'];
	$client->run("MATCH (user {username:'$user'}), (friend {username:'$pes'}) CREATE (user)-[r:HAS_REQUESTED]->(friend)");
}

$query2 = "MATCH (user:USERS) WHERE not(user.username='$user') RETURN user.username as username, user.profilepic as profilepic";
$result2 = $client->run($query2);
$record2 = $result2->getRecord();

$reqQuery = "MATCH (friend:USERS)-[r:HAS_REQUESTED]-(user{username:'$user'}) RETURN friend.username as username";
$reqResult = $client->run($reqQuery);
$reqRecord = $reqResult->getRecord();
?>



<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<meta name="description" content="Pitcher is a social platform where one can share his/her images with specified permissions">
		<meta name="author" content="Pitcher-admin">
		<title>Pitcher - Make Friends</title>
		<link rel="stylesheet" href="css/style-home.css">
		<script type="text/javascript" src="js/searchfriends.js"></script>
	</head>
	<body>
		<span style="fontsize:1;position:absolute;z-index:-1000" id="currUsername"><?php echo $user ?></span>
		<div class="container">
			<div class="sidebar-left">
				<img src="img/logo.png" class="logo">
				<div class="profilePic">
					<img src="users/<?php echo $user."/".$profilepic?>" class="profilePic">
					<p class="profilePic"><?php echo $name?></p>
				</div>
				
				
				<!--############################### Menu Icons Start #############################-->
				<ul class="icon-box">
					<li class="icon-li"><a href="http://localhost/Pitcher/home"><img src="img/home.png" class="icon"></a></li>
					<li class="icon-li"><a href="http://localhost/Pitcher/profile"><img src="img/profile.png" class="icon"></a></li>
					<li class="icon-li"><a href="http://localhost/Pitcher/searchfriends"><img src="img/search.png" class="icon"></a></li>
					<li class="icon-li"><a href="http://localhost/Pitcher/uploadimage"><img src="img/myimage.png" class="icon"></a></li>
					<li class="icon-li"><img src="img/notification.png"  id="notification-icon" class="icon"></li>
					<li class="icon-li"><a href="logout.php?logout"><img src="img/logout.png" class="icon"></a></li>
					
				</ul>
				<!--################################ Menu Icons End ###############################-->

			</div>
				<div id="notifications">
					<div class="insideNotify" style="margin-bottom:10px;font-size:20px;color:white;">Notifications</div>
					<table class="notifications" id="notification">
						
					</table>
				</div>
					<script>
						var flag=false;
						document.getElementById("notification-icon").onmouseover=function(){
							if(flag==false){
								document.getElementById("notifications").style.visibility="visible";
								document.getElementById("notifications").style.opacity="1";
							}
						};
						document.getElementById("notification-icon").onclick=function(){
							if(!flag){
								document.getElementById("notifications").style.visibility="visible";
								document.getElementById("notifications").style.opacity="1";
								flag=true;
							}
							else{
								document.getElementById("notifications").style.visibility="hidden";
								document.getElementById("notifications").style.opacity="0";
								flag=false;
							}
						};
						document.getElementById("notification-icon").onmouseout=function(){
							if(flag==false){
								document.getElementById("notifications").style.visibility="hidden";
								document.getElementById("notifications").style.opacity="0";
							}
						};
					</script>



			<div class="content">
				<div class="container-fluid">
					<div style="width:100%;text-align:center;">
						<div class="search"  id="search" >
							<input type="text" class="searchbox"  autocomplete="off"  id="searchbox" name="searchname" onkeyup="user.getPeople()" placeholder="Search people here with their username....">
						</div>
<!--						<button type="submit" name="search-btn" class="searchButton" id="registerButton"><img src="img/search.png" style="width:20px;margin-left:10px;margin-right:10px;"></button> -->
					</div>
					<table class="search-result" id="display-result">
					</table>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="js/notifications.js"></script>
	</body>
</html>
