<?php

// error_reporting(E_ALL); ini_set('display_errors', 1);
require_once dirname(__FILE__) . '/securimage/securimage.php';

$options = array(
    'use_database' => false,
    'captcha_type' => Securimage::SI_CAPTCHA_STRING
//	'captcha_type' => Securimage::SI_CAPTCHA_MATHEMATIC
);

$img = new Securimage($options);
$img->ttf_file        = './securimage/fonts/Roboto-Medium.ttf';
$img->image_height    = 35;                                // height in pixels of the image
$img->image_width     = 158;
$img->image_bg_color  = new Securimage_Color("#FFFFFF");   // image background color
$img->text_color      = new Securimage_Color("#333333");   // captcha text color
$img->case_sensitive  = false;
$img->line_color      = new Securimage_Color("#707070");   // color of lines over the image
$img->num_lines       = 5;                                 // how many lines to draw over the image
$img->font_size       = 18;
$img->font_ratio      = 0.65;
$img->code_length     = 6;
$img->noise_level     = 3;
$img->charset         = 'ABCDEFGHKLMNPRSTUVWYZabcdefghkmnprstuvwyz23456789';

// set namespace if supplied to script via HTTP GET
if (!empty($_GET['namespace']))
{
    $img->setNamespace($_GET['namespace']);
}

$img->show();
// alternate use:
// $img->show('/path/to/background_image.jpg');
