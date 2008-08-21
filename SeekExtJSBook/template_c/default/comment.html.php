<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Cache-Control" content="no-cache" />
<title>SeekBook-ExtJS Version - Powered By SeekStudio</title>
<link rel="stylesheet" type="text/css" href="images/comment.css" />
</head>
<body>
<div id="wapper">
<div id="comment">
<table id="box">
<tr>
<td class="label">暱稱 :</td>
<td class="text"><span style="color:<?php echo $sexColor[$row['sex']]; ?>"><?php echo htmlspecialchars($row['username']); ?></span></td>
<td class="label">電郵 : </td>
<td class="text"><?php echo htmlspecialchars($row['email']); ?></td>
</tr>
<tr>
<td class="field">留言 : </td>
<td class="board" colspan="3"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></td>
</tr>
</table>
</div>
<div id="reply"><div id="mark">- - - - - 以下為回覆資料 - - - - -</div></div>
<?php if (is_array($rowArr)) { foreach($rowArr as $rk => $rv) { ?>
<div id="reply-<?php echo $rv[rid]; ?>">
<table id="box">
<tr>
<td class="label">暱稱 :</td>
<td class="text"><span style="color:<?php echo $sexColor[$rv['sex']]; ?>"><?php echo htmlspecialchars($rv['username']); ?></span></td>
<td class="label">電郵 : </td>
<td class="text"><?php echo htmlspecialchars($rv['email']); ?></td>
</tr>
<tr>
<td class="field" rowspan="2">留言 : </td>
<td class="board" colspan="3"><?php echo nl2br(htmlspecialchars($rv['comment'])); ?></td>
</tr>
<tr>
<td class="time" colspan="3">發佈時間 : <?php echo $rv['adddate']; ?></td>
</tr>
</table>
</div>
<?php } } ?>
</div>
</body>
</html>