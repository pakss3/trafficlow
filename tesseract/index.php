<?php
    require '../common.php';
    require_once '../vendor/autoload.php';
    /*require './src/TesseractOCR.php';*/
    /*require './tests/UnitTests.php';*/

$ocr =new TesseractOCR((__DIR__.'\images\admin.png'));
/*$ocr->tessdataDir('"D:\\ProgramFiles\\Tesseract-OCR\\tessdata\\"');*/


/*echo (new TesseractOCR((__DIR__.'\images\admin.bmp')))*/
echo $ocr
    ->executable('"D:\ProgramFiles\Tesseract-OCR\tesseract.exe"')
    ->lang('kor','eng')
    ->run();
    /*->buildCommand();*/
;
//D:\dev\Project\trafficlow\tesseract\images\text.jpeg
//D:\dev\Project\trafficlow\tesseract\images\text.jpg

/*
 set TESSDATA_PREFIX="D:\Program Files (x86)\Tesseract-OCR\tessdata"

 */