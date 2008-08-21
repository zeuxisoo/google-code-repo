<?php
function sExit($m) {
	exit("$m");
}

function sAddslashes($string){
	if(!get_magic_quotes_gpc()){
		return is_array($string) ? array_map('sAddslashes',$string) : addslashes($string);
	} else {
		return $string;
	}
}

function getIp() {
	if(getEnv('HTTP_CLIENT_IP') && strCaseCmp(getEnv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getEnv('HTTP_CLIENT_IP');
	} elseif(getEnv('HTTP_X_FORWARDED_FOR') && strCaseCmp(getEnv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$onlineip = getEnv('HTTP_X_FORWARDED_FOR');
	} elseif(getEnv('REMOTE_ADDR') && strCaseCmp(getEnv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getEnv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strCaseCmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	$onlineip = preg_replace("/^([\d\.]+).*/", "\\1", $onlineip);

	return $onlineip;
}

function showMsg($msg, $kind = 'MSG', $url = '', $timeout = 3) {
	global $tpl;
	include_once $tpl->display('showmsg.html');
	exit;
}

function dateFormat($timeval = '', $format = "Y-m-d H:i:s") {
	global $timezone, $timestamp;
	$timeval = (!$timeval) ? $timestamp : $timeval;
	return gmDate($format, $timeval+$timezone*3600);
}

function rFile($filename, $mode = 'r+') {
	$f = fopen($filename, $mode);
	flock($f, LOCK_EX);
	$n = fread($f, fileSize($filename));
	flock($f, LOCK_UN);
	fclose($f);

	return $n;
}

function wFile($filename, $data, $mode = 'w+') {
	$f = fopen($filename, $mode);
	flock($f, LOCK_EX);
	fwrite($f, $data);
	flock($f, LOCK_UN);
	fclose($f);
}

function multi($num, $perpage, $curpage, $mpurl) {
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		$page = 10;
		$offset = 5;
		$pages = @ceil($num / $perpage);
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $curpage + $page - $offset - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $curpage - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="p_redirect">&laquo;</a>' : '').($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="p_redirect">‹</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<span class="p_curpage">'.$i.'</span>' : '<a href="'.$mpurl.'page='.$i.'" class="p_num">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="p_redirect">›</a>' : '').($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="p_redirect">&raquo;</a>' : '');
		$multipage = $multipage ? '<div class="p_bar"><span class="p_info">Records:'.$num.'</span>'.$multipage.'</div>' : '';
	}
	return $multipage;
}
?>