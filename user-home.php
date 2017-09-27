<?php

require_once('includes/session.php');

$user = $_SESSION['user'];

$query = "MATCH (user {username:'$user'}) RETURN user.name as name,user.profilepic as profilepic,user.coverpic as coverpic";
$result = $client->run($query);
$record = $result->getRecord();
$name = $record->value('name');
$profilepic = $record->value('profilepic');
$coverpic = $record->value('coverpic');

$reqQuery = "MATCH (friend:USERS)-[r:HAS_REQUESTED]-(user{username:'$user'}) RETURN friend.username as username";
$reqResult = $client->run($reqQuery);
$reqRecord = $reqResult->getRecord();



$result2 = $client->run("MATCH (user:USERS {username:'$user'})-[r:FRIENDSHIP]->(friend:USERS) RETURN friend.username as username, friend.name as name");

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<meta name="description" content="Pitcher is a social platform where one can share his/her images with specified permissions">
		<meta name="author" content="Pitcher-admin">
		<title>Pitcher - Home</title>
		<link rel="stylesheet" href="css/style-home.css">
		<script type="text/javascript" src="js/ads.js"></script>
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
				
				
				
				<?php foreach ($result2->records() as $record2) {
					$cuuuruser = $record2->value('username');
					$cuuuruser_name = $record2->value('name');
					$result1 = $client->run("MATCH (user:USERS {username:'$cuuuruser'})-[r:HAS_POSTED]->(pic:IMAGE) RETURN pic,pic.filename as filename,pic.posted_time as posted_time  ORDER BY pic.posted_time DESC");
					foreach ($result1->records() as $record1) {
				?>
				<div class="postImageBorder">
					<div class="imagePost">
						<div class="userPosted"><a href=""><?php echo $cuuuruser_name ?><span style="color:grey; margin-left:25px; font-size:15px"><?php echo"Posted on : ".date('d/m/y H:i:s',$record1->value('posted_time'));?></span></a></div>
						<table class="postContent">
							<tr>
								<td style='width: 60%;'>
									<img src="users/<?php echo $cuuuruser?>/<?php echo $record1->value('filename')?>" class="postImage">
									<ul class="postAlign">
										<li><img src="img/like.png" class="postIcon"></li>
										<li><img src="img/dislike.png" class="postIcon"></li>
										<li><img src="img/share.png" class="postIcon"></li>
										<li><img src="img/comment.png" class="postIcon"></li>
									</ul>
								</td>
								<td style='width: 30%;position:relative;background-color:#191919;text-align:center;'>
									<!--<div class="comment">
										<div class="comment-head">
											<a href="">ahmed_fouzan</a>
										</div>
										<p style="margin:10px;">my name is ahmed fouzan you know</p>
									</div>-->
									<p style='font-size:20px;font-weight:600;'>No Comments</p>
									
									
									<div class="comment-box">
										<textarea class="comment-area" placeholder="Write here...."></textarea>
										<input type="submit" value="Post" class="loginButton">
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			<?php
			}
		}
		?>
		<div class="ads" id="ads">

		</div>
		
		</div>
		<script type="text/javascript" src="js/notifications.js"></script>
	</body>
</html>
