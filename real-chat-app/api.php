<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("config.php");
require("init.php");

$resp = array();
$resp["code"] = 404;
$resp["msg"] = "Not found";
$resp["data"] = array();

// Login / Register
if(isset($_GET["logout"])){
	unset($_SESSION["member"]);
}

if(isset($_POST["login"]) && isset($_POST["password"])){
	$stm = $db->prepare("SELECT id, username, name, surname, created_date FROM members WHERE username=? AND password=?");
	$stm->execute(array($_POST["login"], md5($_POST["password"])));
	$tmp = $stm->fetch(\PDO::FETCH_ASSOC);
	
	if($tmp){
		$_SESSION["member"] = $tmp;
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}else{

		$stm = $db->prepare("SELECT password FROM members WHERE username=?");
		$stm->execute(array($_POST["login"]));
		$tmp = $stm->fetch(\PDO::FETCH_ASSOC);
	
		if($tmp && $tmp["password"] == $default_user_password){
			$resp["msg"] = "Reset password";
			$resp["code"] = "500";
		}else{
			$resp["msg"] = "Wrong credentials";
			$resp["code"] = "403";
		}
	}
}

if(isset($_POST["register"]) && isset($_POST["password"])){
	$stm = $db->prepare("INSERT INTO users (username, password, account_type) VALUES (?, ?, ?)");
	$ins = $stm->execute(array($_POST["register"],md5($_POST["password"]), $_POST["account_type"]));
	
	if($ins){
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}else{
		$resp["msg"] = "Wrong credentials";
		$resp["code"] = "403";
	}
}

if(isset($_POST["unlock-member"])){
	$p = $_POST["unlock-member"];
	$stm = $db->prepare("UPDATE members SET password = ? WHERE username=?");
	$stm->execute(array(md5($p["password"]), $p["username"]));
	$resp["data"] = $stm->fetch(\PDO::FETCH_ASSOC);
	$resp["msg"] = "Success";
	$resp["code"] = "200";
}

// Admin functions
if(isset($_SESSION["member"])){
	
	// Members
	if(isset($_GET["get-member"])){
		$stm = $db->prepare("SELECT id, username, name, surname, created_date FROM members WHERE id=?");
		$stm->execute(array($_GET["get-member"]));
		$resp["data"] = $stm->fetch(\PDO::FETCH_ASSOC);
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}

	if(isset($_GET["get-members"])){		
		if(empty($_GET["get-members"])){
			$stm = $db->prepare("SELECT * FROM members WHERE id!=? ORDER BY surname ASC");
			$stm->execute(array($_SESSION["member"]["id"]));
		}else{
			$search = "%".$_GET["get-members"]."%";
			$stm = $db->prepare("SELECT * FROM members WHERE (name LIKE ? OR surname LIKE ?) AND id!=? ORDER BY surname ASC");
			$stm->execute(array($search, $search, $search, $_SESSION["member"]["id"]));
		}
		
		$records = $stm->fetchAll(\PDO::FETCH_ASSOC);
		foreach($records as $record){
			if($_SESSION["member"]["id"] > $record["id"]){
				$record["chat_id"] = $_SESSION["member"]["id"]."-".$record["id"];
			}else{
				$record["chat_id"] = $record["id"]."-".$_SESSION["member"]["id"];
			}
			
			$stm = $db->prepare("SELECT * FROM messages WHERE chat_id=? ORDER BY id DESC LIMIT 1");
			$stm->execute(array($record["chat_id"]));
			$record["room_last_message"] = $stm->fetch(\PDO::FETCH_ASSOC);
			
			if($record["room_last_message"]){
				$record["room_last_message_id"] = $record["room_last_message"]["id"];
			}else{
				$record["room_last_message_id"] = 0;
			}
			
			array_push($resp["data"], $record);
		}
		
		// Sort array last messages on top
		$room_last_message_id = array_column($resp["data"], 'room_last_message_id');
		array_multisort($room_last_message_id, SORT_DESC, $resp["data"]);
		
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}
	
	// Messages
	if(isset($_GET["get-messages"])){
		
		if(isset($_GET["offset"]) && $_GET["offset"] != 0){
			$stm = $db->prepare("SELECT * FROM messages WHERE chat_id=:chat_id ORDER BY id DESC LIMIT 20 OFFSET :offset");
			$stm->bindValue(':chat_id', $_GET["get-messages"], PDO::PARAM_STR);
			$stm->bindValue(':offset', $_GET["offset"], PDO::PARAM_INT);
			$stm->execute();
		}else{
			// Update seen property
			$stm = $db->prepare("UPDATE messages SET seen=? WHERE chat_id=? AND sender_id!=?");
			$stm->execute(array(1, $_GET["get-messages"], $_SESSION["member"]["id"]));
			
			$stm = $db->prepare("SELECT * FROM messages WHERE chat_id=? ORDER BY id DESC LIMIT 20");
			$stm->execute(array($_GET["get-messages"]));
		}

		$resp["data"] = $stm->fetchAll(\PDO::FETCH_ASSOC);	
		$resp["msg"] = "Success";
		$resp["code"] = "200";
	}
	
}



header('Content-type: application/json; charset=utf-8');
echo json_encode($resp);
