<?php

// Include this function on your pages
function print_gzipped_page() {

    global $HTTP_ACCEPT_ENCODING;
    if( headers_sent() ){
        $encoding = false;
    }elseif( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ){
        $encoding = 'x-gzip';
    }elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ){
        $encoding = 'gzip';
    }else{
        $encoding = false;
    }

    if( $encoding ){
        $contents = ob_get_contents();
        ob_end_clean();
        header('Content-Encoding: '.$encoding);
        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
        $size = strlen($contents);
        $contents = gzcompress($contents, 9);
        $contents = substr($contents, 0, $size);
        print($contents);
        exit();
    }else{
        ob_end_flush();
        exit();
    }
}
function hasAcceptEncoding($encoding) {
    return (stripos($_SERVER['HTTP_ACCEPT_ENCODING'], $encoding) !== false);
}

// At the beginning of each page call these two functions
ob_start("ob_gzhandler");
ob_implicit_flush(0);

// Then do everything you want to do on the page
echo "aaaaaaaaaaaaaaaaaaaaaaaaaaa  dafdasfdsafdsf  fdafdasfasdsfd  fdasfafd


fdasfdsaf
fads
fdsa
f
dsafdasfdasfdasfd
fdasfdsafdfdasㄹㅇㅁㄹㅇ머ㅏ임ㄹㅇㅁ
ㄹㅇㅁㄹㅇㅁㄴㄹㅇㄴㅁ
ㄹㅇㅁㄹㅇㅁㄴ";

// Call this function to output everything as gzipped content.
print_gzipped_page();