<script>
	
	var projectDomain = <?php echo "'".$projectDomain."'" ?>;
	var chat_server_port = <?php echo "'".$chat_server_port."'" ?>;
	var mid = <?php echo $_SESSION["member"]["id"]; ?>;
	
	var current_member_chat_id = null;
	var current_room = null;
	var current_offset = 0;
	var wsUri = "ws://"+projectDomain+":"+chat_server_port+"/server.php"; 
			
	websocket = new WebSocket(wsUri); 
	websocket.onopen = function(ev){ // connection is open 
		console.log("User joined app.");
		websocket.send(JSON.stringify({
			"type":"user_status",
			"data": {
				"status": "online",
				"mid": mid,
			},
		}));
	};
	
	// Message received from server
	websocket.onmessage = function(ev) {
		var response = JSON.parse(ev.data); //PHP sends Json data
		console.log(response);
		
		if(response.type == "system"){ // Send from system			
			if(response.data.status == "online"){
				$(".user-"+response.data.mid+"-status").addClass("status-bullet-online");
				$(".user-"+response.data.mid+"-status").removeClass("status-bullet-offline");
				$(".user-"+response.data.tid+"-status").addClass("status-bullet-online");
				$(".user-"+response.data.tid+"-status").removeClass("status-bullet-offline");
			}else{
				$(".user-"+response.data.mid+"-status").addClass("status-bullet-offline");
				$(".user-"+response.data.mid+"-status").removeClass("status-bullet-online");
				$(".user-"+response.data.tid+"-status").addClass("status-bullet-offline");
				$(".user-"+response.data.tid+"-status").removeClass("status-bullet-online");
			}
		}
		
		if(response.type == "user_message"){ // Send from user
			
			$("li[uid="+response.data.sender+"]").prependTo("#members-list");
			$("li[uid="+response.data.receiver+"]").prependTo("#members-list");
			
			if(response.data.sender == mid){
				$(".user-"+response.data.sender+"-message").html("Εσεις: "+getFirstNWords(response.data.msg, 4));
				$(".user-"+response.data.receiver+"-message").html("Εσεις: "+getFirstNWords(response.data.msg, 4));
				$("div[room="+current_room+"]").append(`
					<div class='msg-me'>
						<p class="text">`+response.data.msg+`</p>
						<p class="timestamp">`+response.data.timestamp+`</p>
					</div>	
					<div style='clear:both;' />
				`);
			}else{
				
				// Play sound
				var message_audio = new Audio('./assets/sounds/message_audio.wav');
				message_audio.play();
				
				// If message belongs to another chat room - mark as unseen
				if(response.data.sender != current_member_chat_id){
					$(".user-"+response.data.sender+"-message").addClass("blink-me");
				}
				
				$(".user-"+response.data.sender+"-message").html(getFirstNWords(response.data.msg, 4));
				$(".user-"+response.data.receiver+"-message").html(getFirstNWords(response.data.msg, 4));
				
				// Display message on chat only if it is open
				if(current_member_chat_id == response.data.sender){
					$("div[room="+current_room+"]").append(`
						<div class='msg-other'>
							<p class="text">`+response.data.msg+`</p>
							<p class="timestamp">`+response.data.timestamp+`</p>
						</div>	
						<div style='clear:both;' />
					`);
				}
				
			}
			
			$("#chat-area").animate({ scrollTop: $('#chat-area').prop("scrollHeight") }, 500);

		}
	};
	
	websocket.onerror = function(ev){ console.log(ev.data); }; 
	websocket.onclose = function(ev){ console.log("Connection closed"); };
	
	$(document).ready(function() {
		// Load members
		getMembers("#members-list", null);
		
		// Event Handlers
		$('#members-list-panel').on('click', '.list-group-item', function() { // On member click
			
			// Show chat modules
			$("#chat-room-bar").fadeIn(500);
			$("#chat-area").fadeIn(500);
			$("#typing-area").fadeIn(500);
			
			// Update global variables
			current_offset = 0;
			current_member_chat_id = $(this).attr("uid");
			current_room = getChatRoomCode(mid, current_member_chat_id);
			$("#chat-area").attr("room", current_room);
			$("#chat-area").html("");
			
			// Unset unseen class
			$(".user-"+current_member_chat_id+"-message").removeClass("blink-me");

			// Load member data & messages
			$.getJSON(projectPath+"/api.php", {"get-member":current_member_chat_id}, function (resp){
				user = resp.data;
				$("#chat-room-user").html(user.surname+" "+user.name);
				
				getMessages(current_room, 0);
				current_offset += 20;
			});
			
		});
		
		// On message send button
		$('body').on('click', '#send', function() {
			var msg = $("#msg").val();
			if(msg) sendMsg(msg);
		});
		
		$(document).on('keypress',function(e) {
			var msg = $("#msg").val();
			if(e.which == 13 && msg) sendMsg(msg);
		});
		
		// On member search
		$("#btn-search").click(function(){
			keyword = $("#search-chat-query").val();
			if(keyword && keyword.length > 3) getMembers("#members-list", keyword);
		})
		
		$("#search-chat-query").keyup(function() {
			keyword = $("#search-chat-query").val();
			if(keyword && keyword.length > 3) getMembers("#members-list", keyword);
			if(!keyword) getMembers("#members-list", null);
		});
		
		// On more messages load
		$('body').on('click', '#load-more-messages', function() {
			getMessages(current_room, current_offset);
			current_offset += 20;
		});
	});
	
	
</script>

<style></style>

<!-- Home Page -->
<div class="col-sm-4" id="members-list-panel">
	
	<!-- Logo Area -->
	<div id="logo-area">
		<img class="logo" style="width:15%;float:left;max-width:60px;" src="./assets/imgs/logo.png" />
		<div style="width:70%;color:white;float:left;font-family:san serif;font-size:28px;margin:0px;letter-spacing:2px;font-weight:bold;text-align:center;">
			<h2 style="margin-bottom:0px;cursor:pointer;" onclick="window.location.href='index.php'">Demo Application</h2>
			<h6><?php echo $projectName ?></h6>
		</div>
		<div style="clear: both;"></div>
	</div>
	
	<!-- Member Area -->
	<div id="member-area">
		<?php
			echo $_SESSION["member"]["surname"]." ".$_SESSION["member"]["name"];
		?>
	</div>

	<!-- Search Chat & Chat rooms listing -->
	<div id="search-chat-area" class="input-group mb-3">
	  <input type="text" id="search-chat-query" class="form-control shadow-none" placeholder="Search Contact">
	  <a class="btn" type="button" id="btn-search">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
		  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
		</svg>
	  </a>
	</div>
	<ul id="members-list" class="list-group"></ul>
	
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

<div class="col-sm-8" id="messages-panel">
		
	<!-- Receiver bar details -->
	<div id="chat-room-bar">
		<h3 id="chat-room-user"></h3>
		<div style="clear:both;"></div>
	</div>
	
	<!-- Chat area -->
	<div id="chat-area"></div>
	
	<!-- Typing area -->
	<div id="typing-area">
		<div class="input-group mb-3" style="margin:0px!important;">
		  <input type="text" id="msg" class="form-control shadow-none" placeholder="Your text here..">
		  <a class="btn" type="button" id="send">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
			  <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
			</svg>
		  </a>
		</div>
	</div>
</div>









