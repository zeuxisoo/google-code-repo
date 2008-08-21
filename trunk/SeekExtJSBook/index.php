<?php
require_once 'include/init.php';

switch(isset($sk) ? $sk : null) {
	case 'reply':
		if (empty($cid)) sExit('{success:true,msg:\'留言識別碼錯誤\'}');
		if (empty($username)) sExit('{success:true,msg:\'請輸入你的暱稱\'}');
		if (empty($sex)) sExit('{success:true,msg:\'請輸入你的性別\'}');
		if (empty($email)) sExit('{success:true,msg:\'請輸入電郵地址\'}');
		if (empty($comment)) sExit('{success:true,msg:\'請輸入留言內容\'}');

		$sex = ($sex == '男孩') ? 1 : 2;

		$db->query("INSERT INTO ".$db_pre."reply (cid, username, sex, email, comment, adddate) VALUES ('$cid', '$username', '$sex', '$email', '$comment', '$timestamp')");

		echo '{success:true,msg:\'ok\'}';
		break;
	case 'view':
		$row = $db->fetch_one("SELECT * FROM ".$db_pre."comment WHERE cid = ".intval($cid));

		$rowArr= array();
		$query = $db->query("SELECT * FROM ".$db_pre."reply WHERE cid = ".intval($cid)." ORDER BY adddate ASC");
		while($re=$db->fetch_array($query)) {
			$rowArr[] = array(
								'rid' => $re['rid'],
								'username' => htmlspecialchars($re['username']),
								'sex' => $re['sex'],
								'email' => $re['email'],
								'comment' => $re['comment'],
								'adddate' => dateFormat($re['adddate'], "Y-m-d H:i (D)")
							);
		}

		include_once $tpl->display('comment.html');
		break;
	case 'list':
		$startLimit = isset($start) ? $start : 0;

		$rowArr= array();
		$total = $db->result($db->query("SELECT COUNT(*) FROM ".$db_pre."comment"),0);
		$query = $db->query("
						SELECT c.*, (SELECT COUNT(*) FROM skbkej_reply WHERE cid = 1) as replynum 
						FROM skbkej_comment c
						ORDER BY c.adddate DESC 
						LIMIT 0 , 30 
					");
		while($row=$db->fetch_array($query)) {
			$rowArr[] = array(
							'cid' => $row['cid'],
							'username' => htmlspecialchars($row['username']),
							'title' => htmlspecialchars($row['title']),
							'sex' => $row['sex'],
							'replynum' => $row['replynum'],
							'adddate' => dateFormat($row['adddate'], "Y-m-d H:i (D)")
						);
		}

		echo json_encode(array('rows'=>$rowArr, 'results'=>$total));

		break;
	case 'add':
		if (empty($username)) sExit('{success:true,msg:\'請輸入你的暱稱\'}');
		if (empty($title)) sExit('{success:true,msg:\'請輸入留言標題\'}');
		if (empty($sex)) sExit('{success:true,msg:\'請輸入你的性別\'}');
		if (empty($email)) sExit('{success:true,msg:\'請輸入電郵地址\'}');
		if (empty($comment)) sExit('{success:true,msg:\'請輸入留言內容\'}');

		$sex = ($sex == '男孩') ? 1 : 2;

		$db->query("INSERT INTO ".$db_pre."comment (username, title, sex, email, comment, adddate) VALUES ('$username', '$title', '$sex', '$email', '$comment', '$timestamp')");

		echo '{success:true,msg:\'ok\'}';
		break;
	default:
		include_once $tpl->display('index.html');
		break;
}
?>