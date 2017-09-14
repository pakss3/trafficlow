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
		body{ margin:10px; padding:10px; border:0px; }
		span{float:left; display:inline-block;}
		#url {width: 50% !important; height:30px; display:inline-block; ; text-align:left;}
		#sizex, #sizey {width: 5% !important; height:30px; display:inline-block; text-align:center;}
		input[type=button] {width: 40px !important;; height:30px; display:inline-block;}
		#urlWriteArea { float:left; display:inline-block; width:100%; height:inherit; }
	</style>


</head>
<body>
<div id="urlWriteArea">
	<input type="text" value="http://m.ssu.ac.kr/html/themes/m/html/index.jsp" name="url" id="url"  />&nbsp;
	<input type="button" name="show" id="show" value="▶" onclick="show()"/>
	<input type="text" value="" name="sizex" id="sizex" /> x <input type="text" value="" name="sizey" id="sizey" />
	<select id="quality" name="quality">
		<option value="10">10</option>
		<option value="20">20</option>
		<option value="30">30</option>
		<option value="40">40</option>
		<option value="50">50</option>
		<option value="60">60</option>
		<option value="70">70</option>
		<option value="80">80</option>
		<option value="90">90</option>
		<option value="100">100</option>
	</select>
</div>
<div id="contents">
	<iframe src="" width="70%" height="100%" style="height:100% !important; min-height:500px; width:70% !important; min-width:330px;" id='webpage' name='webpage' frameborder="0"></iframe>

</div>

<script>

	setTimeout(function(){
		document.getElementById("sizex").value = window.innerWidth  || window.body.clientWidth;
		document.getElementById("sizey").value = window.innerHeight || window.body.clientHeight;
	},200);
	function show(){
		var quality = document.getElementById('quality').value,
			sizex = document.getElementById('sizex').value,
			sizey = document.getElementById('sizey').value;

		document.getElementById('webpage').src = ('<?=fixedUrl?>'+ document.getElementById('url').value+"&quality="+quality +"&sizex="+sizex+"&sizey="+sizey);


	}
</script>
</body>
</html>