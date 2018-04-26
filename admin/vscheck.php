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
// | Copyright: (c) 2004-2015 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

session_start();

if(ini_get('max_execution_time') > 60)
{
	@set_time_limit(60);
}

header("Expires: Mon, 1 Jan 1999 01:01:01 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


define('VS_UNCHECKED_IMG', "vs_unchecked");
define('VS_OK_IMG', "vs_ok");
define('VS_BROKEN_IMG', "vs_broken");
define('VS_RESTRICTED_IMG', "vs_restricted");
define('VS_NOTAVAILABLE_IMG', "vs_na");

define('SLEEP', 1);
define('TIME_DIFF', 60);

function response($video_id, $status = 0, $message = "")
{
	$status_img = VS_UNCHECKED_IMG;
	
	switch($status)
	{
		case VS_UNCHECKED: 	$status_img = VS_UNCHECKED_IMG;  break;
		case VS_OK: 		$status_img = VS_OK_IMG; 		 break;
		case VS_BROKEN: 	$status_img = VS_BROKEN_IMG; 	 break;
		case VS_RESTRICTED: $status_img = VS_RESTRICTED_IMG; break;
	}	
	
	$attr_title = '';
	switch ($status)
	{
		case VS_UNCHECKED:
			$attr_title = 'Video Status: Unchecked';
		break;
		
		case VS_OK:
			$attr_title = 'Video Status: OK';
		break;
		
		case VS_BROKEN:
			$attr_title = 'Video Missing';
		break;
		
		case VS_RESTRICTED:	
			$attr_title = 'Video Status: Geo-restricted';
		break;
	}
	$attr_title .= '<br /> Last checked: just now';
	
	return json_encode(array('video_id' => $video_id, 
							 'status_img' => $status_img, 
							 'message' => $message,
							 'attr_title' => $attr_title
						)
			);
}

require_once('../config.php');
include_once( ABSPATH . _ADMIN_FOLDER .'/functions.php');
include_once( ABSPATH . _ADMIN_FOLDER .'/functions-vscheck.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

if ( ! is_user_logged_in() || ( ! is_admin() && ! is_moderator()) || (is_moderator() && mod_cannot('manage_videos')))
{
	//log_error("Unauthorized access attempt", "Video Status Checker", 1);
	exit(json_encode(array('message' => 'You must be logged as an Administrator')));
}

$job_type = 0;
$message = '';
$video_sources = a_fetch_video_sources();

if ( ($_GET['job_type'] != '') || ($_POST['job_type'] != '') )
{
	$job_type = (int) ($_GET['job_type'] != '') ? $_GET['job_type'] : $_POST['job_type'];
}

switch($job_type)
{
	case 1:

		$video_id = (int) trim($_POST['vid_id']);

		$video = array();
		
		if ($video_id != 0 && ($video = vscheck_get_video_details($video_id)))
		{
			// this is more for preventing accidental double-checking, which are kind of easy to do since with click-tr-select
			if (($video['last_check'] > 0 && ($time_now - $video['last_check']) > TIME_DIFF) || $video['last_check'] == 0)
			{
				$vscheck = vscheck_get_video_status(array('video-data' => $video));
			
				vscheck_update_video_status($video_id, $vscheck['status']);
				
				$message = response($video_id, $vscheck['status'], $vscheck['display_message']);
				
				sleep(SLEEP);
			}
			else
			{
				$message = response($video_id, $video['status']);
			}
		}
		else
		{
			$message = response($video_id, 0, 'Error: Cannot retrieve video details.');
		}
		
		echo $message;
		
	break;
}

exit();
