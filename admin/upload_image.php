<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

// Session Cookie workaround
if (isset($_POST["PHPSESSID"])) {
	session_id($_POST["PHPSESSID"]);
} else if (isset($_GET["PHPSESSID"])) {
	session_id($_GET["PHPSESSID"]);
}
session_start();

header('Content-Type: text/plain; charset=utf-8');

require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

$whitelist	  = array('.jpg', '.gif', '.png', '.jpeg');
$allowed_type = array('application/octet-stream');
$upload_errors = array(
        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive",
        3 => "The uploaded file was only partially uploaded",
        4 => "No file was uploaded",
        6 => "Missing a temporary folder"
	);

if ($logged_in && (is_admin() || is_moderator() || is_editor()))
{
	if ('' != $_POST['Filename'] && is_array($_FILES['Filedata']))
	{
		require_once('img.resize.php');

		$file = $_FILES['Filedata'];
		
		$tmp_parts = explode('.', $file['name']);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
		$ext = '.'. $ext;
		
		if (in_array($ext, $whitelist) && in_array($file['type'], $allowed_type))
		{
			if ($file['error'] == 0)
			{
				if ($file['size'] > 0)
				{
					if ($_POST['doing'] != 'logo' && $_POST['doing'] != 'video-thumb' && $_POST['doing'] != 'category-image')
					{
						$new_name = substr(md5($file['name'] . time()), 1, 8) . $ext;
						$uploadFile = _ARTICLE_ATTACH_DIR_PATH . $new_name;
						
						$move = @move_uploaded_file($file['tmp_name'], $uploadFile);
						if ($move !== false)
						{
							$thumb_name = str_replace($ext, '_th'.$ext, $uploadFile);
							
							$resize = resize_then_crop($uploadFile, $thumb_name, THUMB_W_ARTICLE, THUMB_H_ARTICLE, "255", "255", "255");
							
							$img = getimagesize($uploadFile); // 0 = width, 1 = height, 2 = tyoe, 3 = attr
							
							$width = $img[0];
							$height = $img[1];
							//$use_lightbox = false;
							$use_lightbox = true;
							$html = '';
							
							if ($img[0] > 500)
							{
								$width = 500;
								$ratio = (500 * 100) / $img[0];
								$height = round(($img[1] * $ratio) / 100);
								
								$use_lightbox = true;
							}
							
							$file_url = make_url_relative(_ARTICLE_ATTACH_DIR . $new_name);
							$html = '<img src="'. $file_url .'" width="'. $width .'" height="'. $height .'"';
							$html .= ' vspace="" hspace="" border="0" alt="" />';
							
							if ($use_lightbox)
							{
								$html = '<a href="'. $file_url .'" rel="prettyPhoto[phpmelody]">'. $html .'</a>';
							}
							
							echo $html;
							
							exit();
						}
						else
						{
							$error = 'The uploaded file could not be moved.';
						}
					}
					
					if ($_POST['doing'] == 'logo')
					{
						$new_name = 'custom-logo' . $ext;
						
						if (is_writeable( ABSPATH . _UPFOLDER ))
						{
							$uploadFile = ABSPATH . _UPFOLDER .'/'. $new_name;
							$file_url = _URL .'/'. _UPFOLDER .'/'. $new_name;
						}
						else
						{
							$uploadFile = _THUMBS_DIR_PATH . $new_name;
							$file_url = _THUMBS_DIR . $new_name;
						}
						
						$move = @move_uploaded_file($file['tmp_name'], $uploadFile);
						if ($move !== false)
						{
							$img_metadata = getimagesize($uploadFile); // 0 = width, 1 = height, 2 = tyoe, 3 = attr
							
							$width = $img_metadata[0];
							$height = $img_metadata[1];
							$html = '';
							
							if ($img_metadata[0] > 500)
							{
								$width = 500;
								$ratio = (500 * 100) / $img_metadata[0];
								$height = round(($img_metadata[1] * $ratio) / 100);
							}
							
							$file_url = make_url_relative($file_url);
							$html = '<img src="'. $file_url .'?cachebuster='. time() .'" width="'. $width .'" height="'. $height .'"';
							$html .= ' vspace="" hspace="" border="0" alt="" />';
							$html .= '<input type="hidden" name="custom_logo_url" value="'. $file_url .'" />';
							echo $html;

							exit();
						}
						else
						{
							$error = 'The uploaded file could not be moved.';
						}
					}

					if ($_POST['doing'] == 'video-thumb')
					{
						$img = new resize_img();
						$img->sizelimit_x = THUMB_W_VIDEO;
						$img->sizelimit_y = THUMB_H_VIDEO;
						$img->keep_proportions = true;
						$img->output = 'JPG';
						
						$uniq_id = $_POST['uniq_id'];
						
						if(empty($uniq_id)) 
						{
							$uniq_id = substr(md5($_POST['uniq_id'] . time()), 1, 8);
						}
						//$new_name = substr(md5($_POST['uniq_id'] . time()), 1, 8)."-1";
						$new_name = $uniq_id . '-1';
						$file_url = _THUMBS_DIR . $new_name . '.jpg';
						
						//	resize image and save it
						if ($img->resize_image($file['tmp_name']) === false)
						{
							$html = pm_alert_error($img->error, array('id' => '_error_'));
						}
						else
						{
							$img->save_resizedimage(_THUMBS_DIR_PATH, $new_name);
							$html = '<img id="show-thumb" class="show-thumb-temp" src="'. $file_url .'?cachebuster='. time() .'" width="" height=""';
							$html .= ' vspace="" hspace="" border="0" alt="" />';
							$html .= '<input type="hidden" name="yt_thumb_local" value="'. $file_url .'" />';

							generate_social_thumb(_THUMBS_DIR_PATH . $new_name .'.jpg');
						}
						
						exit($html);
					}
					
					if ($_POST['doing'] == 'category-image')
					{
						$img = new resize_img();
						$img->sizelimit_x = 320; 
						$img->sizelimit_y = 180;
						$img->keep_proportions = true;
						$img->output = 'JPG';
						
						$category_id = (int) $_POST['cat_id'];
						
						$random_str = substr(md5($category_id . time()), 0, 11);
						$file_url = _THUMBS_DIR . $random_str . '.jpg';
						
						//	resize image and save it
						if ($img->resize_image($file['tmp_name']) === false)
						{
							$html = pm_alert_error($img->error, array('id' => '_error_'));
						}
						else
						{
							$img->save_resizedimage(_THUMBS_DIR_PATH, $random_str);
							$html = '<img id="show-cat-cover" class="show-thumb-temp" src="'. $file_url .'?cachebuster='. time() .'" width="" height=""';
							$html .= ' vspace="" hspace="" border="0" alt="" />';
							$html .= '<input type="hidden" name="image" value="'. $random_str .'.jpg" />';
						}
						
						exit($html);
					}
				}
				else
				{
					$error = 'File is empty. This error could also be caused by uploads being disabled in your php.ini.';
				}
			}
			else
			{
				$error = $upload_errors[$file['error']];
			}
		}
		else
		{
			$error = 'Invalid file type.';
		}
	}
	else
	{
		$error = 'Select a file first.';
	}
}
else if ( ! $logged_in)
{
	header('Location: login.php');
	exit();
}
else
{
	$error = 'Access denied';
}

if (strlen($error) > 0)
{
	echo pm_alert_error($error, array('id' => '_error_'));
}
exit();