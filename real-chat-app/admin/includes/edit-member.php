<script>
	
	const urlSearchParams = new URLSearchParams(window.location.search);
	const params = Object.fromEntries(urlSearchParams.entries());
	
	$(document).ready(function() {
		$("#select-ranks").append(getMilitaryRanks());
		$("#select-corps").append(getMilitaryCorps());
		
		if(params["id"]){
			loadMember(params["id"]);
			$("#password").attr("disabled", "disabled");
			$("#rpassword").attr("disabled", "disabled");
			$("#password").val("xxxxxx");
			$("#rpassword").val("xxxxxx");
		}

		$(document).on('keypress', function(e) {
			if(e.which == 13) saveMember();
		});
	});
	
	function saveMember(){
		var password = $("#password").val();
		var rpassword = $("#rpassword").val();
		if(password == rpassword){
			
			if(password.length >= 6){
				
				var p = {
					"asm": $("#asm").val(),
					"name": $("#name").val(),
					"surname": $("#surname").val(),
					"ranks": $("#select-ranks").val(),
					"corps": $("#select-corps").val(),
					"username": $("#username").val(),
					"password": password
				}
				
				if(p["asm"] && p["username"] && p["password"] && p["name"] && p["surname"] && p["ranks"] && p["corps"]){
					
					if(!params["id"]){
						$.post("./api.php", {"member-new": p}, function (resp){
							if(resp.code==200){
								Swal.fire({ title: 'Επιτυχια!' });
							}else{
								Swal.fire({ title: 'Σφάλμα εισαγωγής' });
							}
						});
					}else{
						$.post("./api.php?id="+params["id"], {"member-update": p}, function (resp){
							if(resp.code==200){
								Swal.fire({ title: 'Επιτυχια!' });
							}else{
								Swal.fire({ title: 'Σφάλμα εισαγωγής' });
							}
						});
					}
					
				}else{
					Swal.fire({ title: 'Ολα τα πεδία ειναι υποχρεωτικά'});
				}
			}else{
				Swal.fire({ title: 'Μη έγκυρος κωδικός'});
			}
		}else{
			Swal.fire({ title: 'Οι κωδικοί δεν ταιριάζουν'});
		}
	}
			
	function loadMember(mid){
		$.getJSON("./api.php", {"get-member":mid}, function (resp){
			rec = resp.data;
			$("#asm").val(rec.military_id);
			$("#name").val(rec.name);
			$("#username").val(rec.username);
			$("#surname").val(rec.surname);
			$("#select-ranks").val(rec.rank);
			$("#select-corps").val(rec.corp);
			$("#select-service").val(rec.service_id);
		});
	}		
	
</script>

<style>
#container-edit-member span, #container-edit-member input{
	font-size: 14px;
}
	
#container-edit-member input{
	text-transform: uppercase;
}

#member-day-preferences input[type="range"]{
	margin: auto;
}

#member-day-preferences span{
	width:120px;
	font-size: 14px;
}
</style>

<div style="padding:10px;">
	<p class="page-header">
		Edit user
		<a class="btn btn-outline-success" type="button" style="float:right;margin-left:5px;" onclick="saveMember()">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
			  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"></path>
			  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"></path>
			</svg>
			Save
		</a>
		<a class="btn btn-outline-secondary" type="button" style="float:right;" onclick="history.back()">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
			  <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/>
			  <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/>
			</svg>
			Back
		</a>
		<div style="clear:both;"></div>
	</p>

	<div class="row" style="width:100%;margin:0px;">

		<!-- Edit Member Personal information -->
		<div class="col-sm-6" id="container-edit-member">

			<div class="input-group mb-3">
			  <span class="input-group-text">ID</span>
			  <input id="asm" type="text" class="form-control" aria-describedby="inputGroup-sizing-default">
			</div>

			<div class="input-group mb-3">
			  <span class="input-group-text">Name</span>
			  <input id="name" type="text" class="form-control" aria-describedby="inputGroup-sizing-default">
			</div>

			<div class="input-group mb-3">
			  <span class="input-group-text">Surname</span>
			  <input id="surname" type="text" class="form-control" aria-describedby="inputGroup-sizing-default">
			</div>

			<div class="input-group mb-3" style="display:none;">
			  <span class="input-group-text">Βαθμός</span>
			  <select class='form-select' id="select-ranks"></select>
			</div>

			<div class="input-group mb-3" style="display:none;">
			  <span class="input-group-text">Σωμα</span>
			  <select class='form-select' id="select-corps"></select>
			</div>
		
		</div>
			
		<!-- Account credentials -->
		<div class="col-sm-6">
			<div class="input-group mb-3">
			  <span class="input-group-text">Username</span>
			  <input id="username" style="text-transform:none;" type="text" class="form-control" aria-describedby="inputGroup-sizing-default">
			</div>
			
			<div class="input-group mb-3">
			  <span class="input-group-text">Password</span>
			  <input id="password" style="text-transform:none;" type="password" class="form-control" aria-describedby="inputGroup-sizing-default">
			</div>
			
			<div class="input-group mb-3">
			  <span class="input-group-text">Retype Password</span>
			  <input id="rpassword" style="text-transform:none;" type="password" class="form-control" aria-describedby="inputGroup-sizing-default">
			</div>
		</div>

	</div>

</div>
