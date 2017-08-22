<?php
    /*require "common.php";*/
    require "jsmin.php";

    ini_set("display_errors", 1);
    ini_set('allow_url_fopen',1);
    ini_set('allow_url_include', 'on');
    ini_set('default_charset', 'utf-8');
    /*ini_set('mbstring.internal_encoding','UTF-8');*/
    ini_set('mbstring.func_overload',7);
    Header("content-type: application/x-javascript");
    ini_set('max_execution_time', 600);



    echo (JSMin::minify(@file_get_contents($_REQUEST["url"]."?".explode("?", $_SERVER['REQUEST_URI'])[1])));