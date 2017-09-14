<?php
//                      Resize image.
//             Writeen By: Smelban & Haker4o
// Mails smelban@smwebdesigns.com & Haker4o@Haker4o.org
// This code is written to only execute on  jpg,gif,png
// $picname = resizepics('pics', 'new widthmax', 'new heightmax');
// Demo  $picname = resizepics('stihche.jpg', '180', '140');

$quality = $_REQUEST["quality"];
$url = urldecode($_REQUEST["url"]);
$sizex = $_REQUEST["sizex"];
$sizey = $_REQUEST["sizey"];

$pickname = resizepics($url, $sizex, $sizey, $quality);
echo $pickname;
//Error
die( "<font color=\"#FF0066\"><center><b>File not exists :(<b></center></FONT>");
//Funcion resizepics
function resizepics($pics, $newwidth, $newheight, $quality = 100){

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

	if (empty($newwidth) or empty($newheight)){
		return "";
	}

    if(preg_match("/.png/i", "$pics")){
        header('Content-type: image/png');
        $source = imagecreatefrompng($pics);
    }else if(preg_match("/.gif/i", "$pics")){
        header('Content-type: image/gif');
        $source = imagecreatefromgif($pics);
    }else {
        header('Content-type: image/jpeg');
        $source = imagecreatefromjpeg($pics);
	}

    $thumb = imagecreatetruecolor($newwidth, $newheight);

    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


    if(preg_match("/.png/i", "$pics")){
        $red = imagecolorallocate($thumb, 255, 0, 0);
        $black = imagecolorallocate($thumb, 0, 0, 0);
        imagecolortransparent($thumb, $black);
        imagefilledrectangle($thumb, 4, 4, 50, 25, $red);
        $quality -= 100;
        $quality = round(abs($quality));

        $img = imagepng($thumb, null, $quality, PNG_ALL_FILTERS);


    }else if(preg_match("/.gif/i", "$pics")){
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefill($thumb, 0, 0, $transparent);
        imagealphablending($thumb, true);

        $img = imagegif($thumb);

    }else{
        $img = imagejpeg($thumb, null, $quality);
	}

    imagedestroy($thumb);

    return $img;
}
?>