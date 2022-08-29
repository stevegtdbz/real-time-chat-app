<?php
require("./config.php");
require("./init.php");

print("[".date("Y-m-d h-i-s", time())."] Listening on $chat_server_host:$chat_server_port..\n");

//Create TCP/IP sream socket
$null = NULL;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

//reuseable port
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//bind socket to specified host
socket_bind($socket, 0, $chat_server_port);

//listen to port
socket_listen($socket);

//create & add listening socket to the list
$clients = array($socket);
$assocition_map = array();

//start endless loop, so that our script doesn't stop
while(true){
	//manage multipal connections
	$changed = $clients;
	
	//returns the socket resources in $changed array
	socket_select($changed, $null, $null, 0, 10);
	
	//check for new socket
	if(in_array($socket, $changed)){
		$socket_new = socket_accept($socket); //accept new socket
		$clients[] = $socket_new; //add socket to client array
		
		$header = socket_read($socket_new, 1024); //read data sent by the socket
		perform_handshaking($header, $socket_new, $chat_server_host, $chat_server_port); //perform websocket handshake
		
		socket_getpeername($socket_new, $ip); //get ip address of connected socket
		print("New connection from: $ip\n");
		
		//make room for new socket
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
		
	}
	
	//loop through all connected sockets
	foreach($changed as $changed_socket){
		
		//check for any incomming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1){
			$received_text = unmask($buf); //unmask data
			$received_json = json_decode($received_text, true); //json decode 
			
			if($received_json){
				
				// User status message handler
				if($received_json["type"] == "user_status"){
					
					// Associate user id with socket
					socket_getpeername($changed_socket, $ip);
					$assocition_map[$received_json["data"]["mid"]] = $ip;
					
					// Notify all about the user's status
					$response_text = mask(json_encode(array(
						'type'=>'system',
						'data'=>$received_json["data"]
					)));
					
					send_message_to_all($response_text); //send data
					print("User ".$received_json["data"]["status"].": ".$received_json["data"]["mid"]."\n");
					print("Connected users: ".sizeof($assocition_map)."\n");
					
					// Update database
					updateUserStatus($received_json["data"]["mid"], $received_json["data"]["status"]);

				}
				
				// User message handler
				if($received_json["type"] == "user_message"){
					$mid = $received_json["data"]["mid"];
					$tid = $received_json["data"]["tid"];
					$msg = $received_json["data"]["msg"];
					$created_at = date("Y/m/d h:i:s");
					
					// Broadcast to members
					$response_text = mask(json_encode(array(
						'type'=>'user_message',
						'data'=> array(
							"sender" => $mid,
							"receiver" => $tid,
							"msg" => $msg,
							"timestamp" => $created_at
						)
					)));
					
					print("User ".$mid." send msg to ".$tid.": ".$msg."\n");
					send_message_to($mid, $tid, $response_text);
					
					// Save to database
					pushMessage($mid, $tid, $msg, $created_at);					
				}
			}
			
			break 2; //exist this loop
		}
		
		// Check if user disconnected
		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if($buf === false){ // check disconnected client
			
			// remove client for $clients array
			$found_socket = array_search($changed_socket, $clients);
			socket_getpeername($changed_socket, $ip);			
			unset($clients[$found_socket]);
			
			// remove from association map
			$mid = null;
			foreach($assocition_map as $key=>$value){
				if($ip == $value){
					unset($assocition_map[$key]);
					$mid = $key;
					break;
				}
			}
			
			// Notify all about the user's status
			$response_text = mask(json_encode(array(
				'type'=>'system',
				'data'=> array(
					'status'=>'offline',
					'mid'=>$mid,
				)
			)));
			send_message_to_all($response_text); //send data
			
			// Update database
			updateUserStatus($mid, "offline");

			print("Connection closed: $ip\n");		
			print("Connected users: ".sizeof($assocition_map)."\n");	
		}
	}
}

// close the listening socket
socket_close($socket);

// My functions
function send_message_to_all($msg){
	global $clients;
	foreach($clients as $changed_socket){
		@socket_write($changed_socket, $msg, strlen($msg));
	}
	return true;
}

function send_message_to($mid, $tid, $msg){
	global $clients, $assocition_map;
	foreach($clients as $changed_socket){
		@socket_getpeername($changed_socket, $ip);
		if($ip == $assocition_map[$mid] || (isset($assocition_map[$tid]) && $ip == $assocition_map[$tid])){
			echo "SENDING TO IP: ".$ip."\n";
			@socket_write($changed_socket, $msg, strlen($msg));
		}
	}
	return true;
}


//Unmask incoming framed message
function unmask($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

//Encode message for transfer to client.
function mask($text){
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);
	
	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port){
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line){
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
}
