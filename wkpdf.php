<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-01
 * Time: 오후 8:06
 */
require "common.php";
require __DIR__ . '/vendor/autoload.php';

use mikehaertl\wkhtmlto\Pdf;
use mikehaertl\wkhtmlto\Image;

$url = "https://wooridigital.saramin.co.kr/service/wooridigital/811/admin/applicant/applicant_resume_view_mht_interview2.asp?id_no_enc=e0a91282de25120aabe27859a287a085f19fb98e156ae4f6c175701a8feb7fb4";
// You can pass a filename, a HTML string, an URL or an options array to the constructor


$image = new Image($url);
$image->saveAs('page.png');

// ... or send to client for inline display
$image->send();

// ... or send to client as file download
$image->send('page.png');