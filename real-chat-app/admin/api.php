<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("../config.php");
require("../init.php");

$resp = array();
$resp["code"] = 404;
$resp["msg"] = "Not found";
$resp["data"] = array();

// Login / Register
if(isset($_GET["logout"])){
	unset($_SESSION["admin"]);
}

if(isset($_POST["login"]) && isset($_POST["password"])){
	$stm = $db->prepare("SELECT id, username, created_at FROM admins WHERE username=? AND password=?");
	$stm->execute(array($_POST["login"], md5($_POST["password"])));
	$tmp = $stm->fetch(\PDO::FETCH_ASSOC);
	
	if($tmp){
		$_SESSION["admin"] = $tmp;
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}else{
		$resp["msg"] = "Wrong credentials";
		$resp["code"] = "403";
	}
}

// Member functions
if(isset($_SESSION["admin"])){
	
	// Members
	if(isset($_POST["member-new"])){
		$p = $_POST["member-new"];
				
		$stm = $db->prepare("INSERT INTO members (military_id, username, password, name, surname, rank, corp) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$ins = $stm->execute(array($p["asm"],$p["username"],md5($p["password"]),$p["name"],$p["surname"],$p["ranks"],$p["corps"]));
		
		if($ins){
			$resp["msg"] = "Success";
			$resp["code"] = "200";
		}else{
			$resp["msg"] = "Error";
			$resp["code"] = "403";
		}
	}
	
	if(isset($_POST["member-update"]) && isset($_GET["id"])){
		$p = $_POST["member-update"];	
		$stm = $db->prepare("UPDATE members SET military_id=?, username=?, name=?, surname=?, rank=?, corp=? WHERE id=?");
		$ins = $stm->execute(array($p["asm"],$p["username"],$p["name"],$p["surname"],$p["ranks"],$p["corps"],$_GET["id"]));
		
		if($ins){
			$resp["msg"] = "Success";
			$resp["code"] = "200";
		}else{
			$resp["msg"] = "Error";
			$resp["code"] = "403";
		}
	}
	
	if(isset($_GET["get-member"])){
		$stm = $db->prepare("SELECT id, username, military_id, name, surname, rank, corp, created_date, status FROM members WHERE id=?");
		$stm->execute(array($_GET["get-member"]));
		$resp["data"] = $stm->fetch(\PDO::FETCH_ASSOC);
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}
	
	if(isset($_GET["get-members"])){		
		if(empty($_GET["get-members"])){
			$stm = $db->prepare("SELECT * FROM members ORDER BY id DESC");
			$stm->execute();
		}else{
			$search = "%".$_GET["get-members"]."%";
			$stm = $db->prepare("SELECT * FROM members WHERE username LIKE ? OR military_id LIKE ? OR name LIKE ? OR surname LIKE ? ORDER BY id DESC");
			$stm->execute(array($search, $search, $search, $search));
		}
		
		$resp["data"] = $stm->fetchAll(\PDO::FETCH_ASSOC);
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}
	
	if(isset($_GET["unlock-member"])){		
		$stm = $db->prepare("UPDATE members SET password = ? WHERE id=?");
		$stm->execute(array($default_user_password, $_GET["unlock-member"]));
		$resp["data"] = $stm->fetch(\PDO::FETCH_ASSOC);
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}

	if(isset($_GET["delete-member"])){
		$stm = $db->prepare("DELETE FROM members WHERE id=?");
		$stm->execute(array($_GET["delete-member"]));
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}
	
}



header('Content-type: application/json; charset=utf-8');
echo json_encode($resp);

