
var daysNames = ["Κυρ", "Δευ", "Τρ", "Τετ", "Πεμ", "Παρ", "Σαβ"];
var monthNames = ["Ιαν", "Φεβ", "Μαρ", "Απρ", "Μαι", "Ιουν", "Ιουλ", "Αυγ", "Σεπ", "Οκτ", "Νοε", "Δεκ"];

// Cookies & Sessions
function logout(){
	$.getJSON(projectPath+"/api.php", {"logout":""}, function (resp){
		location.reload();
	});
}

function setPreference(param){
	document.cookie = param+" expires=Thu, 18 Dec 9999 12:00:00 UTC; path=/;";
}

function getPreference(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

function delPreference(name) {
  document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

// Time & Price Formating
function timeRemains(date) {
	var targetTime = new Date(date);
	var timeZone = new Date().getTimezoneOffset(); // -60 = +1 UTC
	timeZone = -1 * (timeZone / 60);

	var tzDifference = timeZone * 60;
	var offsetTime = new Date(targetTime.getTime() + tzDifference * 60 * 1000);
	
	var currentDate = new Date();
	var diff = new Date(offsetTime.getTime() - currentDate.getTime());

	var years = diff.getUTCFullYear() - 1970;
	var months = diff.getUTCMonth();
	var days = diff.getUTCDate()-1;
	var hours = diff.getUTCHours();
	var minutes = diff.getUTCMinutes();
	
	result = [];
	if(years) result.push(years+" yrs");
	if(months) result.push(months+" months");
	if(days) result.push(days+" days");
	if(hours) result.push(hours+" hrs");
	if(minutes) result.push(minutes+" mins");
	return result.join(", ");

}

function getCookie(name) {
	const value = `; ${document.cookie}`;
	const parts = value.split(`; ${name}=`);
	if (parts.length === 2) return parts.pop().split(';').shift();
}

function getFirstNWords(str, n){
	return str.split(/\s+/).slice(0,n).join(" ")+"..";
}

function sendMsg(msg){
	console.log("Sending message.");
	websocket.send(JSON.stringify({
		"type":"user_message",
		"data": {
			"msg": msg,
			"mid": mid,
			"tid": current_member_chat_id,
		},
	}));
	$("#msg").val("");
}

function getMembers(el, keyword){
	$(el).empty();
	$.getJSON(projectPath+"/api.php", {"get-members":keyword}, function (resp){
		
		for(i=0;i<resp.data.length;i++){
			rec = resp.data[i];
			
			if(rec.room_last_message){
				room_last_message = rec.room_last_message.message;
			}else{
				room_last_message = "Start a conversation..";
			}
			
			last_sender_prefix="";
			unseen="";
			if(rec.room_last_message.sender_id==mid) last_sender_prefix = "You: ";
			if(rec.room_last_message.sender_id!=mid && rec.room_last_message.seen == 0) unseen = "blink-me";
			
			if(rec.room_last_message){
				current_date = new Date();
				last_message_sent_at = new Date(rec.room_last_message.created_at);

				if(last_message_sent_at.getFullYear() == current_date.getFullYear()){
					
					if(last_message_sent_at.getMonth() == current_date.getMonth()){
						
						if(last_message_sent_at.getDay() == current_date.getDay()){
							last_message_sent_at = "Σήμερα";
						}else{
							last_message_sent_at = last_message_sent_at.getDate()+" "+monthNames[last_message_sent_at.getMonth()];
						}
						
					}else{
						last_message_sent_at = last_message_sent_at.getDate()+" "+monthNames[last_message_sent_at.getMonth()];
					}
					
				}else{
					last_message_sent_at = monthNames[last_message_sent_at.getMonth()]+" "+last_message_sent_at.getFullYear();
				}
			}else{
				last_message_sent_at = "";
			}
			
			$(el).append(`
				<li class="list-group-item" uid="`+rec.id+`">
					<span class="user-`+rec.id+`-status status-bullet-`+rec.status+`"></span>
					<p class="room-preview-user">`+rec.surname+` `+rec.name+`</p>
					<p class="user-`+rec.id+`-message room-last-message `+unseen+`">`+last_message_sent_at+` • `+last_sender_prefix+room_last_message+`</p>
				</li>
			`);
		}
	});
}

function getMessages(current_room, offset){
	$.getJSON(projectPath+"/api.php", {"get-messages":current_room, "offset":offset}, function (resp){
		
		$("#load-more-messages").remove();
		
		for(i=0;i<resp.data.length;i++){
			var rec = resp.data[i];
			if(rec.sender_id == mid){
				$("div[room="+current_room+"]").prepend(`
					<div class='msg-me'>
						<p class="text">`+rec.message+`</p>
						<p class="timestamp">`+rec.created_at+`</p>
					</div>	
					<div style='clear:both;' />
				`);
			}else{
				$("div[room="+current_room+"]").prepend(`
					<div class='msg-other'>
						<p class="text">`+rec.message+`</p>
						<p class="timestamp">`+rec.created_at+`</p>
					</div>	
					<div style='clear:both;' />
				`);
			}
		}
		
		if(resp.data.length == 20){
			$("div[room="+current_room+"]").prepend(`
				<p style="text-align:center;" class="no-select"><span id="load-more-messages">Προηγούμενα μηνύματα..</span></p>
			`);
		}
		
		if(offset==0) $("#chat-area").animate({ scrollTop: $("div[room="+current_room+"]").prop("scrollHeight") }, 0);

	});
}

function getChatRoomCode(id1, id2){
	if(id1 > id2){
		chat_room_code = id1+"-"+id2;
	}else{
		chat_room_code = id2+"-"+id1;
	}
	return chat_room_code;
}

function version(){
	 $("#version").text("Developed by Stavros V.");
}
