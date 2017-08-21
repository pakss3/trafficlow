<?php
//                      Resize image.
//             Writeen By: Smelban & Haker4o
// Mails smelban@smwebdesigns.com & Haker4o@Haker4o.org
// This code is written to only execute on  jpg,gif,png
// $picname = resizepics('pics', 'new widthmax', 'new heightmax');
// Demo  $picname = resizepics('stihche.jpg', '180', '140');

$pickname = resizepics($_REQUEST["url"], $_REQUEST["sizex"], $_REQUEST["sizey"]);
echo $pickname;
//Error
die( "<font color=\"#FF0066\"><center><b>File not exists :(<b></center></FONT>");
//Funcion resizepics
function resizepics($pics, $newwidth, $newheight){

    list($width, $height) = getimagesize($pics);

        if($width>$newwidth || $height>$newheight) {
            // 가로길이가 가로limit값보다 크거나 세로길이가 세로limit보다 클경우
            $sumw = (100*$newheight)/$height;
            $sumh = (100*$newwidth)/$width;
            if($sumw < $sumh) {
                // 가로가 세로보다 클경우
                $img_width = ceil(($width*$sumw)/100);
                $img_height = $newheight;
            } else {
                // 세로가 가로보다 클경우
                $img_height = ceil(($height*$sumh)/100);
                $img_width = $newwidth;
            }
        } else {
            // limit보다 크지 않는 경우는 원본 사이즈 그대로.....
            $img_width = $width;
            $img_height = $height;
        }

    $newwidth = $img_width;
    $newheight = $img_height;

    if(preg_match("/.jpg/i", "$pics") || preg_match("/.jpeg/i", "$pics")){
        header('Content-type: image/jpeg');
        $source = imagecreatefromjpeg($pics);
    }
    if(preg_match("/.png/i", "$pics")){
        header('Content-type: image/png');
        $source = imagecreatefrompng($pics);
    }
    if(preg_match("/.gif/i", "$pics")){
        header('Content-type: image/gif');
        $source = imagecreatefromgif($pics);
    }
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    /*return imagejpeg($thumb);*/

    if(preg_match("/.jpeg/i", "$pics") || preg_match("/.jpg/i", "$pics")){
        return imagejpeg($thumb, null, 100);
    }
    if(preg_match("/.png/i", "$pics")){
        return imagepng($thumb, null, 1);
    }
    if(preg_match("/.gif/i", "$pics")){
        header('Content-type: image/gif');
        return imagegif($thumb);
    }
}
?>