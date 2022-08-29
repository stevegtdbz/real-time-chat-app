// Global Variables
military_ranks = [
	"ΣΤΡ:ΣΤΡΑΤΙΩΤΗΣ",
	"ΔΚΝΣ:ΔΕΚΑΝΕΑΣ_ΕΠΟΠ",
	"ΔΚΝΣ:ΔΕΚΑΝΕΑΣ_ΣΜΥ",
	"ΛΧΙΑΣ:ΛΟΧΙΑΣ_ΕΠΟΠ",
	"ΛΧΙΑΣ:ΛΟΧΙΑΣ_ΣΜΥ",
	"ΕΠΛΧΙΑΣ:ΕΠΙΛΟΧΙΑΣ_ΕΠΟΠ",
	"ΕΠΛΧΙΑΣ:ΕΠΙΛΟΧΙΑΣ_ΣΜΥ",
	"ΑΛΧΙΑΣ:ΑΡΧΙΛΟΧΙΑΣ_ΕΠΟΠ",
	"ΑΛΧΙΑΣ:ΑΡΧΙΛΟΧΙΑΣ_ΣΜΥ",
	"ΥΠΣΤΗΣ:ΥΠΑΣΠΙΣΤΗΣ",
	"ΔΕΑ:ΔΟΚΙΜΟΣ ΕΦΕΔΡΟΣ ΑΞΙΩΜΑΤΙΚΟΣ",
	"ΑΝΘΓΟΣ:ΑΝΘΥΠΟΛΟΧΑΓΟΣ",
	"ΥΠΓΟΣ:ΥΠΟΛΟΧΑΓΟΣ",
	"ΛΓΟΣ:ΛΟΧΑΓΟΣ",
	"ΤΧΗΣ:ΤΑΓΜΑΤΑΡΧΗΣ",
	"ΑΝΧΗΣ:ΑΝΤΙΣΥΝΤΑΓΜΑΤΑΡΧΗΣ",
	"ΣΧΗΣ:ΣΥΝΤΑΓΜΑΤΑΡΧΗΣ",
	"ΤΞΧΟΣ:ΤΑΞΙΑΡΧΟΣ",
	"ΥΠΓΟΣ:ΥΠΟΣΤΡΑΤΗΓΟΣ",
	"ΑΝΓΟΣ:ΑΝΤΙΣΤΡΑΤΗΓΟΣ",
	"ΣΤΡΓΟΣ:ΣΤΡΑΤΗΓΟΣ"
];

military_corps = [
	"ΠΖ:ΠΕΖΙΚΟ",
	"ΠΒ:ΠΥΡΟΒΟΛΙΚΟ",
	"ΕΠ:ΕΡΕΥΝΑΣ ΠΛΗΡΟΦΟΡΙΚΗΣ",
];

function logout(){
	$.getJSON("./api.php", {"logout":""}, function (resp){
		location.reload();
	});
}

function getMilitaryRanks(){
	html = "";
	for(i=0;i<military_ranks.length;i++){
		html+="<option value='"+military_ranks[i]+"'>"+military_ranks[i].split(":")[1]+"</option>";
	}
	return html;
}

function getMilitaryCorps(){
	html = "";
	for(i=0;i<military_corps.length;i++){
		html+="<option value='"+military_corps[i]+"'>"+military_corps[i].split(":")[1]+"</option>";
	}
	return html;
}
