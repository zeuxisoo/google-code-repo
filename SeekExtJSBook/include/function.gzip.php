<?php
function gzip_ob_start($val=null) {
	global $gz_kind;

	if (headers_sent()) {
		return false;
	}
	if (!isset($gz_kind)) {
		if (ob_start($val)){
			$gz_kind = true;
		} else {
			$gz_kind = false;
		}
	}
	return $gz_kind;
}

function chk_gzip() {
	if (!gzip_ob_start()) {
		
		return false;

	} elseif (strpos(' '.$_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') !== false && function_exists('gzcompress')) {

		if (strpos(' '.$_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip') !== false) {
			return "x-gzip";
		} else {
			return "gzip";
		}
	} else {
		return false;
	}
}

function do_gzip($level=3, $debug=1) {
	global $db_debug;

	$ENCODING = chk_gzip();

	if ( $ENCODING) {

		$contents = ob_get_contents();
		
		$debug_contents = '';

		ob_end_clean();
		
		if ( $debug) {
		
			$debug_contents .= "	Not Compress Length: ".sizeTranslate(strlen($contents)).", ";
			$debug_contents .= "	Compressed Length: ".sizeTranslate(strlen(gzcompress($contents,$level))).", ";
			$debug_contents .= "	Compressed Level: $level";

		}else{
			$contents .= "\n<!-- Use compress $ENCODING($level) -->\n";
		}

		$contents = str_replace('<gz_debug></gz_debug>',$debug_contents,$contents);

		header("Content-Encoding: $ENCODING");
		echo pack('cccccccc', 0x1f, 0x8b, 0x08, 0x00, 0x00, 0x00, 0x00, 0x00);
		$size =	strlen($contents);
		$crc = crc32($contents);
		$contents =	gzcompress($contents, $level);
		$contents =	substr($contents, 0, strlen($contents) - 4);
		echo $contents;
		echo pack('V',$crc);
		echo pack('V',$size);
		exit;

	} else {

		echo "\n<!-- Use compress false -->\n";
		ob_end_flush();
		exit();

	}
}

function output() {
	global $gz_type, $gz_level, $gz_debug, $debug, $tpl, $db, $version;

	if ($debug == 1) {
		$gz_contents = ob_get_contents();
		ob_end_clean();
		include_once $tpl->display('debugpage.tpl');
	}else{
		if ($gz_type) {
			do_gzip($gz_level, $gz_debug);
		}
	}
}
?>