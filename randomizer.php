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

function get_random_video( $category_id = '')
{
	global $config, $time_now_minute;
	
	if ($category_id != '')
	{
		$sql = "SELECT published_videos 
				FROM pm_categories 
				WHERE id = '". $category_id ."'";
		
		if ( ! $result = mysql_query($sql))
		{
			return false;
		}
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		$total_rows = (int) $row['published_videos'];
		
		if ($total_rows == 0)
		{
			$category_id = '';
			$total_rows = $config['published_videos'];
		}
	}
	else
	{
		if ($config['published_videos'] == 0)
		{
			$sql = "SELECT COUNT(*) as total 
					FROM pm_videos 
					WHERE added <= '". $time_now_minute ."'";
			if ( ! $result = mysql_query($sql))
			{
				return false;
			}
			$row = mysql_fetch_assoc($result);
			mysql_free_result($result);
			
			if ($row['total'] == 0)
			{
				return false;
			}
			update_config('published_videos', $row['total']);
		}
		$total_rows = $config['published_videos'];
	}
		
	$found = false;
	$uniq_id = '';

	while ( ! $found)
	{

		$rand_from = abs(rand(0, $total_rows - 5));

		$sql = "SELECT uniq_id 
				FROM pm_videos 
				WHERE added <= '". $time_now_minute ."'";
		if ($category_id != '')
		{
			$sql .= " AND (category LIKE '$category_id' 
					 	  OR category LIKE '$category_id,%' 
					 	  OR category LIKE '%,$category_id' 
					 	  OR category LIKE '%,$category_id,%')";
		}
		$sql .= "LIMIT $rand_from, 5";

		if ( ! $result = mysql_query($sql))
		{
			return false;
		}
	
		while ($row = mysql_fetch_assoc($result))
		{
			if ($row['uniq_id'] != '')
			{
				$uniq_id = $row['uniq_id'];
				$found = true;
				break;
			}
		}
		mysql_free_result($result);
	}
	
	return $uniq_id;
}

$uniq_id = get_random_video();

if($uniq_id === false)
	$location = _URL."/index."._FEXT;
else
	$location = makevideolink($uniq_id);

@header("Location: ". $location);
exit();
?>