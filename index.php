<?php
require "common.php";
?>
<!doctype html>
<html lang="ko">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<meta http-equiv="Content-Style-Type" content="text/css">

	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<style>
		html, body { height:100%; overflow:hidden }
		#contents {  /*border:1px solid red;*/ height:auto; height:90%; }
		#urlWriteArea { height:5%; }

		#url {width: 60%; height:1.5em; display:inline-block; ; text-align:left;}
		#sizex, #sizey {width: 8% !important; height:1.5em; display:inline-block; text-align:center;}
		input[type=button] {width: 5%; height:1.5em; display:inline-block;}

	</style>

</head>
<body>
<div id="urlWriteArea">
	<input type="text" value="http://m.ssu.ac.kr/html/themes/m/html/index.jsp" name="url" id="url"  />&nbsp;
	<input type="button" name="show" id="show" value="▶" onclick="document.getElementById('webpage').src = ('<?=fixedUrl?>'+ document.getElementById('url').value);"/>
	<input type="text" value="" name="sizex" id="sizex" /> x <input type="text" value="" name="sizey" id="sizey" />
</div>
<div id="contents">
	<iframe src="" width="70%" height="100%" style="height:100% ; width:100% ; " id='webpage' frameborder="1"></iframe>

</div>

<script>
	window.onload = function(){
		document.getElementById("sizex").value = window.innerWidth  || window.body.clientWidth;
		document.getElementById("sizey").value = window.innerHeight || window.body.clientHeight;
	};
</script>
</body>
</html>