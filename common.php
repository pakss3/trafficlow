<?php
	/*require 'vendor/autoload.php';*/
    /*error_reporting(E_ALL);

    ini_set("display_errors", 1);
    ini_set('allow_url_fopen',1);
    ini_set('allow_url_include', 'on');
    ini_set('default_charset', 'utf-8');
    /*ini_set('mbstring.internal_encoding','UTF-8');*/
    ini_set('mbstring.func_overload',7);
    header('Content-Type: text/html; charset=UTF-8');
    ini_set('max_execution_time', 600);

	const fixedUrl = "https://trafficlow:8087/resizeframe.php?url=";
    const imageResizeUrl = "https://trafficlow:8087/resize.php?url=";
    const jsMiniUrl = "https://trafficlow:8087/jsminifier.php?url=";

    use Minifier as CSSmin;

// Include this function on your pages
function print_gzipped_page($content) {
    $encoding = gzipStart();
    $encoding = true;
    $contents = $content;

    if( $encoding ){
        echo $contents;

        $gzip_size        = ob_get_length();
        $gzip_contents    = ob_get_clean(); // PHP < 4.3 use ob_get_contents() + ob_end_clean()
        header('Content-Encoding: gzip');
        header('Vary: Accept-Encoding');
        header("cache-control: must-revalidate");
        header( 'Content-Length: ' . $gzip_size );
        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00",
        substr(gzcompress($gzip_contents, 9), 0, - 4),
        pack('V', crc32($gzip_contents)),    // crc32 and
        pack('V', $gzip_size);
        exit();
    }else{
        if(!ob_start("ob_gzhandler")){
            ob_start();
        }
        echo ($contents.PHP_EOL.PHP_EOL);
        ob_end_flush();
        exit();
    }
}
function gzipStart(){
    $phpver = phpversion();
    $useragent = $_SERVER["HTTP_USER_AGENT"];
    $do_gzip_compress = false;
    if ( $phpver >= '4.0.4pl1' && ( strstr($useragent,'compatible') || strstr($useragent,'Gecko') ) )
    {
        if ( extension_loaded('zlib') )
        {
            ob_start('ob_gzhandler');
            $do_gzip_compress = TRUE;
        }
    }
    else if ( $phpver > '4.0' )
    {
        if ( strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') )
        {
            if ( extension_loaded('zlib') )
            {
                $do_gzip_compress = TRUE;
                ob_start();
                ob_implicit_flush(0);
                header('Content-Encoding: gzip');
            }
        }
    }
    return $do_gzip_compress;
}

?>