<?php
	require "/common.php";
?>
<html>
<head>
	<style>
		body{ margin:10px; padding:10px; border:0px;}
		span{float:left; display:inline-block;}
		input[type=text] {width: 70% !important; height:30px; display:inline-block;}
		input[type=button] {width: 10% !important;; height:30px; display:inline-block;}
		#urlWriteArea { float:left; display:inline-block; width:100%; height:inherit; }
	</style>
</head>
<body>
	<div id="urlWriteArea">
	<input type="text" value="http://" name="url" id="url"  />&nbsp;
		<input type="button" name="show" id="show" value="URL전송" onclick="document.getElementById('webpage').src = ('<?=fixedUrl?>'+ document.getElementById('url').value);"/>
	</div>
	<br /><br />
	<iframe src="" width="100%" height="100%" id='webpage' frameborder="1"></iframe>


</body>
</html>
