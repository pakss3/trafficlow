<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-14
 * Time: 오후 2:57
 */

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


    if((!isset($parts['host']))  or ($parts['host'] == null)){
        $href .= $originUrl;
    }else{
        $href .= $parts['host'];
    }


    if (isset($parts['port'])) {
        $href .= ':' . $parts['port'];
    }

    if (isset($parts['path'])) {
        $href .=  $parts['path'];
    }
    /*	if(strpos($url,"bg_slidebar.png") !== false){
            var_dump($url,$href);

        }*/
    return $href;
};


$url = "/v2014/images/common/bg_slidebar.png";
$requesturl = $_REQUEST["url"];
$baseurl = "www.eluocnc.com";


echo parse_url($requesturl)["host"];

echo getFullUrl(getFullUrl($url, $baseurl),$baseurl);