<?php
	require "/common.php";
?>
<!doctype html>
<html lang="ko">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<meta http-equiv="Content-Style-Type" content="text/css">

	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<style>
		body{ margin:10px; padding:10px; border:0px; }
		span{float:left; display:inline-block;}
		input[type=text] {width: 50% !important; height:30px; display:inline-block;}
		input[type=button] {width: 40px !important;; height:30px; display:inline-block;}
		#urlWriteArea { float:left; display:inline-block; width:100%; height:inherit; }
	</style>
</head>
<body>
<div id="urlWriteArea">
	<input type="text" value="https://m.naver.com/" name="url" id="url"  />&nbsp;
	<input type="button" name="show" id="show" value="▶" onclick="document.getElementById('webpage').src = ('<?=fixedUrl?>'+ document.getElementById('url').value);"/>
</div>
<br /><br />
<iframe src="" width="70%" height="100%" style="height:100% !important; min-height:500px;" id='webpage' frameborder="1"></iframe>


</body>
</html>