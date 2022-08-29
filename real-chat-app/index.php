<!--
author: 		ΣΤΡΑΤΙΩΤΗΣ ΕΡΕΥΝΑΣ ΠΛΗΡΟΦΟΡΙΚΗΣ ΒΡΑΧΝΗΣ ΣΤΑΥΡΟΣ
last update:	09/07/2022 
-->

<?php
session_start();
if(!isset($_SESSION["member"])) header("Location: login.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require("config.php");
require("init.php");

?>


<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link href="./assets/css/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" type="image/png" href="assets/imgs/logo.png">

		<!-- Jquery -->
		<script src="assets/js/jquery.min.js"></script>

		<!-- Custom -->
		<link rel="stylesheet" href="assets/css/dark-theme.css">
		<link rel="stylesheet" href="assets/css/main.css">
		<script src="assets/js/sweetalert2@11"></script>
		<script src="assets/js/main.js"></script>
		<script src="assets/js/sorttable.js"></script>
		
		<script>
			var projectPath = "<?php echo $projectPath;?>";
		</script>

		<title><?php echo $projectName; ?></title>
	</head>
	
	<body>
		
		<!-- Private chat container -->
		<div class="row" style="margin:0px;">
			
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

		<script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
