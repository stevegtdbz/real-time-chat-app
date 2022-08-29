<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch ( PDOException $e ){
    print $e->getMessage();
}

function updateUserStatus($uid, $status){
	global $db;
	
	$stm = $db->prepare("UPDATE members SET status=? WHERE id=?");
	return $stm->execute(array($status, $uid));
}

function pushMessage($sender, $receiver, $msg, $created_at){
	global $db;
	
	if($sender > $receiver){
		$chat_id = $sender."-".$receiver;
	}else{
		$chat_id = $receiver."-".$sender;
	}
		
	$stm = $db->prepare("INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, ?)");
	return $stm->execute(array($chat_id, $sender, $msg, $created_at));
	
}
