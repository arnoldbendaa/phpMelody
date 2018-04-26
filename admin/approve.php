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

$showm	= '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_prettypop = 1;
$_page_title = 'Videos pending approval';
include('header.php');

$action	 = $_GET['a'];
$id 	 = (int) $_GET['vid'];
$page	 = (int) $_GET['page'];

if ($_POST['Submit'] != '')
{
	
	if ($_POST['Submit'] == 'Delete')
	{
		$action = 'delvids';
	}
	if ($_POST['Submit'] == 'Approve')
	{
		$action = 'approveall';
	}
	
}

if($page == '' || !is_numeric($page))
   $page = 1;

$limit = (isset($_COOKIE['aa_videos_per_page'])) ? $_COOKIE['aa_videos_per_page'] : 25;

$from = $page * $limit - ($limit);
$errors = array();

$modframework->trigger_hook('admin_approve_top');
switch($action)
{
	case 'deleted':
		$info_msg = pm_alert_success('The selected entry was removed.');
	break;
	
	case 'approve':
		if($id == '')	break;
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
			break;
		}
		
		define('PHPMELODY', true);
		
		$video_details = array(	'uniq_id' => '',	
								'video_title' => '',	
								'description' => '',	
								'yt_id' => '',	
								'yt_length' => '',	
								'category' => '',	
								'submitted_user_id' => 0,
								'submitted' => '',	
								'source_id' => '',	
								'language' => '',	
								'age_verification' => '',
								'url_flv' => '',	
								'yt_thumb' => '',
								'mp4' => '',	
								'direct' => '',	
								'tags' => '',
								'restricted' => 0,
								'allow_comments' => 1 
								);		
		$sources = a_fetch_video_sources();
		
		$temp	= array();
		$query = mysql_query("SELECT * FROM pm_temp WHERE id = '".$id."'");
		$input = mysql_fetch_assoc($query);
		mysql_free_result($query);

		$video_details['video_title']	=	$input['video_title'];
		$video_details['description']	=	$input['description'];
		$video_details['submitted']		=	$input['username'];
		$video_details['direct']		=	$input['url'];
		$video_details['category']		=	$input['category'];
		$video_details['submitted_user_id']	= (int) username_to_id($input['username']);
		$video_details['submitted']		=	$input['username'];
		$video_details['source_id']		=	$input['source_id'];
		$video_details['language']		=	$input['language'];
		$video_details['tags']			=	$input['tags'];
		$video_details['yt_length'] 	= 	$input['yt_length'];
		$video_details['url_flv'] 		= 	$input['url_flv'];
		$video_details['mp4'] 			= 	$input['mp4'];
		$video_details['yt_thumb'] 		= 	$input['thumbnail'];
		$video_details['yt_id'] 		= 	$input['yt_id'];
		$video_details['language'] 		= 	1;
		$video_details['age_verification'] = 0;
		$video_details['added'] 		= 	time();
		$video_details['featured'] 		= 	0;
		$video_details['restricted'] 	= 	0;
		
		require_once( './src/localhost.php'); // just for download_thumb()
		
		$download_thumb = $sources['localhost']['php_namespace'] .'\download_thumb';
		
		$uniq_id = generate_video_uniq_id();
		
		//	fetch information about this video
		if ($input['source_id'] != $sources['localhost']['source_id'])
		{
			switch ($sources[ $video_details['source_id'] ]['source_name'])
			{
				case 'divx':
				case 'windows media player':
				case 'quicktime':
				case 'mp3':
					$video_details['source_id'] = $sources['other']['source_id'];
				break;
			}
			
			if ($video_details['yt_id'] == '')
			{
				$video_details['yt_id'] = substr( md5( time() ), 2, 9);
			}
			if ($video_details['url_flv'] == '')
			{
				$video_details['url_flv'] = $input['url'];
			}
		}
		else // user uploaded video
		{
			$video_details['url_flv'] = $input['url'];
			$video_details['direct'] = $input['url'];
			$video_details['yt_length'] = $input['yt_length'];
			
			if ($input['thumbnail'] != '')
			{
				$tmp_parts = explode('.', $input['thumbnail']);
				$ext = array_pop($tmp_parts);
				$ext = strtolower($ext);
				if (rename(_THUMBS_DIR_PATH . $input['thumbnail'], _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
				{
					$input['thumbnail'] =  $uniq_id . '-1.'. $ext;
				}
				
				$video_details['yt_thumb'] = _THUMBS_DIR . $input['thumbnail'];	
				generate_social_thumb(_THUMBS_DIR_PATH . $input['thumbnail']);
			}
			else
			{
				$video_details['yt_thumb'] = '';
			}
		}

		$video_details['uniq_id'] = $uniq_id;

		foreach($video_details as $k => $v)
		{
			$video_details[$k] = str_replace("&amp;", "&", $v);
		}
		$modframework->trigger_hook('admin_approve_insert_before');
		//	Ok, let's add this video to our database
		$new_video = insert_new_video($video_details, $new_video_id);
		
		if($new_video !== true)
		{
			$info_msg = pm_alert_error('Could not insert new video in your database.<br />MySQL returned: '. $new_video[0]);				
		}
		else
		{
			$modframework->trigger_hook('admin_approve_insert_after');
			//	download thumbnail
			if ('' != $video_details['yt_thumb'] && $video_details['source_id'] != $sources['localhost']['source_id'])
			{	 
				$img = $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
				
				if ( ! $img)
				{
					$info_msg = pm_alert_error('Could not download the thumbnail for video <em>'. $video_details['video_title']. '</em> from <code>'. $video_details['yt_thumb'].'</code>');
				}
				else
				{
					generate_social_thumb($img);
				}
			}
			
			if($video_details['tags'] != '')
			{
				$tags = explode(",", $video_details['tags']);
				foreach($tags as $k => $tag)
				{
					$tags[$k] = stripslashes(trim($tag));
				}
				//	remove duplicates and 'empty' tags
				$temp = array();
				for($i = 0; $i < count($tags); $i++)
				{
					if($tags[$i] != '')
						if($i <= (count($tags)-1))
						{
							$found = 0;
							for($j = $i + 1; $j < count($tags); $j++)
							{
								if(strcmp($tags[$i], $tags[$j]) == 0)
									$found++;
							}
							if($found == 0)
								$temp[] = $tags[$i];
						}
				}
				$tags = $temp;
				//	insert tags
				if(count($tags) > 0)
					insert_tags($uniq_id, $tags);
			}
			
			if (_MOD_SOCIAL)
			{
				$act_type = ($video_details['source_id'] == $sources['localhost']['source_id']) ? ACT_TYPE_UPLOAD_VIDEO : ACT_TYPE_SUGGEST_VIDEO; 
				
				log_activity(array(
							'user_id' => username_to_id($video_details['submitted']),
							'activity_type' => $act_type,
							'object_id' => $new_video_id,
							'object_type' => ACT_OBJ_VIDEO,
							'object_data' => $video_details
							)
					);
					

			}
			$modframework->trigger_hook('admin_approve_insert_final');
			$info_msg = pm_alert_success('Video has been approved<br />Would you like to <strong><a href="modify.php?vid='.$uniq_id.'">edit</a></strong> it now?'); 
		}

		//	remove the suggested video from 'pm_temp'
		@mysql_query("DELETE FROM pm_temp WHERE id = '".$id."'");
	break;
	
	case 'approveall':

		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
			break;
		}
	
		$video_ids = array();
		$video_ids = $_POST['video_ids'];
		$total_added = 0;
		
		$total_ids = count($video_ids);
		if ($total_ids > 0)
		{
			$suggestions = array();
			$sql = "SELECT * FROM pm_temp WHERE id IN (". implode(',', $video_ids) .")";
			$result = mysql_query($sql);
				
			while ($row = mysql_fetch_assoc($result))
			{
				$suggestions[] = $row;
			}
			mysql_free_result($result);
			
			$sources = a_fetch_video_sources();
			
			require_once( './src/localhost.php'); // just for download_thumb()
		
			$download_thumb = $sources['localhost']['php_namespace'] .'\download_thumb';
			
			$video_details = array();
			
			foreach ($suggestions as $k => $data)
			{
				$video_details['video_title']	=	$data['video_title'];
				$video_details['description']	=	$data['description'];
				$video_details['submitted']		=	$data['username'];
				$video_details['direct']		=	$data['url'];
				$video_details['category']		=	$data['category'];
				$video_details['submitted_user_id']	= (int) username_to_id($data['username']);
				$video_details['submitted']		=	$data['username'];
				$video_details['source_id']		=	$data['source_id'];
				$video_details['language']		=	$data['language'];
				$video_details['tags']			=	$data['tags'];
				$video_details['yt_length'] 	= 	$data['yt_length'];
				$video_details['url_flv'] 		= 	$data['url_flv'];
				$video_details['mp4'] 			= 	$data['mp4'];
				$video_details['yt_thumb'] 		= 	$data['thumbnail'];
				$video_details['yt_id'] 		= 	$data['yt_id'];
				$video_details['language'] 		= 	1;
				$video_details['age_verification'] = 0;
				$video_details['added'] 		= 	time();
				$video_details['featured'] 		= 	0;
				$video_details['restricted'] 	= 	0;
				
				$uniq_id = generate_video_uniq_id();
				
				if ($data['source_id'] != $sources['localhost']['source_id'])
				{
					switch ($sources[ $video_details['source_id'] ]['source_name'])
					{
						case 'divx':
						case 'windows media player':
						case 'quicktime':
						case 'mp3':
							$video_details['source_id'] = $sources['other']['source_id'];
						break;
					}
					
					if ($video_details['yt_id'] == '')
					{
						$video_details['yt_id'] = substr( md5( time() ), 2, 9);
					}
					if ($video_details['url_flv'] == '')
					{
						$video_details['url_flv'] = $data['url'];
					}
				}
				else // user uploaded video
				{
					$video_details['url_flv'] = $data['url'];
					$video_details['direct'] = $data['url'];
					$video_details['yt_length'] = $data['yt_length'];
					
					if ($data['thumbnail'] != '')
					{
						$tmp_parts = explode('.', $data['thumbnail']);
						$ext = array_pop($tmp_parts);
						$ext = strtolower($ext);

						if (rename(_THUMBS_DIR_PATH . $data['thumbnail'], _THUMBS_DIR_PATH . $uniq_id . '-1.'. $ext))
						{
							$data['thumbnail'] =  $uniq_id . '-1.'. $ext;
						}
						
						$video_details['yt_thumb'] = _THUMBS_DIR . $data['thumbnail'];	
						generate_social_thumb(_THUMBS_DIR_PATH . $data['thumbnail']);
					}
					else
					{
						$video_details['yt_thumb'] = '';
					}
				}
				
				$video_details['uniq_id'] = $uniq_id;
				
				foreach($video_details as $k => $v)
				{
					$video_details[$k] = str_replace("&amp;", "&", $v);
				}
				$modframework->trigger_hook('admin_approve_insert_before');
				//	Ok, let's add this video to our database
				$new_video = insert_new_video($video_details, $new_video_id);
				
				if ($new_video !== true)
				{
					$errors[] = 'Could not insert video <em>'. $video_details['video_title'] .'</em> in your database.<br />MySQL returned: '. $new_video[0];
				}
				else
				{
					$total_added++;
					$modframework->trigger_hook('admin_approve_insert_after');
					//	download thumbnail
					if ('' != $video_details['yt_thumb'] && $video_details['source_id'] != $sources['localhost']['source_id'])
					{
						$img = $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
						
						if ( ! $img)
						{
							$errors[] = 'Could not download the thumbnail for video <em>'. $video_details['video_title']. '</em> from <code>'. $video_details['yt_thumb'].'</code>';
						}
						else
						{
							generate_social_thumb($img);
						}
					}
					
					if($video_details['tags'] != '')
					{
						$tags = explode(",", $video_details['tags']);
						foreach($tags as $k => $tag)
						{
							$tags[$k] = stripslashes(trim($tag));
						}
						//	remove duplicates and 'empty' tags
						$temp = array();
						for($i = 0; $i < count($tags); $i++)
						{
							if($tags[$i] != '')
								if($i <= (count($tags)-1))
								{
									$found = 0;
									for($j = $i + 1; $j < count($tags); $j++)
									{
										if(strcmp($tags[$i], $tags[$j]) == 0)
											$found++;
									}
									if($found == 0)
										$temp[] = $tags[$i];
								}
						}
						$tags = $temp;
						//	insert tags
						if(count($tags) > 0)
							insert_tags($uniq_id, $tags);
					}
					
					if (_MOD_SOCIAL)
					{
						$act_type = ($video_details['source_id'] == $sources['localhost']['source_id']) ? ACT_TYPE_UPLOAD_VIDEO : ACT_TYPE_SUGGEST_VIDEO; 
						
						log_activity(array(
									'user_id' => username_to_id($video_details['submitted']),
									'activity_type' => $act_type,
									'object_id' => $new_video_id,
									'object_type' => ACT_OBJ_VIDEO,
									'object_data' => $video_details
									)
							);
					}
					$modframework->trigger_hook('admin_approve_insert_final');
				}
		
				//	remove the suggested video from 'pm_temp'
				if ($new_video == true)
				{
					@mysql_query("DELETE FROM pm_temp WHERE id = '". $data['id'] ."'");
				}
			} // end foreach();

			if ($total_added > 0)
			{
				if ($total_added == $total_ids)
				{
					$info_msg = pm_alert_success('The selected videos were successfully approved.');
				}
				else
				{
					$info_msg = pm_alert_success('Added <strong>'. $total_added .'</strong> out of <strong>'. $total_ids .'</strong> selected videos.');
				}
			}
			
		}
		else
		{
			$info_msg = pm_alert_warning('Please select something first.');
		}

		
	break;

	case 'delall':
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
			break;
		}
			
		$files = array();
		$sql = "SELECT url FROM pm_temp WHERE source_id = '1'";
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$files[] = $row['url'];
			}
			mysql_free_result($result);
		}
		
		if (count($files) > 0)
		{
			foreach ($files as $k => $filename)
			{
				if (file_exists(_VIDEOS_DIR_PATH . $filename) && strlen($filename) > 0)
				{
					unlink(_VIDEOS_DIR_PATH . $filename);
				}
			}
		}
		
		mysql_query("TRUNCATE TABLE pm_temp");
		$info_msg = pm_alert_success('All pending videos have been removed.');
		
	break;
	
	case 'delvid':
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
			break;
		}
		
		$sql = "SELECT url, source_id, thumbnail FROM pm_temp WHERE id = '". $id ."'";
		$result = mysql_query($sql);
		$video = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ($video['source_id'] == 1)
		{
			if (file_exists(_VIDEOS_DIR_PATH . $video['url']) && strlen($video['url']) > 0)
			{
				unlink(_VIDEOS_DIR_PATH . $video['url']);
			}
			if ($video['thumbnail'] != '')
			{
				unlink(_THUMBS_DIR_PATH . $video['thumbnail']);
			}
		}

		@mysql_query("DELETE FROM pm_temp WHERE id = '".$id."'");
	
		echo '<meta http-equiv="refresh" content="0;URL=approve.php?a=deleted&page='. $page .'" />';
		exit();
	break;
	
	case 'delvids':
		
		if ( ! csrfguard_check_referer('_admin_approve'))
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
			break;
		}
		
		if($_POST['Submit'] == 'Delete')
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
					$videos = array();
					$sql = "SELECT url, source_id, thumbnail FROM pm_temp WHERE id IN (". $in_arr .") AND source_id = '1'";
					$result = mysql_query($sql);
					
					while ($row = mysql_fetch_assoc($result))
					{
						$videos[] = $row;
					}
					mysql_free_result($result);
					
					$sql = "DELETE FROM pm_temp WHERE id IN (" . $in_arr . ")";
					$result = @mysql_query($sql);
					if ( ! $result)
					{
						$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
					}
					else
					{
						$info_msg = pm_alert_success('The selected videos were removed.');
					}
					
					if (count($videos) > 0)
					{
						foreach ($videos as $k => $video)
						{
							if (file_exists(_VIDEOS_DIR_PATH . $video['url']))
							{
								unlink(_VIDEOS_DIR_PATH . $video['url']);
							}
							if ($video['thumbnail'] != '')
							{
								unlink(_THUMBS_DIR_PATH . $video['thumbnail']);
							}
						}
					}
				}
			}
		}	
	break;
} //	end switch

// COUNT VIDEOS IN DB
$total_videos = count_entries('pm_temp', '', '');

if($total_videos - $from == 1)
	$page--;

$approve_nonce = csrfguard_raw('_admin_approve');

$videos = a_list_temp('', '', $from, $limit, $page); 

if($total_videos - $from == 1)
	$page++;

// generate smart pagination
$filename = ('' != $_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : 'approve.php';
$pagination = '';

$pagination = a_generate_smart_pagination($page, $total_videos, $limit, 1, $filename, '');

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
            <p>In case you allow video suggestions or video uploads to your site, here is the place they will be awaiting your approval. This process requires human intervention to prevent any abuse or ill-intended attempts.
            You can preview each submitted videos by clicking the play button from the thumbnail image.</p>
            <p>Approve any satisfactory submissions by clicking on the &quot;check&quot; icon from the &quot;Actions&quot; column. The video will be published as submitted by the user but you can choose to modify it after approval.</p>
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_videos); ?></strong><span>videos</span></div>
                <div class="blueImg"><img src="img/ico-videos-new.png" width="18" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>Videos Pending Approval</h2>
<?php echo $info_msg; ?>
<?php 
if (is_array($errors) && count($errors) > 0)
{
	echo pm_alert_error($errors);
}
?>

<div class="row-fluid">
<div class="span8">
</div><!-- .span8 -->
<div class="span4">
  <form name="videos_per_page" action="approve.php" method="get" class="form-inline pull-right">
  	<input type="hidden" name="page" value="1" />
  	<label><small>Videos/page</small></label>
    <select name="results" class="smaller-select" onChange="this.form.submit()" >
	<option value="25" <?php if($limit == 25) echo 'selected="selected"'; ?>>25</option>
	<option value="50" <?php if($limit == 50) echo 'selected="selected"'; ?>>50</option>
	<option value="75" <?php if($limit == 75) echo 'selected="selected"'; ?>>75</option>
	<option value="100" <?php if($limit == 100) echo 'selected="selected"'; ?>>100</option>
	<option value="125" <?php if($limit == 125) echo 'selected="selected"'; ?>>125</option>
    </select>
  </form>
</div>
</div>

<form name="approve_videos_checkboxes" action="approve.php?page=<?php echo $page;?>" method="post">

<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
  <tr> 
	<th align="center" style="text-align:center" width="20"><input type="checkbox" class="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	<th width="15">&nbsp;</th>
    <th width="40%">Title &amp; Description</th>
	<th>Tags</th>
    <th>Submitted on</th>
	<th width="5%">Submitted by</th>
    <th>Category</th>
    <th align="center" style="text-align:center; width: 120px;">Action</th>
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
	
	<?php echo $videos; ?>
	
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
    <div class="pull-left">
    </div>
	<div class="btn-toolbar">
	<div class="btn-group">
    <button type="submit" name="Submit" value="Approve" class="btn btn-small btn-success btn-strong">Approve</button>
    </div>
    <div class="btn-group dropup">
    <button type="submit" name="Submit" value="Delete" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">Delete</button>
    <button class="btn  btn-small btn-danger dropdown-toggle" data-toggle="dropdown">
    <span class="caret"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li><a href="#" rel="tooltip" title="Remove ALL (<?php echo $total_videos; ?>) pending videos?" onClick="del_alltemp()">Delete all</a></li>
    </ul>
    </div>
	</div>
	<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $approve_nonce['_pmnonce'];?>" value="<?php echo $approve_nonce['_pmnonce'];?>" />
	<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $approve_nonce['_pmnonce'];?>" value="<?php echo $approve_nonce['_pmnonce_t'];?>" />
    </div><!-- #list-controls -->

</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>