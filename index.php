<?php
	require "/common.php";
?>
<html>
<head>
</head>
<body>
	url  : <input type="text" value="http://" name="url" id="url" size="100" />&nbsp;<input type="button" name="show" id="show" value="전송" onclick="document.getElementById('webpage').src = '<?=fixedUrl?>'+ document.getElementById('url').value;"/>
	<br /><br />
	<iframe src="" width="100%" height="100%" id='webpage'></iframe>


</body>
</html>
