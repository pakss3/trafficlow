<?php
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
$base_url = "http://nimg.nate.com/ui/uidev/m/release/css/common.css?201708101400";
$baseText = "a.more{position:absolute;top:4px;right:15px;width:18px;height:18px;font-size:14px;color:transparent;text-indent:-9999px;letter-spacing:-1px;background:url(/images/mobile/img/common/ir_v20170404.png) -131px -93px no-repeat;background-size:320px auto}";
preg_match_all("/background.+?url\s?[(]\s?[\"|\']?(.+?)[\"|\']?[)]/i", $baseText, $matches);
for($i =0; count($matches[1]) > $i; $i++) {
    $link = $matches[0][$i];
    $url = $matches[1][$i];
    $convertUrl = http_path_to_url($url, $base_url);
    echo var_dump($link,"<br>",$url,"<br>",$convertUrl);
    /*$html = str_replace("$url", trim(getDataURI($convertUrl)), $html);*/
}




function http_path_to_url($path, $base_uri)
{
    if (preg_match("@^[a-z]{1}[a-z0-9\+\-\.]+:@i", $path)) return $path;
    else if ($path=="") return $base_uri;

    $base_a	= parse_url($base_uri);
    /*var_dump($base_a);*/
    $base_a['shp']	= substr($base_uri, 0, strlen($base_uri) - strlen($base_a['path'].(isset($base_a['query']) ? '?'.$base_a['query'] : '').(isset($base_a['fragment']) ? '#'.$base_a['fragment'] : '')));

    if (preg_match("@^//@i", $path)) {
        if(!isset($base_a['scheme'])){
            $base_a['scheme'] = "http";
        }
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
                $op_a['query']	= str_replace(":__".$md5."__/__/", "://", $op_a['query']);
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

            return $base_a['shp'] .$ap .(isset($op_a['query']) ? '?'.$op_a['query'] : '') .(isset($op_a['fragment']) ? '#'.$op_a['fragment'] : '');
        }
    }
}