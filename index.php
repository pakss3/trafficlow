<?php
	require "/common.php";
?>
<html>
<head>
</head>
<body>
	url  : <input type="text" value="https://www.google.co.kr" name="url" id="url" size="100" onclick="document.getElementById('webpage').src = '<?=fixedUrl?>'+this.value;"/>&nbsp;<input type="button" name="show" id="show" value="전송" />
	<br /><br />
	<iframe src="" width="100%" height="100%" id='webpage'></iframe>


</body>
</html>
