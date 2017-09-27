<?php

require_once('includes/session.php');

$user = $_SESSION['user'];

if(isset($_POST['go_ahead']))
{
	mkdir("users/".$user);
	$folder1="users/".$user."/";
	$file_loc1 = $_FILES['photo']['tmp_name'];
	$file1 = explode(".", $_FILES['photo']['name']);
	$newfilename1 = 'profile'.'.' . end($file1);
	
	$file_loc2 = $_FILES['cover_photo']['tmp_name'];
	$file2 = explode(".", $_FILES['cover_photo']['name']);
	$newfilename2 = 'cover'. '.' . end($file2);
	
	move_uploaded_file($file_loc1,$folder1.$newfilename1) && move_uploaded_file($file_loc2,$folder1.$newfilename2);

	$uage = $_POST['age'];	
	$ugender = $_POST['gender'];

	$client->run("MATCH (user {username:'$user'}) SET user.age='$uage', user.gender='$ugender', user.profilepic='$newfilename1', user.coverpic='$newfilename2', user.no_of_pics_uploaded=0");
	?>
	<script>window.location = "http://localhost/Pitcher/home";</script>
	<?php
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Sharage is a social platform where one can share his/her images with specified permissions">
		<meta name="author" content="Sharage-admin">
		<title>Sharage - Home</title>
		<link rel="stylesheet" href="css/style-home.css">
	</head>
	<body>				
				<div class="sidebar-left">
					<img src="img/logo.png" class="logo">
				</div>
			<form method="post" id="form" enctype="multipart/form-data">
			<div class="content">

				<div class="fill-box">
					<div class="fill-head">
						Fill Your Profile
					</div>
					<table style="width:100%;">
						<tr class="fill-in">
							<td class="fill-in-td" style="width:15%;padding-left:30px;">Profile Image</td>
							<td style="width:30%;">
								<div class="upload-box" style="margin:10px;height:180px;">
									<label class="custom-file-upload" style="height:100%;">
										<div id="appphoto">
											<input  id="imageLoader" type="file" class="searchButton" name="photo" accept="image/*" required/>
										</div>
										<canvas id="imageCanvas" style="width:150px;height:170px;background-image:url('img/image-btn.png'); background-repeat:no-repeat;background-position:center;background-size: 50% auto;"></canvas>
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
								</div>
							</td>
							<td style="width:20%"></td>
							<td style="width:35%"></td>
						</tr>
						<tr class="fill-in">
							<td class="fill-in-td" style="width:15%;padding-left:30px;">Cover Photo</td>
							<td style="width:30%;">
								<div class="upload-box" style="margin:10px;height:180px;">
									<label class="custom-file-upload" style="height:100%;">
										<div id="appphoto"><!--<canvas id="imageCanvas2" style="width:150px;height:170px"></canvas>-->
											<input id="imageLoader2" name="cover_photo" type="file" class="searchButton" name="sign" accept="image/*" required/>
										</div>
										<canvas id="imageCanvas2" style="width:150px;height:170px;background-image:url('img/image-btn.png'); background-repeat:no-repeat;background-position:center;background-size: 50% auto;"></canvas>
										<script>
											var imageLoader2 = document.getElementById('imageLoader2');
												imageLoader2.addEventListener('change', handleImage2, false);
											var canvas2 = document.getElementById('imageCanvas2');
											var ctx2= canvas2.getContext('2d');
											if(canvas2.innerHTML == ""){
												var img2 = new Image();
												canvas2.width = img2.width;
												canvas2.height = img2.height;
												ctx2.drawImage(img2,0,0);
											}
											function handleImage2(e){
												var reader2 = new FileReader();
												reader2.onload = function(event){
													var img2 = new Image();
													img2.onload = function(){
														canvas2.width = img2.width;
													   canvas2.height = img2.height;
													   ctx2.drawImage(img2,0,0);
												   }
													img2.src = event.target.result;
												}
												reader2.readAsDataURL(e.target.files[0]);     
											}
										</script>
									</label>
								</div>
							</td>
							<td style="width:20%">
							</td>
							<td style="width:35%"></td>
						</tr>
						<tr class="fill-in">
							<td class="fill-in-td" style="width:15%;padding-left:30px;">Age</td>
							<td class="fill-in-td" style="width:15%"><input name="age" type="text" class="fill-input"></td>
							<td style="width:70%"></td>
						</tr>
						<tr class="fill-in">
							<td class="fill-in-td" style="width:20%;padding-left:30px;">Gender</td>
							<td class="fill-in-td" style="width:15%;padding:0;"><input type="radio" class="gender" name="gender" value="male"><label>Male</label></td>
							<td class="fill-in-td" style="width:15%;padding:0;"><input type="radio" class="gender" name="gender" value="female"><label>Female</label></td>
						</tr>
						<tr class="fill-in">
							<td class="fill-in-td" style="width:20%;padding-left:30px;">
								<input name="go_ahead" type="submit" value="Go Ahead" class="post-img">
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		</form>
	</body>
</html>
