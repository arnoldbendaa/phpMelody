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

error_reporting(0);
session_start();
@header('Content-Type: text/html; charset=UTF-8;');
require('config.php');
require_once('include/functions.php');

$output 	 = '';

$queryString = trim($_POST['queryString']);
// Is there a posted query string?
if($queryString != '') 
{
	$queryString = secure_sql($queryString);
	$queryString = str_replace(array('%', ','), '', $queryString);
	
	//	only perform queries if the length of the search string is greather than 3 characters
	if(strlen($queryString) >= 3)
	{
		$num_res = 0;
		if(strlen($queryString) > 3)
		{
			$sql	 = "SELECT uniq_id, video_title, yt_id, yt_thumb, source_id, video_slug  
						FROM pm_videos 
						WHERE MATCH(video_title) 
						AGAINST ('$queryString') AS score 
						  AND added <= '". $time_now_minute ."' 
						ORDER BY score ASC 
						LIMIT 0, 10";
			$query	 = @mysql_query($sql);
			$num_res = @mysql_num_rows($query);
		}
		
		if($num_res == 0)
		{
			$sql = "SELECT video_title, uniq_id, yt_id, yt_thumb, source_id, video_slug 
					FROM pm_videos 
					WHERE added <= '". $time_now_minute ."' 
					  AND  (video_title LIKE '%$queryString%') 
					LIMIT 0, 10";
			$query = @mysql_query($sql);
		}
		
		if($query)
		{
			while($result = mysql_fetch_array($query))
			{
				$output .= '<li onClick="fill(\''.$result['video_title'].'\');" data-video-id="' . $result['uniq_id'] . '">';
				$output .= '<a href="'. makevideolink($result['uniq_id'], $result['video_title'], $result['video_slug']) .'">';
				
				if (_THUMB_FROM == 2)	//	Localhost
				{
					$output .= '<img src="'. show_thumb($result['uniq_id'], 1, $result) .'" width="45" align="absmiddle" class="pm-sl-thumb opac7" alt="'. htmlentities($result['video_title']).'" />';
				}
				$output .= $result['video_title'] .'</a>';
				$output .= '</li>';
			}
		} 
		else 
		{
			$output = $lang['search_results_msg3'];
		}
	}
}
echo $output;
exit();
