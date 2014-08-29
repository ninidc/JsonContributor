<?php

$section = isset($_GET["section"]) ? $_GET["section"] : null;


$jsonFile = "../_dynamic/data/en.json";
$data = file_get_contents($jsonFile);

$data = json_decode($data, true);

$sections = array();
foreach($data as $name=>$d) {
	$sections[] = $name;
}

foreach($sections as $s) {
	echo '<a href="?section='.urlencode($s).'">'.$s.'</a> - ';
}


if($section) {
	echo '<table>';
	foreach($data[$section] as $r) {
		echo '<tr>';
		if(is_array($r)) {
			foreach($r as $item) {
				echo '<td>'.$item.'</td>';
			} 
		}
		echo '</tr>';

	}
	echo '</table>';
}




?>
