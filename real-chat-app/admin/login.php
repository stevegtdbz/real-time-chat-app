<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
if(isset($_SESSION["admin"])) header("Location: index.php");

require("../config.php");
require("../init.php");

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $projectName; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link rel="shortcut icon" type="image/png" href="assets/imgs/79adte.png">

	<!-- Jquery -->
	<script src="assets/js/jquery.min.js"></script>

	<!-- Custom -->
	<link rel="stylesheet" href="assets/css/dark-theme.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<script src="assets/js/main.js"></script>
	<script src="assets/js/sweetalert2@11"></script>

	<script>
		var projectPath = "<?php echo $projectPath;?>";
		
		function login(){
			var email = $("#email").val();
			var password = $("#password").val();
			
			if(email && password){
				$.post("./api.php", {"login":email, "password":password}, function (resp){
					if(resp.code==200){
						window.location.href= "./index.php";
					}else{
						Swal.fire({ title: 'Λάθος όνομα ή κωδικός χρήστη.'});
						$("#email").val("");
						$("#password").val("");
					}
				});
			}else{
				displayMsg("All fields required!","danger");
			}
		}
		
		$(document).on('keypress', function(e) {
			if(e.which == 13) login();
		});
		
	</script>
	
	<style>	
		body{
			background: var(--action_color);
		}
		
		#login-container{
			background: transparent;
			margin: auto auto;
			width: 40vw;
			max-width: 900px;
			margin-top: 20vh;
		}

		#login-container h3{
			color: var(--white);
			text-align: center;
			margin-top: 20px;
		}
		
		#login-container h3 span{
			color: var(--top_bar_color);
		}

		#login-container .footer{
			margin-top: 10px;
			font-size: 13px;
			text-align: right;
			color: white;
		}
}
	</style>

</head>


<body>
	<div id="login-container" class="row">

		<!-- Logo Area -->
		<div id="logo-area">
			<img class="logo" style="width:15%;float:left;max-width:60px;" src="./assets/imgs/logo.png" />
			<div style="width:70%;color:white;float:left;font-family:san serif;font-size:28px;margin:0px;letter-spacing:2px;font-weight:bold;text-align:center;">
				<h2 style="margin-bottom:0px;cursor:pointer;" onclick="window.location.href='index.php'">Demo Application</h2>
				<h6><?php echo $projectName ?></h6>
				<h6 style="color:red;font-size:15px;margin:0px;">Administrator Panel</h6>
				<br>
			</div>
			<img class="logo" style="width:15%;float:right;max-width:60px;" src="./assets/imgs/logo.png" />
			<div style="clear: both;"></div>
		</div>
		
		<!-- Login form -->
		<div>
			<div class="input-group mb-3">
			  <span class="input-group-text">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
				  <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
				</svg>
			  </span>
			  <input placeholder="Username" id="email" type="username" class="form-control shadow-none" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
			</div>

			<div class="input-group mb-3">
			  <span class="input-group-text">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
				  <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
				</svg>
			  </span>
			  <input placeholder="Password" id="password" type="password" class="form-control shadow-none" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
			</div>

			<a class="btn input-group mb-3" style="background:var(--top_bar_color);color:white;" type="button" onclick="login()">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
				  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
				  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
				</svg>
				Login as admin
			</a>
			
		</div>
	</div>
	
	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
