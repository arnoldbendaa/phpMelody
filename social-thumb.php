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


/**
 * Generate a more social media friendly thumbnail for videos.
 * Social media friendly means thumbs with a play icon in the center in this case.
 *
 * How it works:
 * - all requests are masked with .htaccess so social media sites (ex. facebook) don't refuse them (ex. "/uploads/thumbs/{video-uniq-id}-social.jpg")
 * - checks to see if the image already exists on disk
 * - if it doesn't, it generates one on the fly and saves it on disk for later usage
 * - outputs the image file with proper headers() or 404
 *
 * @since v2.3
 */

function output_image($file)
{
	global $conn_id;

	if ($conn_id)
	{
		mysql_close($conn_id);
	}

	$fp = fopen($file, 'rb');
	$filesize = filesize($file);
	
	header('Content-Type: image/jpeg');
	header('Content-length: '. $filesize);
	
	if (function_exists('fpassthru'))
	{
		fpassthru($fp);
	}
	else // some hosts disable this function; high chances similar functions are disabled too in this scenario
	{
		flock($fp, LOCK_SH);
		
		$buffer = fread($fp, $filesize);
	    echo $buffer;
		
	    flock($fp, LOCK_UN);
	    fclose($fp);		
	}
	
	
	exit();
}

$uniq_id = $_GET['vid'];

if ( ! empty($uniq_id) && strlen($uniq_id) < 10 && ctype_alnum($uniq_id))
{
	define('IGNORE_MOBILE', true);
	define('IGNORE_MAINTENANCE_MODE', true);

	require('config.php');
	require_once('include/functions.php');
	require_once('include/user_functions.php');

	$file = _THUMBS_DIR_PATH . $uniq_id .'-social.jpg';
	$source_file = _THUMBS_DIR_PATH . $uniq_id .'-1.jpg';

	//$video = request_video($uniq_id);
	if (file_exists($file))
	{
		//Source image was modified after the social thumb? Regenerate the social thumbnail!
		if (file_exists($source_file) && filemtime($file) < filemtime($source_file))
		{
			unlink($file);
			
			if (generate_social_thumb($source_file))
			{
				output_image($file);
			}
			else
			{
				output_image($source_file);
			}
		}
		else
		{
			output_image($file);
		}
		
	}

	// generate social sharing thumb
	if (file_exists($source_file))
	{
		if (generate_social_thumb($source_file))
		{
			output_image($file);
		}
		else
		{
			output_image($source_file);
		}
	}

	output_image(ABSPATH .'/templates/'. _TPLFOLDER .'/img/no-thumbnail.jpg');
}

// worst case scenario
header("HTTP/1.0 404 Not Found");
exit();