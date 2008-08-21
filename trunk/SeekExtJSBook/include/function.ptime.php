<?php
function starttime() {
	list($seconds, $mseconds) = explode(' ', microtime());
	return (real)((float)$seconds + (float)$mseconds);
}

function processtime() {
	global $starttime;

	$endtime = starttime();
	return round($endtime - $starttime, 6);
}

function debuginfo() {
	global $db, $gz_type, $gz_level, $debuginfo;

	$proctime = processtime();
	$gz_type  = ($gz_type) ? "ON [Level: $gz_level]" : "OFF";
	$total_q  = $db->querycount + $db->updatecount;
	$debuginfo= array('proctime'=>$proctime,'sqlqueries'=>$total_q.' ('.$db->querycount.'+'.$db->updatecount.')','gz_type'=>$gz_type);
	
}
?>