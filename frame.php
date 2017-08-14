<?php
	require "/common.php";
ini_set('max_execution_time', 300);
function getDataURI($imageurl, $mime = '') {
	/*var_dump($imageurl);*/
	$info = @getimagesize($imageurl);
	$result = $imageurl;


	if ($info['mime'] == 'image/jpeg' or $info['mime'] == 'image/jpg') {
		ob_start ();
		$image = imagecreatefromjpeg($imageurl);
		imagejpeg($image, null, 30);
		$image_data = ob_get_contents ();
		$result = 'data: '.$info['mime'].';base64,'.base64_encode ($image_data);

		ob_end_clean ();
	}elseif ($info['mime'] == 'image/gif') {
		ob_start ();
		$image = imagecreatefromgif($imageurl);
		imagegif($image, null,30);
		$image_data = ob_get_contents ();
		$result = 'data: '.$info['mime'].';base64,'.base64_encode ($image_data);

		ob_end_clean ();
	}
	/*elseif ($info['mime'] == 'image/png'){
		$image = imagecreatefrompng($imageurl);
		imagepng($image, null,100);
	}*/




	/*return 'data: '.$info['mime'].';base64,'.base64_encode(file_get_contents($imageurl));*/
	return $result;

}

function getUrl($url){
	$parts = parse_url($url);
	$href = "";

	if(isset($parts['scheme'])){
		if (0 !== strpos($url, 'http')) {
			$href = $parts['scheme'] . '://';
		}else{
			$href = 'http://';
		}
	}
/*var_dump($parts['host'], $url);*/

	$href .= $parts['host'];
	if (isset($parts['port'])) {
		$href .= ':' . $parts['port'];
	}

	return $href;
};

function getFullUrl($url, $originUrl){
	$parts = parse_url($url);
	$href = "";
/*var_dump($url, $originUrl);*/

	if ( strpos($url, 'http') !== false) {
		if(isset($parts['scheme'])) {
			$href = $parts['scheme'] . '://';
		}else {
			$href = 'http://';
		}
	}else{
		$href = 'http://';
	}


	if( ((!isset($parts['host']))  or ($parts['host'] == null))   ){
		$href .= $originUrl;
/*		if(strpos($url,"bg_slidebar.png") !== false){
			var_dump($href);
		}*/
	}else{
		$href .= $parts['host'];


	}


	if (isset($parts['port'])) {
		$href .= ':' . $parts['port'];
	}

	if (isset($parts['path'])) {
		$href .=  $parts['path'];
	}

	return $href;
};

function crawl_page($url, $depth = 1)
{
    static $seen = array();
    if (isset($seen[$url]) || $depth === 0) {
        return;
    }
	$base_url = parse_url($url)["host"];


    $seen[$url] = true;

    $dom = new DOMDocument('1.0','utf-8');
    @$dom->loadHTMLFile($url);

    $anchors = $dom->getElementsByTagName('a');
    $link = $dom->getElementsByTagName('link');
    $script = $dom->getElementsByTagName('script');
	$img = $dom->getElementsByTagName('img');

    foreach ($anchors as $element) {
		$href = $element->getAttribute('href');

        if (0 !== strpos($href, 'javascript')) {
	        if (0 !== strpos($href, 'http')) {
				$element->setAttribute('href',fixedUrl.getUrl($url).$href);
			}else{
				$element->setAttribute('href',fixedUrl.$href);

			}
			$element->setAttribute("target","_self");
        }

    }
//
    foreach ($img as $element) {
		$src = $element->getAttribute('src');
		$element->setAttribute('src', getDataURI(getFullUrl($src,$base_url)));
    }

    foreach ($link as $element) {
		$href = $element->getAttribute('href');

        if (0 !== strpos($href, 'http')) {
			$href = getFullUrl($href,$base_url);
		}

		$element->setattribute('href',$href);
		/*$header = @get_headers($href);

		if($header !== false  ){
			if(findStr($header,"css")){
				$element->parentNode->appendChild($dom->createElement('style', (crawl_css_page($href))));
				$element->parentNode->removeChild($element);
			}
		}*/
    }

/*	
	while ($link->length > 0) {
		$p = $link->item(0);
		$p->parentNode->removeChild($p);
	}
*/
/*	while ($link->length > 0) {
		$p = $link->item(0);

		$p->parentNode->appendChild($dom->createElement('style', crawl_css_page($p->getAttribute('href'))));
		$p->parentNode->removeChild($p);
	}*/


/*	while ($img->length > 0) {
		$p = $img->item(0);
		$p->parentNode->removeChild($p);
	}*/

	while ($script->length > 0) {
		$p = $script->item(0);
		$p->parentNode->removeChild($p);
	}
    /*echo "URL:",$url,PHP_EOL,"CONTENT:",PHP_EOL,finalDataReplace($dom->saveHTML(), $base_url),PHP_EOL,PHP_EOL;*/
	echo finalDataReplace($dom->saveHTML(), $base_url),PHP_EOL,PHP_EOL;
}


function crawl_css_page($link, $base_url = ''){
	$html = file_get_contents($link);

	if($html === false){
		return "";
	}
	preg_match_all("/background.+?url\s?[(]\s?[\"|']?(.+?)[\"|']?[)]/i", $html, $matches);

	for($i =0; count($matches[1]) > $i; $i++) {
		$link = $matches[0][$i];
		$url = $matches[1][$i];

		$size = retrieve_remote_file_size($url);
		$convertUrl = getFullUrl($url, $base_url);
		if(round($size / 1024, 2) > 10){		//100kb

			/*$html = str_replace("$link", "", $html);*/
			$html = str_replace("$link", getDataURI($convertUrl), $html);
		}else {
			$html = str_replace("$link", getDataURI($convertUrl), $html);

		}

		/*var_dump(getRemoteFilesize($url) );*/

	}
	/*$html = preg_replace("/background\s*[:].*url.+?([;]|[}]|[\n])/i","",$html);*/
	$html = preg_replace("/font[-]face.+?([;]|[}]|[\\n])/i","",$html);

	return $html;
}

function findStr($arr, $str)
{
	if(!is_array($arr)){ return false; }
	foreach ($arr as &$s)
	{
		if(strpos($s, $str) !== false)
			return true;
	}

	return false;
}

function finalDataReplace($html, $base_url = ''){



//Get the urls out of the page
	preg_match_all("/[<]\s*link.+?href\s?[=]\s?[\"|']?(.+?)[\"|'].*?[>]/", $html, $matches);

	for($i =0; count($matches[1]) > $i; $i++){
		$url = $matches[1][$i];
		$link = $matches[0][$i];

		if(strpos($url, "css") !== false){
			$html = str_replace("$link", "<style>".crawl_css_page($url, $base_url)."</style>", $html);
		}
	}
	/*$html = preg_replace("/[<]/i","",$html);*/
	/*$html = preg_replace("/font[-]face.+?([;]|[}])/i","",$html);*/

	return $html;
}

crawl_page($_REQUEST["url"],1);
// background\s*[:]\s*url.+?([;]|[}]) , ""
// font[-]face.+?([;]|[}]);
function retrieve_remote_file_size($url){
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_NOBODY, TRUE);

	$data = curl_exec($ch);
	$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);



	curl_close($ch);
	return $size;
}

function file_size_format($clen){
	$size = $clen;
	switch ($clen) {
		case $clen < 1024:
			$size = $clen .' B'; break;
		case $clen < 1048576:
			$size = round($clen / 1024, 2) .' KiB'; break;
		case $clen < 1073741824:
			$size = round($clen / 1048576, 2) . ' MiB'; break;
		case $clen < 1099511627776:
			$size = round($clen / 1073741824, 2) . ' GiB'; break;
	}
	return $size;
}

?>
