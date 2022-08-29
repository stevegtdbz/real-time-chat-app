<script>
	
	$(document).ready(function() {
		getMembers(null);
		
		$('#search-member').on("input", function() {
			var term = $("#search-member").val();
			if(term.length > 3) getMembers(term);
			if(term.length == 0) getMembers(null);
		});
	});
	
	function getMembers(search){
		$("#table-members tbody").empty();
		
		$.getJSON("./api.php", {"get-members":search}, function (resp){
			
			html = "";
			for(i=0;i<resp.data.length;i++){
				rec = resp.data[i];
				$("#table-members tbody").append(`<tr>
					<td>`+rec.military_id+`</td>
					<td style="text-transform:none;">`+rec.username+`</td>
					<td>`+rec.name+`</td>
					<td>`+rec.surname+`</td>
					<td><span class="status-bullet-`+rec.status+`" style="margin-top:3px;"></span>`+rec.status+`</td>
					<td style="width:100px;">
						<a href="#" onclick="unlockMember(`+rec.id+`)" class="btn btn-warning" title="Ξεκλείδωμα">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock" viewBox="0 0 16 16">
							<path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2zM3 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1H3z"/>
							</svg>
						</a>
						<a href="?page=edit-member&id=`+rec.id+`" class="btn btn-primary" title="Επεξεργασία">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
							  <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
							</svg>
						</a>
						<a href="javascript:;" class="btn btn-danger" title="Διαγραφή" onclick="deleteMember(`+rec.id+`)">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
							  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
							  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
							</svg>
						</a>
					</td>
				</tr>`);
			}
						
		});
	}
	
	function deleteMember(mid){
		$.getJSON("./api.php", {"delete-member":mid}, function (resp){
			getMembers(null);
		});
	}
	
	function unlockMember(mid){
		$.getJSON("./api.php", {"unlock-member":mid}, function (resp){
			Swal.fire({ title: 'Account unlocked!'});
		});
	}

</script>

<style>
#table-members{
	width: 100%;
}

#table-members .btn{
	font-size: 14px;
	padding: 5px;
}
</style>

<!-- Manage Members Container -->
<div style="padding:10px;">
	
	<!-- Content bar -->
	<div>
		<a class="btn btn-success" href="?page=edit-member" role="button" style="float:right;">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
			  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
			</svg>
			Add member
		</a>
		
		<div class="input-group mb-3" style="float:left;width:auto;">
		  <input type="text" id="search-member" class="form-control" placeholder="Search Member">
		  <a class="btn btn-outline-success" type="button" onclick="getMembers($(this).prev().val())">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
			  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
			</svg>
		  </a>
		</div>
		
		<div style="clear:both;"></div>
	</div>
	
	<table id="table-members">
		<thead>
			<tr>
				<th>ID</th>
				<th>username</th>
				<th>Name</th>
				<th>Surname</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	
	
</div>
