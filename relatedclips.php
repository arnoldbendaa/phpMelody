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

session_start();
require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');
require_once('include/rating_functions.php');

function clean_feed($input) 
{
	$original = array("<", ">", "&", '"', "'", "<br/>", "<br>");
	$replaced = array("&lt;", "&gt;", "&amp;", "&quot;","&apos;", "", "");
	$newinput = str_replace($original, $replaced, $input);
	
	return $newinput;
}

function show_more_xml($what_kind = 'related', $cid = '1', $limit = 5) 
{
	global $lang, $config, $video, $time_now_minute;
	
	$categories = load_categories();
	
	$video_title = $video['video_title'];
	$cid	= secure_sql( trim($cid) );
	$query	= '';
	$result	= '';
	$sql	= '';
	$related 	= array(); 
	
	if($what_kind != 'related' && $what_kind != 'best_in_category')
		$what_kind = 'related';		//	default value
	
	switch($what_kind)
	{
		case 'related':
			
			$total_videos = (int) $categories[$cid]['published_videos'];
			
			$rand_from = abs(rand(0, $total_rows - $limit));
			
 			$sql = "SELECT * 
					FROM pm_videos 
					WHERE  
					MATCH (video_title) AGAINST ('". addslashes($video_title) ."')
					AND id != '". $video['id'] ."'
					AND added <= '". $time_now_minute ."'
					LIMIT 0, $limit";
			
			$query = @mysql_query($sql);
			$total_results = @mysql_num_rows($query);
	
			if ($total_results == 0)
			{
				$sql = "SELECT * 
						FROM pm_videos 
						WHERE (category LIKE '%,$cid,%' 
							 OR category like '%,$cid' 
							 OR category like '$cid,%' 
							 OR category='$cid') 
						  AND id != '". $video['id'] ."'
						  AND added <= '". $time_now_minute ."' 
						LIMIT $rand_from, $limit";
				$query = @mysql_query($sql);
				$total_results = @mysql_num_rows($query);
			}
			
			while($row = mysql_fetch_array($query))
			{
				$related[] = $row;
			}
			mysql_free_result($query);
			
			// fill it to the brim
			if ($total_results < $limit && $total_results > 0)
			{
				$limit_left = $limit - $total_results;
				$sql = "SELECT *  
						FROM pm_videos 
						WHERE (category LIKE '%,$cid,%' 
							 OR category like '%,$cid' 
							 OR category like '$cid,%' 
							 OR category='$cid') 
						  AND id != '". $video['id'] ."' 
						  AND added <= '". $time_now_minute ."'
						LIMIT $rand_from, $limit_left";

				$result = mysql_query($sql);
				while ($row = mysql_fetch_assoc($result))
				{
					$related[] = $row;
				}
				mysql_free_result($result);
			}
			
		break;
		
		case 'best_in_category':
			if(strstr($cid, ",")) 
			{
				$temp = explode(",", $cid);
				$cid = trim($temp[0]);
			}
			$sql = "SELECT pm_videos.*, COALESCE(pm_bin_rating_meta.score, 0) as score 
					FROM pm_videos 
					LEFT JOIN pm_bin_rating_meta ON (pm_videos.uniq_id = pm_bin_rating_meta.uniq_id) 
					WHERE added <= '". $time_now_minute ."' 
					  AND (category LIKE '%,$cid,%' 
						   OR category like '%,$cid' 
						   OR category like '$cid,%' 
						   OR category='$cid') 
					  AND id != '". $video['id'] ."'
					ORDER BY score DESC
					LIMIT $set_limit, $limit" ;
			$query = @mysql_query($sql);
			$total_results = @mysql_num_rows($query);
			
			if ($total_results == 0)
			{
				$sql = "SELECT * 
						FROM pm_videos 
						WHERE category = '".$cid."'
						  AND added <= '". $time_now_minute ."' 
						  AND id != '". $video['id'] ."'
						ORDER BY site_views DESC  
						LIMIT ".$limit;
				$query = @mysql_query($sql);
				$total_results = @mysql_num_rows($query);
			}
			
			while($row = mysql_fetch_array($query))
			{
				$related[] = $row;
			}
			mysql_free_result($query);
			
		break;
	}

	$result = '';
	if($total_results > 0)
	{
		$n = 1;
		foreach ($related as $k => $row)
		{
			$results .= "
			<video id=\"00".$n."\">
				<title>".clean_feed($row['video_title'])."</title>
				<thumb>".show_thumb($row['uniq_id'], 1, $row)."</thumb>
				<url>".makevideolink($row['uniq_id'], $row['video_title'], $row['video_slug'])."</url>
			</video>";
			$n++;
		}
	}
	else
	{
		$results .= "
			<video id=\"001\">
				<title>".$lang['top_videos_msg1']."</title>
				<thumb>".show_thumb($row['uniq_id'], 1, $row)."</thumb>
				<url></url>
			</video>";
	}

	return $results;
}

$illegal_chars = array('>', '<', '&', "'", '"', '*', '%');

$video_id 	= $_GET['vid'];
$video_id 	= str_replace($illegal_chars, '', $video_id);
$video 		= request_video($video_id, '', true);

if ( ! is_array($video))
{
	exit();
}

$output =  show_more_xml('related', $video['category'], 10);
@header("Content-Type: text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8"?>
<videolist>
<title>Related videos:</title>';
echo $output."\n</videolist>";
exit();
?>