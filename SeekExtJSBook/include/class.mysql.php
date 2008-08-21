<?php
/*
 * Author : Zeuxis Lo
 * Data   : 2007/09/28 21:04
 * Version: v0.001 Beta
 * P-Name : Seek MYSQL Class
 *
 */
class SeekMysql {

	var $explain;
	var $querytime;
	var $querycount = 0;
	var $updatecount= 0;
	var $character  = '';
	var $debug      = 0;

	function error() {
		return mysql_error();
	}

	function geterrno() {
		return mysql_errno();
	}

	function insert_id() {
		return mysql_insert_id();
	}

	function connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect=0) {
		if($usepconnect) {
			if(!@mysql_pconnect($servername, $dbusername, $dbpassword)) {
				$this->halt('Can not connect to MySQL Server');
			}
		} else {
			if(!@mysql_connect($servername, $dbusername, $dbpassword)) {
				$this->halt('Can not connect to MySQL Server');
			}
		}
		if(mysql_get_server_info() > '4.1') {
			$get_charset = str_replace('-', '', $this->character);
			mysql_query("SET NAMES '".$get_charset."'");
			mysql_query("SET CHARACTER_SET_CLIENT = '$get_charset';");
			mysql_query("SET CHARACTER_SET_RESULTS = '$get_charset';");
		}
		if(mysql_get_server_info() > '5.0'){
			mysql_query("SET sql_mode=''");
		}
		$this->select_db($dbname);
	}

	function select_db($dbname) {
		if (!@mysql_select_db($dbname)) {
			$this->halt('Can not select to MySQL DataBase');
		}
	}

	function query($sql, $query_type = '') {
		$starttime = starttime();
		if($query_type == 'U_B' && @function_exists('mysql_unbuffered_query')) {
			$query = mysql_unbuffered_query($sql);
		} else {
			if($query_type == 'CACHE' && intval(mysql_get_server_info()) >= 4) {
				$sql = 'SELECT SQL_CACHE'.substr($sql, 6);
			}
			if(!($query = mysql_query($sql)) && $query_type != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}
		$endtime = processtime();
		$this->querytime += $endtime;
		$this->querycount++;
		if($this->debug) {
			$sql = str_replace("\n","",trim($sql));
			$this->explain_query($sql, $endtime);
		}
		return $query;
	}

	function update($sql, $query_type = '') {
		$starttime = starttime();
		if(@function_exists('mysql_unbuffered_query')) {
			$query = mysql_unbuffered_query($sql);
		} else {
			if($query_type == 'CACHE' && intval(mysql_get_server_info()) >= 4) {
				$sql = 'SELECT SQL_CACHE'.substr($sql, 6);
			}
			if(!($query = mysql_query($sql)) && $query_type != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}
		$endtime = processtime();
		$this->querytime += $endtime;
		$this->updatecount++;
		if($this->debug) {
			$this->explain_query($sql, $endtime);
		}
		return $query;
	}

	function explain_query($sql, $qtime) {
		if(preg_match("#^select#i", $sql)) {

			$query = mysql_query("EXPLAIN $sql");
			$this->explain .= "<table width='95%' class='alltable' cellpadding='4' cellspacing='1' align=\"center\">";
			$this->explain .= "<tr>";
			$this->explain .= "	<td colspan='8' class='tabletitle'><strong>#".$this->querycount." - Select Query</strong></td>";
			$this->explain .= "</tr>";
			$this->explain .= "<tr>";
			$this->explain .= "	<td colspan='8' class='sqlcode'>".$sql."</td>";
			$this->explain .= "</tr>";
			$this->explain .= "<tr class='sqltitle'>";
			$this->explain .= "	<td><strong>table</strong></td>";
			$this->explain .= "	<td><strong>type</strong></td>";
			$this->explain .= "	<td><strong>possible_keys</strong></td>";
			$this->explain .= "	<td><strong>key</strong></td>";
			$this->explain .= "	<td><strong>key_len</strong></td>";
			$this->explain .= "	<td><strong>ref</strong></td>";
			$this->explain .= "	<td><strong>rows</strong></td>";
			$this->explain .= "	<td><strong>Extra</strong></td>";
			$this->explain .= "</tr>";

			while($table = mysql_fetch_array($query)) {
				$this->explain .= "<tr class='sqlinfo'>";
				$this->explain .= "	<td>".$table['table']."</td>";
				$this->explain .= "	<td>".$table['type']."</td>";
				$this->explain .= "	<td>".$table['possible_keys']."</td>";
				$this->explain .= "	<td>".$table['key']."</td>";
				$this->explain .= "	<td>".$table['key_len']."</td>";
				$this->explain .= "	<td>".$table['ref']."</td>";
				$this->explain .= "	<td>".$table['rows']."</td>";
				$this->explain .= "	<td>".$table['Extra']."</td>";
				$this->explain .= "</tr>";
			}

			$this->explain .= "<tr class='sqltime'>";
			$this->explain .= "	<td colspan='8'>Query Time: ".$qtime."</td>";
			$this->explain .= "</tr>";
			$this->explain .= "</table>";
			$this->explain .= "<br />";
		
		} else {

			$this->explain .= "<table width='95%' cellpadding='4' cellspacing='1' align=\"center\">";
			$this->explain .= "<tr>";
			$this->explain .= "	<td><strong>#";$this->querycount." - Update Query</strong></td>";
			$this->explain .= "</tr>";
			$this->explain .= "<tr>";
			$this->explain .= "	<td><span>";$sql."</span></td>";
			$this->explain .= "</tr>";
			$this->explain .= "<tr>";
			$this->explain .= "	<td>Query Time: ";$qtime."</td>";
			$this->explain .= "</tr>";
			$this->explain .= "</table>";
			$this->explain .= "<br />";

		}
	}

	function fetch_array($query, $type = MYSQL_ASSOC) {
		return mysql_fetch_array($query,$type);
	}

	function fetch_row($query) {
		return mysql_fetch_row($query);
	}

	function fetch_one($query, $type = MYSQL_ASSOC) {
		$result = $this->query($query);
		$record = $this->fetch_array($result, $type);
		$this->free_result($result);
		return $record;
	}

	function fetch_two($query, $type = MYSQL_ASSOC) {
		$result = $this->query($query);
		
		while($records = $this->fetch_array($result, $type)) {
			$record[] = $records;
		}

		$this->free_result($result);
		return $record;
	}

	function num_rows($query) {
		return mysql_num_rows($query);
	}

	function result($query, $row) {
		return mysql_result($query, $row);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function close() {
		return mysql_close();
	}

	function halt($msg,$sql=""){
		global $charset;

		$author_mark = "3*JTNDc3BhbiUyMG9uY2xpY2slM0QlMjJ3aW5kb3cubG9jYXRpb24lM0QlMjdodHRwJTNBJTJGJTJGbmVyby4zamsuY29tJTJGJTI3JTIyJTNFU2Vla1N0dWRpbyUzQyUyRnNwYW4lM0U=";

		$message  = "<html>";
		$message .= "<head>";
		$message .= "<meta content=\"text/html; charset=".$charset."\" http-equiv=\"Content-Type\">";
		$message .= "<style type=\"text/css\">";
		$message .= "body,td,pre {";
		$message .= "	font-family : Tahoma, sans-serif; font-size : 9pt;";
		$message .= "}";
		$message .= "td {";
		$message .= "	background-color:#FFFFFF";
		$message .= "}";
		$message .= "</style>";
		$message .= "</head>";
		$message .= "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#006699\" vlink=\"#5493B4\">";
		
		$message .= "<div align='left'>";
		$message .= "<table cellpadding='3' cellspacing='1' border='0'>";
		$message .= "<tr>";
		$message .= "	<td colspan='3' style='color:#FF0000'><b>[SK-SYSTEM!] DataBase Error Messages - By ".rawurldecode(base64_decode(substr($author_mark,2,138)))."</b></td>";
		$message .= "</tr>";
		$message .= "<tr>";
		$message .= "	<td><b>Time</b></td><td align='center' width='5%'> :: </td><td align='left'>".date("Y-m-d H:i")."</td>";
		$message .= "</tr>";
		$message .= "<tr>";
		$message .= "	<td><b>Error</b></td><td align='center'> :: </td><td align='left'>".$this->error()."</td>";
		$message .= "</tr>";
		$message .= "<tr>";
		$message .= "	<td><b>ErrorNo</b></td><td align='center'> :: </td><td align='left'>".$this->geterrno()."</td>";
		$message .= "</tr>";
		if ($sql) {
			$message .= "<tr>";
			$message .= "	<td><b>Query</b></td><td align='center'> :: </td><td align='left'>".$sql."</td>";
			$message .= "</tr>";
		}
		$message .= "<tr>";
		$message .= "	<td><b>Script</b></td><td align='center'> :: </td><td align='left'>http://".$_SERVER['HTTP_HOST'].getenv("REQUEST_URI")."</td>";
		$message .= "</tr>";
		$message .= "<tr>";
		$message .= "	<td><b>Message</b></td><td align='center'> :: </td><td align='left'>".htmlspecialchars($msg)."</td>";
		$message .= "</tr>";
		$message .= "</table>";

		$message .= "</div></body></html>";
		echo $message;
		exit;
	}
}
?>