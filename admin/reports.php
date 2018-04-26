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

$showm = '2';
$load_scrolltofixed = 1;
$_page_title = 'Reported videos';
include('header.php');

$action	 = (int) $_GET['a'];
$id		 = (int) $_GET['rid'];

$page	 = (int) $_GET['page'];

if(empty($page) || !is_numeric($page) || $page == '')
   $page = 1;
$limit	 = 20;	//	reported videos per page
$from	 = $page * $limit - ($limit);

//	Batch Delete Reports
if($_POST['Submit'] == 'Delete reports')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			$sql = "DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")";
			$result = @mysql_query($sql);

			if ( ! $result)
			{
				$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '.mysql_error());
			}
			else
			{
				$info_msg = pm_alert_success('The selected reports were removed.');
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
//	Batch Delete Videos
if (($_POST['Submit'] == 'Delete videos') && ! csrfguard_check_referer('_admin_reports_deletevideos'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
}
else if ($_POST['Submit'] == 'Delete videos')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= "'" . $video_ids[ $i ] . "', ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			$video_list_data = array();
			$sql = "SELECT id, uniq_id, category, url_flv, added, source_id FROM pm_videos WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result))
			{
				$video_list_data[$row['uniq_id']] = $row;
			}
			mysql_free_result($result);

			$sql = "DELETE FROM pm_videos WHERE uniq_id IN (" . $in_arr . ")";
			$result = mysql_query($sql);
			
			if(!$result)
			{
				$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '.mysql_error());
			}
			else
			{
				mysql_query("DELETE FROM pm_comments WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_reports WHERE entry_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_videos_urls WHERE uniq_id IN (" . $in_arr . ")");
				//mysql_query("DELETE FROM pm_favorites WHERE uniq_id IN (" . $in_arr . ")"); // @deprecated since v2.2
				mysql_query("DELETE FROM pm_chart WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_tags WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_bin_rating_meta WHERE uniq_id IN (" . $in_arr . ")");
				mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (" . $in_arr . ")");
				
				$ids = array();
				foreach ($video_list_data as $uniq_id => $video)
				{
					$ids[] = $video['id'];
					
					// handle playlists
					$playlist_ids = array();
					
					$sql = "SELECT list_id 
							FROM pm_playlist_items 
							WHERE video_id = ". $video['id'];
					
					if ($result = @mysql_query($sql))
					{
						$in_playlists = false;
						while ($row = mysql_fetch_assoc($result))
						{
							$playlist_ids[] = (int) $row['list_id'];
							$in_playlists = true;
						}
						mysql_free_result($result);
					
						if ($in_playlists)
						{
							$sql = "DELETE FROM pm_playlist_items
									WHERE video_id = ". $video['id'];
							@mysql_query($sql);
			
							$sql = "UPDATE pm_playlists 
									SET items_count = items_count - 1 
									WHERE list_id IN (". implode(',', $playlist_ids) .")";
							@mysql_query($sql);
						}
					}
				}
				
				mysql_query("DELETE FROM pm_meta WHERE item_id IN (". implode(',', $ids) .") AND item_type = ". IS_VIDEO);
				unset($ids);
		
				$info_msg = pm_alert_success('The selected videos have been removed.');
			}
			
			// update video count for each category
			$video_count = array();
			$video_published_count = array();
			$time_now = time();
			foreach ($video_list_data as $uniq_id => $row)
			{
				// delete hosted files
				if ($row['source_id'] == 1)
				{
					if (file_exists(_VIDEOS_DIR_PATH . $row['url_flv']) && strlen($row['url_flv']) > 0)
					{
						unlink(_VIDEOS_DIR_PATH . $row['url_flv']);					
					}
					if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg'))
					{
						unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-1.jpg');
					}
					if (file_exists(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg'))
					{
						unlink(_THUMBS_DIR_PATH . $row['uniq_id'] .'-social.jpg');
					}
				}
				
				$buffer = explode(',', $row['category']);
				foreach ($buffer as $k => $id)
				{
					$video_count[$id]++;
					if ($row['added'] <= $time_now)
					{
						$video_published_count[$id]++;
					}
				}
			}
			
			if (count($video_count) > 0)
			foreach ($video_count as $cid => $count)
			{
				if ('' != $cid)
				{
					$sql = "UPDATE pm_categories SET total_videos=total_videos-". $count;
					if ($video_published_count[$id] < 0)
					{
						$sql .= ", published_videos = published_videos - ". $video_published_count[$id];
					}
					$sql .= " WHERE id = '". $cid ."'";
					mysql_query($sql);
				}
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

//	DELETE REPORT
if($action == 1) { 
	
	@mysql_query("DELETE FROM pm_reports WHERE id = '".$id."'");
	$info_msg = pm_alert_success('The report has been removed.');
}
//	DELETE ALL REPORTS
if($action == 2) { 

	@mysql_query("TRUNCATE TABLE pm_reports");
	$info_msg = pm_alert_success('The reports were deleted.');
}

$total_rvideos = count_entries('pm_reports', 'r_type', '1');

if($total_rvideos - $from == 1)
	$page--;
$reported = a_list_vreports( '1', $from, $limit, $page); // 1-videos, 2-comments 

if($total_rvideos - $from == 1)
	$page++;
// generate smart pagination
$filename = 'reports.php';
$pagination = '';

$pagination = a_generate_smart_pagination($page, $total_rvideos, $limit, 1, $filename, '');



?>
<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
		<div class="span12">
		<div class="tabbable tabs-left">
		  <ul class="nav nav-tabs">
			<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane fade in active" id="help-overview">
			<p>This page will list all the problematic videos from your site as defined by your visitors or by the video checking bot.</p>
			</div>
		  </div>
		</div> <!-- /tabbable -->
		</div><!-- .span12 -->
	</div><!-- /help-assist -->
	<div class="content">
	<a href="#" id="show-help-assist">Help</a>
	
	<div class="entry-count">
		<ul class="pageControls">
			<li>
				<div class="floatL"><strong class="blue"><?php echo pm_number_format($total_rvideos); ?></strong><span>report(s)</span></div>
				<div class="blueImg"><img src="img/ico-videos-new.png" width="18" height="18" alt="" /></div>
			</li>
		</ul><!-- .pageControls -->
	</div>
	<h2>Reported Videos</h2>

	<div id="video_check_message" class="alert alert-info" style="display: none;"></div>
	
	<?php echo $info_msg; ?>
	<div class="tablename">
	<h6></h6>
	<div class="qsFilter move-right">
	<div class="btn-group input-prepend">

	<?php
	if($total_rvideos != 0) {
	?>
	<a href="#" class="btn btn-small btn-danger" onClick="del_allreports()">Delete all reports</a>
	<?php
	}
	?>
	</div><!-- .btn-group -->
	</div><!-- .qsFilter -->
	</div>

	<form name="reported_videos_checkboxes" action="reports.php?page=<?php echo $page;?>" method="post">
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
	 <thead>
	  <tr> 	
		<th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
		<th width="2%">&nbsp;</th>
		<th width="5%">Unique ID</th>
		<th width="2%">&nbsp;</th>
		<th width="">Video</th>
		<th width="20%">Reason</th>
		<th width="10%">Submitted by</th>
		<th align="center" style="text-align:center; width:90px;">Action</th>
	  </tr>
	 </thead>
	 <tbody>
		<?php if ($pagination != '') : ?>
		<tr class="tablePagination">
			<td colspan="8" class="tableFooter">
				<div class="pagination pull-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php echo $reported; ?>
		
		<?php if ($pagination != '') : ?>
		<tr class="tablePagination">
			<td colspan="8" class="tableFooter">
				<div class="pagination pull-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
	 </tbody>
	</table>

	<div class="clearfix"></div>
	
	<div id="stack-controls" class="list-controls">
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="submit" name="VideoChecker" id="VideoChecker" value="Check status" class="btn btn-small btn-success btn-strong" onclick="javascript: return false;">Check status</button>
		</div>
		<div class="btn-group">
			<button type="submit" name="Submit" value="Delete reports" class="btn btn-small btn-warning btn-strong">Delete reports</button>
		</div>
		<div class="btn-group">
			<button type="submit" name="Submit" value="Delete videos" class="btn btn-small btn-danger btn-strong">Delete videos</button>
		</div>
	</div>
	<?php echo csrfguard_form('_admin_reports_deletevideos');?>
	</form>
	</div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>