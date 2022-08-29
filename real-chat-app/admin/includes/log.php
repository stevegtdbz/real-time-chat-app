


<div style="padding:10px;overflow-y:scroll;font-size:13px;">
	<?php 
		$log = file_get_contents("../log");
		$lines = explode("\n",$log);
		for($i=0; $i<sizeof($lines); $i++){
			echo $lines[$i]."<br>";
		}
	?>
</div>
