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
require_once('config.php');
require_once('include/functions.php');

$time_now = time();
$impression_counter_delay = 5; // seconds

$hash = $_GET['h'];
$action = $_GET['tz'];
if($action == '')
	$action = 'video';

if($hash == '' || (strlen($hash) != 12) || !preg_match("/^[a-z0-9]+$/", $hash))
{
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate'); 
	header('Content-type: text/html');
	header('HTTP/1.0 404 Not Found');
	exit();
}

switch($action)
{
	default: 
	case 'video':
		//	video ad request
		$sql = "SELECT * FROM pm_videoads WHERE hash='".secure_sql($hash)."'";
		$result = @mysql_query($sql);
		if(!$result)
		{
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Content-type: text/html');
			exit();
		}
		$ad = @mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ($_SESSION['PVA_view_time'] > 0)
		{
			if (($time_now - $_SESSION['PVA_view_time']) >= $impression_counter_delay)
			{
				if ($ad['disable_stats'] == 0)
				{
					$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, impressions)
							VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
							ON DUPLICATE KEY 
								UPDATE impressions = impressions + 1";
					@mysql_query($sql); 
				}
				
				//	register session
				$_SESSION['PVA_view_time'] = $time_now;
			}
		}
		else
		{
			if ($ad['disable_stats'] == 0)
			{
				//	update impressions
				$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, impressions)
						VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
						ON DUPLICATE KEY 
							UPDATE impressions = impressions + 1";
				@mysql_query($sql);
			}
			
			//	register session
			$_SESSION['PVA_view_time'] = $time_now;		
		}
		//	send headers
		header('Pragma: no-cache');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		//header("Content-Type: video/x-flv"); @Since v2.5 out of the box, Video JS will detect mp4 and FLV on its own (line not required) 
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Location: '.$ad['flv_url']);
	break;
	case "t":
		//	redirect to advertiser's page
		$sql = "SELECT * FROM pm_videoads WHERE hash='".secure_sql($hash)."'";
		$result = @mysql_query($sql);
		if(!$result)
		{
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Content-type: text/html');
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header('HTTP/1.0 404 Not Found');
			exit();
		}
		$ad = @mysql_fetch_assoc($result);
		mysql_free_result($result);

		if(isset($_SESSION['PVA_click_time']) && (is_numeric($_SESSION['PVA_click_time'])) && (strlen($_SESSION['PVA_click_time']) == 10))
		{
			if( $time_now - $_SESSION['PVA_click_time'] >= $impression_counter_delay)
			{
				//	update clicks
				if ($ad['disable_stats'] == 0)
				{
					$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, clicks)
							VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
							ON DUPLICATE KEY 
								UPDATE clicks = clicks + 1";
					@mysql_query($sql); 
				}
				
				//	register session
				$_SESSION['PVA_click_time'] = $time_now;
			}
		}
		else
		{
			if ($ad['disable_stats'] == 0)
			{
				$sql = "INSERT INTO pm_ads_log (date, ad_id, ad_type, clicks)
						VALUES (CURDATE(), ". $ad['id'] .", ". _AD_TYPE_VIDEO .", 1) 
						ON DUPLICATE KEY 
							UPDATE clicks = clicks + 1";
				@mysql_query($sql); 
			}
			
			//	register session
			$_SESSION['PVA_click_time'] = $time_now;
		}
		
		//	redirect user
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Location: '.$ad['redirect_url']);
	break;
}
exit();
?>