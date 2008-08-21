<?php
function import($f, $k = '') {
	global $tempDataPath;

	switch($k) {
		case 'data':
			$fp = $tempDataPath . '/' . $f;
			break;
		default:
			$fp = ACP_ROOT . $f;
			break;
	}
	if (!is_file($fp)) {
		sExit('Cant Not Load ['.$f.'] File');
	}else{
		return $fp;
	}
}

function authCode($string, $operation = 'ENCODE') {
	return ($operation == 'DECODE' ? base64_decode($string) : base64_encode($string));
}
?>