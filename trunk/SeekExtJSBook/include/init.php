<?php
error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
set_magic_quotes_runtime(0);

define('ROOT_PATH', './');
define('SEEK_BOOK',TRUE);

if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
	exit('Request tainting attempted.');
}

require_once ROOT_PATH . 'include/config.php';
require_once ROOT_PATH . 'include/function.global.php';
require_once ROOT_PATH . 'include/function.ptime.php';
require_once ROOT_PATH . 'include/class.template.php';
require_once ROOT_PATH . 'include/class.mysql.php';

$db = new SeekMysql;
$db ->character = $db_char;
$db ->connect($db_host, $db_id, $db_pw, $db_db, $db_kind);
unset($db_host, $db_id, $db_pw, $db_db, $db_kind);

foreach(array('_COOKIE', '_POST', '_GET') as $_request) {
	foreach($$_request as $_key => $_value) {
		$_key{0} != '_' && $$_key = sAddslashes($_value);
	}
}

if (!get_magic_quotes_gpc() && $_FILES) $_FILES = sAddslashes($_FILES);

$getip     = getIp();
$timestamp = time();
$phpself   = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : baseName($_SERVER['SCRIPT_NAME']);
$selfurl   = 'http://'.$_SERVER['HTTP_HOST'].subStr($PHP_SELF, 0, strRPos($PHP_SELF, '/') + 1);
$version   = '0.01 Beta';

$tpl   = new SeekTemplate;

if(PHP_VERSION > '5.1') @date_default_timezone_set('Etc/GMT'.($timezone > 0 ? '-' : '+').(abs($timezone)));

if ($use_gzip) {
	require_once ROOT_PATH . 'include/function.gzip.php';
	gzip_ob_start();
}

if ($nocache) {
	@header("Cache-Control: no-cache, must-revalidate, max-age=0");
	@header("Expires: 0");
	@header("Pragma: no-cache");
}

if ($charset) @header('Content-Type: text/html; charset='.$charset);

$tpl->dir_skin = 'default';
?>