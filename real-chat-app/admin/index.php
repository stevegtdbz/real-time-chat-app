<?php
session_start();
if(!isset($_SESSION["admin"])) header("Location: login.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require("../config.php");
require("../init.php");

?>


<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" type="image/png" href="assets/imgs/logo.png">

		<!-- Jquery -->
		<script src="assets/js/jquery.min.js"></script>

		<!-- Custom -->
		<link rel="stylesheet" href="assets/css/dark-theme.css">
		<link rel="stylesheet" href="assets/css/main.css">
		<script src="assets/js/sweetalert2@11"></script>
		<script src="assets/js/main.js"></script>
		
		<script>
			var projectPath = "<?php echo $projectPath;?>";
		</script>

		<title><?php echo $projectName; ?></title>
	</head>
	
	<body>
			
		<div class="row" style="margin:0px;background:beige;">	
			
			<!-- Admin Menu -->
			<div class="col-sm-4" id="members-list-panel">
				
				<!-- Logo Area -->
				<div id="logo-area">
					<img class="logo" style="width:15%;float:left;max-width:60px;" src="./assets/imgs/logo.png" />
					<div style="width:70%;color:white;float:left;font-family:san serif;font-size:28px;margin:0px;letter-spacing:2px;font-weight:bold;text-align:center;">
						<h2 style="margin-bottom:0px;cursor:pointer;" onclick="window.location.href='index.php'">Demo Application</h2>
						<h6><?php echo $projectName ?></h6>
					</div>
					<img class="logo" style="width:15%;float:right;max-width:60px;" src="./assets/imgs/logo.png" />
					<div style="clear: both;"></div>
				</div>
				
				<!-- Member Area -->
				<div id="member-area">
					<?php
						echo "Admin Account: ".$_SESSION["admin"]["username"];
					?>
				</div>

				<!-- Search Chat & Chat rooms listing -->
				<ul id="members-list" class="list-group" style="overflow-y:unset;">
					<li class="list-group-item"><a href="?page=manage-members">Users</a></li>
				</ul>
				
				<!-- Logout Area -->
				<a id="logout" href="#" onclick="logout()">
					Logout
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
					  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
					  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
					</svg>
				</a>
				
				<!-- Version -->
				<a id="version" href="#" onclick="version()">
					Version <?php echo $version; ?>
				</a>
			</div>
					
			<!-- Private chat container -->
			<div class="col-sm-8" style="margin:0px;padding:0px;">
				
				<?php
					if(isset($_GET["page"])){
						if(file_exists("./includes/".$_GET["page"].".php")){
							include("./includes/".$_GET["page"].".php");
						}else{
							echo "<div class='content_center'>Page not found!</div>";
						}	
					}else{
						include("./includes/home.php");
					}
				?>
				
			</div>

		</div>

		<script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
