<?php
require "common.php";
require 'jsmin.php';
function getImgMaxWidthSize(){
    $defaultX = "200";
    return (!isset($_REQUEST["sizex"])) ? $defaultX : $_REQUEST["sizex"];
}
function getImgMaxHeightSize(){
    $defaultY = "200";
    return (!isset($_REQUEST["sizey"])) ? $defaultY : $_REQUEST["sizey"];
}
function getDataURI($imageurl) {
    if((strpos($imageurl,"base64") !== false) or (strpos($imageurl,imageResizeUrl) !== false) or (strpos($imageurl,"sizey") !== false) or (strpos($imageurl,"sizex") !== false) ){
        return $imageurl;
    }

    return trim(imageResizeUrl.($imageurl)."&sizex=".getImgMaxWidthSize()."&sizey=". getImgMaxHeightSize());
}
function addScriptMinifier($srcUrl) {
    if(strpos($srcUrl,"jsmini") !== false ){
        return $srcUrl;
    }
    return trim(jsMiniUrl.($srcUrl));
}

function getUrl($url, $originUrl){
    $info  = parse_url($url);
    $result = "";
    if (!isset($info["scheme"])){
        $result = "http://";
    }else{
        $result = $info["scheme"]."://";
    }
    if( (!isset($info['host']))  or ($info['host'] == "")   ){
        $result .= $originUrl;
    }else{
        $result .= $info['host'];
    }
    if (isset($info['port'])) {
        $result .= ':' . $info['port'];
    }
    return $result;
};
function getFullUrl($url, $originUrl){
    $info  = parse_url($url);
    $result = "";
    if (!isset($info["scheme"])){
        $result = "http://";
    }else{
        $result = $info["scheme"]."://";
    }
    if( (!isset($info['host']))  or ($info['host'] == "")   ){
        $result .= $originUrl;
    }else{
        $result .= $info['host'];
    }
    if (isset($info['port'])) {
        $result .= ':' . $info['port'];
    }
    if (isset($info['path'])) {
        $result .=  $info['path'];
    }
    return $result;
}
if(!function_exists('mb_detect_encoding')) {
    function mb_detect_encoding($string, $enc = null, $ret = true)
    {
        $out = $enc;
        static $list = array('utf-8', 'iso-8859-1', 'iso-8859-15', 'windows-1251');
        foreach ($list as $item) {
            $sample = iconv($item, $item, $string);
            if (md5($sample) == md5($string)) {
                $out = ($ret !== false) ? true : $item;
            }
        }
        return $out;
    }
}

function stripInvalidXml($value)
{
    $ret = "";
    $current = null;
    if (empty($value))
    {
        return $ret;
    }

    $length = strlen($value);
    for ($i=0; $i < $length; $i++)
    {
        $current = ord($value{$i});
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x20) && ($current <= 0xD7FF)) ||
            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) && ($current <= 0x10FFFF)))
        {
            $ret .= chr($current);
        }
        else
        {
            $ret .= " ";
        }
    }
    return $ret;
}
function crawl_page($url, $depth = 1)
{
    static $seen = array();
    if (isset($seen[$url]) || $depth === 0) {
        return;
    }
    $base_url = parse_url($url)["host"];
    $seen[$url] = true;
    $html =@file_get_contents($url);
    $encoding =mb_detect_encoding($html);
    $dom = new DOMDocument('1.0',$encoding);
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $encoding));
    $anchors = $dom->getElementsByTagName('a');
    $link = $dom->getElementsByTagName('link');
    $script = $dom->getElementsByTagName('script');
    $img = $dom->getElementsByTagName('img');
    $meta = $dom->getElementsByTagName('meta');
    foreach ($meta as $element) {
        $charset = $element->getAttribute('charset');
        if($charset != ""){
            $element->setAttribute("charset",$encoding);
        }
    }
    foreach ($anchors as $element) {
        $href = $element->getAttribute('href');

        if (false === strpos($href, 'javascript')  ) {

            if (false === strpos($href, 'http') ) {
                $element->setAttribute('href',fixedUrl.urlencode(getUrl($href,$base_url).$href));
            }else{
                $element->setAttribute('href',fixedUrl.urlencode($href));
            }
            $element->setAttribute("target","_self");
        }
    }
//
    foreach ($img as $element) {
        $src = $element->getAttribute('src');
        if($src != "") {
            $imgurl = getFullUrl($src, $base_url);
            $convertUrl = getFullUrl($url, $base_url);
            $element->setAttribute('src', getDataURI($imgurl));
//			$size = retrieve_remote_file_size($convertUrl);
        }
        /*		if(round($size / 1024, 2) < 500) {        //100kb*/
        $data_src = $element->getAttribute('data-src');
        if($data_src != ""){
            $dataimgurl = getFullUrl($data_src,$base_url);
            $convertDataUrl = getFullUrl($dataimgurl, $base_url);
            $element->setAttribute('data-src', getDataURI($dataimgurl));
        }
        /*		}else{
                    $element->parentNode->appendChild($dom->createElement('p', $element->setAttribute('alt')));
                    $element->parentNode->removeChild($element);
                }*/
    }
    foreach ($link as $element) {
        $href = $element->getAttribute('href');
        if (!(false !== strpos($src, 'http'))) {
            $href = http_path_to_url($href,$url);
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
    foreach ($script as $element) {
        $src = $element->getAttribute('src');
     /*   if($src == "/js/common.js"){
            echo retrieve_remote_file(getFullUrl($src,$base_url));
            exit;
        }*/
   /*     if(strpos($src, './') !== false){
            echo $base_url, ":", http_path_to_url($src,$url);
            exit();
        }*/
        if (!(false !== strpos($src, 'http'))) {
            $src = http_path_to_url($src,$url);
        }

        /*$element->setAttribute('src',$src);*/

        if($element->nodeValue != ""){
            $element->removeAttribute("src" );
        }else{
            if(strpos($src,".js") !== false) {
                $element->setAttribute('src', addScriptMinifier(getFullUrl($src,$base_url)));
            }
        }


/*        if(strpos($src,".js") !== false){

            $element->parentNode->appendChild($dom->createElement('script', " \n//<![CDATA[\n".htmlentities(crawl_script_page($src),ENT_COMPAT,"UTF-8" )."\n//]]>\n" ));
            $element->parentNode->removeChild($element);
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
    /*while ($script->length > 0) {
        $p = $script->item(0);
        $p->parentNode->removeChild($p);
    }*/
    /*echo "URL:",$url,PHP_EOL,"CONTENT:",PHP_EOL,finalDataReplace($dom->saveHTML(), $base_url),PHP_EOL,PHP_EOL;*/
    // At the beginning of each page call these two functions
// Then do everything you want to do on the page
    $html = $dom->saveHTML();
    print_gzipped_page(str_replace("euc-kr","utf-8",finalDataReplace($html, $base_url)));
}


function getCssImport($html, $base_url){
    preg_match_all("/[@]\s*import.+?url\s?[(]\s?[\"|']?(.+?)[\"|']\s?[)]/", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++){
        $url = $matches[1][$i];
        $link = $matches[0][$i];
        if(strpos($url, ".css") !== false){
            $html = str_replace("$link", crawl_css_page($url, $base_url), $html);
        }
    }

    return $html;
}


function crawl_css_page($link, $base_url = ''){
    $html = @file_get_contents($link);

    if($html === false){
        return "";
    }
    preg_match_all("/background.+?url\s?[(]\s?[\"|\']?(.+?)[\"|\']?[)]/i", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++) {
        $replacelink = $matches[0][$i];
        $url = $matches[1][$i];
        $convertUrl = getDataURI(http_path_to_url($url, $link));
        /*$size = retrieve_remote_file_size($convertUrl);*/
        if ( (false !== strpos($url, '.png')) or (false !== strpos($url, '.jpg')) or (false !== strpos($url, '.jpeg')) or (false !== strpos($url, '.gif')) or (false !== strpos($url, './')) ) {
                $html = str_replace("$url", $convertUrl, $html);
        }
        /*            if(round($size / 1024, 2) > 10){		//100kb*/
    }
    /*$html = preg_replace("/background\s*[:].*url.+?([;]|[}]|[\n])/i","",$html);*/
    $html = preg_replace("/font[-]face.+?([;]|[}]|[\\n])/i","",$html);
    $html = getCssImport($html, $base_url);
    return $html;
}
function crawl_script_page($link){
    /*$html = strip_tags(@retrieve_remote_file($link),'<b><i><sup><sub><em><strong><u><br><div><p><span><body><html>');*/
    /*$html = @retrieve_remote_file($link);*/
    $html = @file_get_contents($link);

    if($html === false){
        return "";
    }

    $html = str_replace("</script>","",$html);
    $html = JSMin::minify($html);


    return ($html);
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
        if(strpos($url, ".css") !== false){
            $html = str_replace("$link", "<style>".crawl_css_page($url, $base_url)."</style>", $html);
        }
    }

    /*getCssImport($html);*/

    preg_match_all("/background.+?url\s?[(]\s?[\"|\']?(.+?)[\"|\']?[)]/i", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++) {
        $url = $matches[1][$i];
        $convertUrl = getDataURI(http_path_to_url($url, $base_url));
        /*$size = retrieve_remote_file_size($convertUrl);*/
        if ( (false !== strpos($url, '.png')) or (false !== strpos($url, '.jpg')) or (false !== strpos($url, '.jpeg')) or (false !== strpos($url, '.gif')) or (false !== strpos($url, './')) ) {
                $html = str_replace("$url", $convertUrl, $html);
        }
    }

    /*preg_match_all("/[<]\s*script.+?src\s?[=]\s?[\"|']?(.+?)[\"|'].*?[>].*?[<][\/]script[>]/", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++){
        $url = $matches[1][$i];
        $link = $matches[0][$i];
        if(strpos($url, ".js") !== false){
            $html = str_replace("$link", " <script> \n//<![CDATA[\n".crawl_script_page($url)."\n//]]>\n</script> ", $html);
        }
    }

    $html = preg_replace("/[<]\s?script.+?src\s?[=]\s?[\"|']?.+?[\"|'].*?[>]/i"," <script  >",$html);*/
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

// font[-]face.+?([;]|[}]);
function retrieve_remote_file($url){
    $ch = curl_init();;
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    $data = curl_exec($ch);

    curl_close($ch);
    return $data;
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

function http_path_to_url($path, $base_uri)
{
    if (preg_match("@^[a-z]{1}[a-z0-9\+\-\.]+:@i", $path)) return $path;
    else if ($path=="") return $base_uri;
    $base_a	= parse_url($base_uri);

    /*var_dump($base_a);*/
    $base_a['shp']	= substr($base_uri, 0, strlen($base_uri) - strlen((isset($base_a['path']) ? $base_a['path'] : '').(isset($base_a['query']) ? '?'.$base_a['query'] : '').(isset($base_a['fragment']) ? '#'.$base_a['fragment'] : '')));
    if(!isset($base_a['scheme'])){
        $base_a['scheme'] = "http";
    }
    if (preg_match("@^//@i", $path)) {
        return $base_a['scheme'].":".$path;
    } else if (preg_match("@^\?@", $path)) {
        return $base_a['shp'].$base_a['path'].$path;
    } else if (preg_match("@^#@", $path)) {
        return preg_replace("@#$@", "", substr($base_uri, 0, strlen($base_uri)-strlen($base_a['fragment']))).$path;
    } else {
        if (preg_match("@^(/\.+)+@", $path)) {
            return $base_a['shp'].$path;
        } else {
            if ($path[0]!="/" && isset($base_a['path']) && $base_a['path']!='') {
                $base_a['file']	= str_replace('/', '', strrchr($base_a['path'], '/')); // 파일명
                if (!preg_match("@/@", $base_a['path'])) $base_a['file'] = $base_a['path']; // 파일 만으로 되어 있을 경우 위에서 "/" 검색이 안 되므로
                $base_a['dir']	= substr($base_a['path'], 0, strlen($base_a['path']) - strlen($base_a['file'])); // 디렉토리, "/" 포함
            }
// 2007-06 : query에 프로토콜이 있을 경우 parse_url가 제대로 작동하지 않으므로 임시 변환 부분 추가
            if (preg_match("@[a-z]{1}[a-z0-9\+\-\.]+:[/]{2,}@i", $path)) {
                $md5	= md5(microtime()).md5(microtime());
                $path	= str_replace("://", ":__".$md5."__/__/", $path);
                $op_a	= parse_url($path);
                $op_a['query']	= str_replace(":__".$md5."__/__/", "://", (isset($op_a['query']) ? '?'.$op_a['query'] : ''));
                $path	= str_replace(":__".$md5."__/__/", "://", $path);
            } else {
                $op_a	= parse_url($path);
            }
            if(!isset($base_a['dir'])){
                $base_a['dir'] = "";
            }
            $tp_a	= explode("/", $base_a['dir'].$op_a['path']);
            $tp_c	= count($tp_a);
            $ap_a	= array();
            for ($i=0; $i < $tp_c; $i++) {
                if ($tp_a[$i]=="..") {
                    if (count($ap_a) >= 1) $ap_a = array_slice($ap_a, 0, count($ap_a)-1);
                    if ($i==$tp_c-1) $ap_a[] = ""; // 마지막일 경우
                } else if ($tp_a[$i]==".") {
                    if ($i==$tp_c-1) $ap_a[] = ""; // 마지막일 경우
                } else {
                    $ap_a[]	= $tp_a[$i];
                }
            }
            $ap	= implode("/", $ap_a);
            if (!preg_match("@^/@", $ap)) $ap = "/".$ap;
            return $base_a['shp'] .$ap .(isset($op_a['query']) ? '?'.$op_a['query'] : '') .(isset($op_a['fragment']) ? '#'.$op_a['fragment'] : '') ;
        }
    }
}