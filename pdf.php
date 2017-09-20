<?php
require __DIR__.'/vendor/autoload.php';
require 'common.php';

use Spipu\Html2Pdf\Html2Pdf;

define("DOMPDF_UNICODE_ENABLED", true);
mb_internal_encoding('UTF-8');



$url = "https://wooridigital.saramin.co.kr/service/wooridigital/811/admin/applicant/applicant_resume_view_mht_interview2.asp?id_no_enc=e0a91282de25120aabe27859a287a085f19fb98e156ae4f6c175701a8feb7fb4";


function getHost($url){
    $convertUrl =parse_url($url);
    return $convertUrl["scheme"]."://".$convertUrl["host"];
}


$contents = get_url_contents_and_final_url($url);



$encoding =mb_detect_encoding($contents);
$dom = new DOMDocument('1.0',$encoding);
@$dom->loadHTML(mb_convert_encoding($contents, 'HTML-ENTITIES', $encoding));
$link = $dom->getElementsByTagName('link');
$script = $dom->getElementsByTagName('script');
$img = $dom->getElementsByTagName('img');
$caption = $dom->getElementsByTagName('caption');


foreach ($img as $element) {
    $src = $element->getAttribute('src');
    if($src != "") {
        $imgurl = http_path_to_url($src, $url);
        $element->setAttribute('src', ($imgurl));
    }

    $data_src = $element->getAttribute('data-src');
    if($data_src != ""){
        $dataimgurl = http_path_to_url($data_src,$url);
        $element->setAttribute('data-src', ($dataimgurl));
    }

}



foreach ($link as $element) {
    $href = $element->getAttribute('href');
    if (!(false !== strpos($href, 'http'))) {

        $href = http_path_to_url($href,$url);
    }
    $element->setattribute('href',$href);

}
$cont = finalDataReplace($dom->saveHTML(),$url);
function strip_tag_arrays($str, $strip_tags) {

    foreach ($strip_tags as $val) {

        $str = preg_replace("/\<".$val."[^>]*>/i", '', $str);
        $str = preg_replace("/\<\/".$val."\>/i", '', $str);
    }

    return $str;
}


$html2pdf = new Html2Pdf('P', 'A4', 'en',true, 'UTF-8');
/*$html2pdf->setModeDebug();*/
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->setTestTdInOnePage(false);
/*$html2pdf->addFont('verdana', '', ''.$root_directory.'/fonts/source-sans-pro.light.php');*/
/*$html2pdf->setDefaultFont('Arial');*/
$cont = str_replace("'나눔고딕','NanumGothicWeb','Nanum Gothic','돋움'","",$cont );
$cont = str_replace("맑은고딕","",$cont );
$cont = str_replace("돋움","",$cont );


$cont = strip_tag_arrays($cont,["caption","colgroup"]);


@$html2pdf->writeHTML($cont);
$html2pdf->output();

// https://trafficlow:8087/testpdf.php
function get_url_contents_and_final_url(&$url)
{
    $host = getHost($url);

    $context = stream_context_create(
        array(
            "http" => array(
                "follow_location" => false,
                "method" => "GET",
                "header" => "User-Agent: MS DOS 6.0 Firefox Browser\r\nReferer: {$host}/\r\n"
            ),
        )
    );

    $result = @file_get_contents($url, false, $context);

    $pattern = "/^Location:\s*(.*)$/i";
    $location_headers = preg_grep($pattern, $http_response_header);

    if (!empty($location_headers) &&
        preg_match($pattern, array_values($location_headers)[0], $matches))
    {
        $url = $matches[1];

    }


    return $result;
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
        if(!isset($base_a['fragment'])){
            $base_a['fragment'] = "";
        }
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


function finalDataReplace($html, $base_url = ''){
//Get the urls out of the page
    preg_match_all("/[<]\s*link.+?href\s?[=]\s?[\"|']?(.+?)[\"|'].*?[>]/", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++){
        $url = $matches[1][$i];
        $link = $matches[0][$i];
        $convertUrl = http_path_to_url($url, $base_url);
        if(strpos($url, ".css") !== false){
            $html = str_replace("$link", "<style>".crawl_css_page($convertUrl, $base_url)."</style>", $html);
        }
    }
    /*getCssImport($html);*/
    preg_match_all("/background.+?url\s?[(]\s?[\"|\']?(.+?)[\"|\']?[)]/i", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++) {
        $url = $matches[1][$i];
        $convertUrl = (http_path_to_url($url, $base_url));
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


function getCssImport($html, $base_url){
    preg_match_all("/[@]\s*import.+?url\s?[(]\s?[\"|']?(.+?)[\"|']\s?[)]/", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++){
        $url = $matches[1][$i];
        $link = $matches[0][$i];
        if(strpos($url, ".css") !== false){
            $html = str_replace("$link", crawl_css_page(http_path_to_url($url, $base_url)), $html);
        }
    }
    return $html;
}
function crawl_css_page($link, $base_url = ''){
    $html = @get_url_contents_and_final_url($link);
    if($html === false){
        return "";
    }
    preg_match_all("/background.+?url\s?[(]\s?[\"|\']?(.+?)[\"|\']?[)]/i", $html, $matches);
    for($i =0; count($matches[1]) > $i; $i++) {
        $replacelink = $matches[0][$i];
        $url = $matches[1][$i];
        $convertUrl = (http_path_to_url($url, $link));
        /*$size = retrieve_remote_file_size($convertUrl);*/
        if ( (false !== strpos($url, '.png')) or (false !== strpos($url, '.jpg')) or (false !== strpos($url, '.jpeg')) or (false !== strpos($url, '.gif')) or (false !== strpos($url, './')) ) {
            $html = str_replace("$url", $convertUrl, $html);
        }
        /*            if(round($size / 1024, 2) > 10){		//100kb*/
    }
    /*$html = preg_replace("/background\s*[:].*url.+?([;]|[}]|[\n])/i","",$html);*/
    $html = preg_replace("/font[-]face.+?([;]|[}]|[\\n])/i","",$html);
    $html = getCssImport($html, $link);
    return $html;
}

