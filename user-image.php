<?php

require_once('includes/session.php');

if(!isset($_SESSION['user']))
{
	header("Location: index.php");
}
$user = $_SESSION['user'];
$query = "MATCH (user {username:'$user'}) RETURN user.name as name,user.profilepic as profilepic,user.no_of_pics_uploaded as number";
$result = $client->run($query);
$record = $result->getRecord();
$name = $record->value('name');
$profilepic = $record->value('profilepic');
$old_pic_no = $record->value('number');
$new_pic_no = $old_pic_no + 1;
$initialize=0;

if(isset($_POST['post_pic']))
{
	$folder="users/".$user."/";
	$file_loc = $_FILES['upload_pic']['tmp_name'];
	$file = explode(".", $_FILES['upload_pic']['name']);
	$newfilename = $user.'_'.$new_pic_no.'.' . end($file);
	$time = time();
	
	move_uploaded_file($file_loc,$folder.$newfilename);
	$client->run("MATCH (user {username:'$user'}) SET user.no_of_pics_uploaded=$new_pic_no");
	$client->run("CREATE (pic:IMAGE) SET pic += {infos}", ['infos' => ['filename' => $newfilename, 'posted_time' => $time, 'likes' => $initialize, 'dislikes' => $initialize, 'comments' => $initialize]]);
	$client->run("MATCH (user {username:'$user'}), (pic {filename:'$newfilename'}) CREATE (user)-[r:HAS_POSTED]->(pic)");
	?><script>alert("Uploaded Successfully");</script><?php
}


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
		<title>Pitcher - Upload Image</title>
		<link rel="stylesheet" href="css/style-home.css">
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



			<form method="post" enctype="multipart/form-data"> 
			<div class="content">
				<div class="upload-box">
					<label class="custom-file-upload">
						<input type="file" class="searchButton" id="imageLoader" name="upload_pic" accept="image/*" required/>
						<!--<img src="img/image-btn.png" class="upload-btn-img">-->
						<canvas id="imageCanvas" style="width:90%;height:83%;margin-top:5%;background-image:url('img/image-btn.png'); background-repeat:no-repeat;background-position: center;"></canvas>
									<script>
											var imageLoader = document.getElementById('imageLoader');
											imageLoader.addEventListener('change', handleImage, false);
											var canvas = document.getElementById('imageCanvas');
											var ctx = canvas.getContext('2d');
											if(canvas.innerHTML == ""){
												var img = new Image();
												canvas.width = img.width;
												canvas.height = img.height;
												ctx.drawImage(img,0,0);
											}
											
											function handleImage(e){
												var reader = new FileReader();
												reader.onload = function(event){
													var img = new Image();
													img.onload = function(){
														canvas.width = img.width;
														canvas.height = img.height;
														ctx.drawImage(img,0,0);
												   }
													img.src = event.target.result;
												}
												reader.readAsDataURL(e.target.files[0]);
											}
									</script>
					</label>
					<input name="post_pic" type="submit" value="Post" class="post-img">
				</div>
			</div>
		</form>
		</div>
		<script type="text/javascript" src="js/notifications.js"></script>
	</body>
</html>
