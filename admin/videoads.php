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

$showm = '9';
$_page_title = 'Pre-roll video ads manager';
include('header.php');

$action = $_GET['act'];
if( $action == '' || empty($action))
	$action = '';


function manage_ad_form($action = 'addnew', $item = false)//$id = 0, $name = '', $flv_url = '', $redirect_url = '', $redirect_type = '', $active = 0)
{
	if (empty($item['id']))
	{
		$item['id'] = 0;
	}
	
	$target = '';
	switch($action)
	{
		case 'addnew':
			$target = 'videoads.php?act=addnew';
		break;
		case 'edit':
			$target = ($id != 0) ? 'videoads.php?act=edit&id='.$id : 'videoads.php?act=edit';
		break;
	}
	// generate form
	?>
	<form name="videoad" method="post" action="<?php echo $target; ?>">
	<table width="100%" border="0" cellpadding="4">
	  <tr class="table_row1">
		<td class="fieldtitle" width="210px">Name</td>
		<td><input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" size="40" /></td>
	  </tr>
	  <tr  class="table_row1">
		<td class="fieldtitle" valign="top">Video Ad URL <a href="#" rel="tooltip" title="Supported file types: *.flv, *.mp4"><i class="icon-info-sign"></i></a><br /><small>Supported file types: <span style="color:#000">*.flv, *.mp4</span></small></td>
		<td><input type="text" name="flv_url" value="<?php echo $item['flv_url']; ?>" size="120" /></td>
	  </tr>
	  <tr  class="table_row1">
		<td class="fieldtitle" valign="top">Advertised URL</td>
		<td><input type="text" name="redirect_url" value="<?php echo $item['redirect_url'] ?>" size="120" /></td>
	  </tr>
	  <tr class="table_row1">
		<td class="fieldtitle" valign="top">Redirect Type</td>
		<td>
			<label><input type="radio" name="redirect_type" value="0" <?php echo ($item['redirect_type'] == 0) ? 'checked' : ''; ?> /> Open <em>Advertised URL</em> in new window</label>
			<br />
			<label><input type="radio" name="redirect_type" value="1" <?php echo ($item['$redirect_type'] == 1) ? 'checked' : ''; ?> /> Open <em>Advertised URL</em> in the same window</label>
		</td>
	  </tr>
	  <tr class="table_row1">
		<td class="fieldtitle">Status</td>
		<td>
			<label><input name="status" type="radio" value="0" <?php echo ($item['status'] == 0) ? 'checked' : ''; ?> /> Inactive</label> <label><input name="status" type="radio" value="1" <?php echo ($item['status'] == 1) ? 'checked' : ''; ?> /> Active</label>
		</td>
	  </tr>
	  <tr class="table_row1">
		<td class="fieldtitle" valign="top">Enable Statistics</td>
		<td>
			<label><input type="radio" name="disable_stats" value="0" <?php echo ($item['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
			<label><input type="radio" name="disable_stats" value="1" <?php echo ($item['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
		</td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="Submit" value="Submit" class="btn" /></td>
	  </tr>
	</table>
	</form>
	<?php
	
	return;
}
?>
<style>
label input {
  line-height: 1em;
  padding: 0;
  margin: 0;
  margin-left: 4px;
  line-height: 0;
  top: -3px;
  position: relative;
  font-weight: normal;
}
</style>
<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
		<div class="span12">
		<div class="tabbable tabs-left">
		  <ul class="nav nav-tabs">
			<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane fade in active" id="help-overview">
			<p>Video ads are clickable pre-roll ads that appear at specified intervals. To enable video ads, you need to provide a *.FLV or *.MP4 video (i.e. the video ad) and an URL which (i.e. the sponsored/promoted website).</p>
			<p>If you'd like to modify how often users see your pre-roll video ads, visit the <strong><a href="settings.php?view=t5">Settings > Video Ads Settings</a></strong> page.</p>
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
				<div class="floatL"><strong class="blue"><?php echo pm_number_format($total_ads); ?></strong><span>ad<?php echo ($total_ads > 1) ? 's' : '';?></span></div>
				<div class="blueImg"><img src="img/ico-ads-new.png" width="19" height="18" alt="" /></div>
			</li>
		</ul><!-- .pageControls -->
	</div>
	<h2>Pre-roll Video Ads Manager <a class="label opac5" href="#addNew" onclick="location.href='#addNew';" data-toggle="modal">+ add new</a></h2>
<?php

switch($action)
{
	case 'addnew':
		if($_GET['act'] == 'addnew')
		{
			if(isset($_POST['Submit']))
			{
				
				$arr_fields = array('name' => "Name", 'flv_url' => "Video Ad location", 'redirect_url' => 'Advertised URL', 'status' => 'Status');
				$errors = array();
				foreach($_POST as $k => $v)
				{
					if(((is_array($v) && empty($v)) || (is_string($v) && trim($v) == '')) && array_key_exists($k, $arr_fields) === TRUE)
					{
						$errors[] = "The '".$arr_fields[$k]."' field shouldn't be empty.";
					}	
				}
				if (count($errors) > 0)
				{
					$info_msg = pm_alert_error($errors);
					echo manage_ad_form('addnew', $_POST);//0, $_POST['name'], $_POST['flv_url'], $_POST['redirect_url'], $_POST['redirect_type'], $_POST['status']);
				}
				else
				{
					$status = ($_POST['status'] == 1) ? 1 : 0;
					$redirect_url = trim($_POST['redirect_url']);
					$name = trim($_POST['name']);
					$flv_url = trim($_POST['flv_url']);
					$redirect_type = trim($_POST['redirect_type']);
					$hash = md5( rand(0,123) . time() );
					$disable_stats = (int) $_POST['disable_stats'];
					
					if (strpos($redirect_url, 'http') !== 0)
						$redirect_url = make_url_https("http://".$redirect_url);
					if (strpos($flv_url, 'http') !== 0)
						$flv_url = make_url_https("http://".$flv_url);
					
					$sql = "INSERT INTO pm_videoads SET hash = '".$hash."',
														name = '".secure_sql($name)."', 
														flv_url = '".secure_sql($flv_url)."',
														redirect_url = '".secure_sql($redirect_url)."', 
														redirect_type = '".secure_sql($redirect_type)."',
														status = '".$status."',
														disable_stats = '". $disable_stats ."'";
														
					$modframework->trigger_hook('admin_videoads_add_before');
					$query = mysql_query($sql);
					if ( ! $query)
					{
						$info_msg = pm_alert_error('There was a problem while inserting new ad in database.<br />MySQL returned: '. mysql_error());
					}
					else
					{
						$new_ad_id = mysql_insert_id();
						$modframework->trigger_hook('admin_videoads_add_after');

						$msg = 'Your pre-roll video ad was successfully created.';
						if($status == 0)
							$msg .= "<br />Note: New ads are not enabled by default. Remember to enable ads after creating them.";
						$msg = pm_alert_success($msg);
						$msg .= '<input name="continue" type="button" value="&larr; Return to the Video Ad Manager" onClick="location.href=\'videoads.php\'" class="btn" />';
						
						echo $msg;
					}
				}
			}
			else
			{
				echo manage_ad_form('addnew');
			}
		}	
	break;
	
	case 'edit':
		$id = $_GET['id'];
		if($id <= 0 || !is_numeric($id) || $id == '')
		{
			$info_msg = pm_alert_error('Invalid or missing ID.');
		}
		else
		{
			if(isset($_POST['Submit']))
			{
				$arr_fields = array('name' => "Name", 'flv_url' => "Video Ad location", 'redirect_url' => 'Advertised URL', 'status' => 'Status');
				$errors = array();
				foreach($_POST as $k => $v)
				{
					if(((is_array($v) && empty($v)) || (is_string($v) && trim($v) == '')) && array_key_exists($k, $arr_fields) === TRUE)
					{
						$errors[] = "The '".$arr_fields[$k]."' field shouldn't be empty.";
					}
				}				
				if (count($errors) > 0)
				{
					$_POST['id'] = $id;
					
					echo pm_alert_errors($errors);
					echo manage_ad_form('addnew', $_POST);//$id, $_POST['name'], $_POST['flv_url'], $_POST['redirect_url'], $_POST['redirect_type'], $_POST['status']);
					
					include('footer.php');
					
					exit();
				}
				$status = ($_POST['status'] == 1) ? 1 : 0;
				$redirect_url = trim($_POST['redirect_url']);
				$name = trim($_POST['name']);
				$flv_url = trim($_POST['flv_url']);
				$redirect_type = trim($_POST['redirect_type']);
				$disable_stats = (int) $_POST['disable_stats']; 
				
				if (strpos($redirect_url, 'http') !== 0)
					$redirect_url = make_url_https("http://".$redirect_url);
				if (strpos($flv_url, 'http') !== 0)
					$flv_url = make_url_https("http://".$flv_url);
				
				$sql = "UPDATE pm_videoads SET name = '".secure_sql($name)."', 
												flv_url = '".secure_sql($flv_url)."',
												redirect_url = '".secure_sql($redirect_url)."', 
												redirect_type = '".secure_sql($redirect_type)."',
												status = '".$status."',
												disable_stats = '". $disable_stats ."' 
										 WHERE id = '".$id."' ";
				$modframework->trigger_hook('admin_videoads_edit_before');
				$query = mysql_query($sql);
				if ( ! $query)
				{
					$info_msg = pm_alert_error('There was an error while updating your ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$modframework->trigger_hook('admin_videoads_edit_after');
					$info_msg = pm_alert_success('<strong>'. htmlspecialchars($name) .'</strong> updated.');
					$info_msg .= '<input name="continue" type="button" value="&larr; Return to the Video Ad Manager" onClick="location.href=\'videoads.php\'" class="btn" />';
					echo $info_msg;
				}
			}
			else
			{
				$query = mysql_query("SELECT * FROM pm_videoads WHERE id='".$id."'");
				if ( ! $query)
				{
					echo pm_alert_error('There was an error while retrieving your data.<br />MySQL returned: '. mysql_error());

					include('footer.php');
					exit();
				}
				
				$ad = mysql_fetch_assoc($query);
				if ($ad['id'] == '')
				{
					$info_msg = pm_alert_error('The selected as was not found in your database.');
				}
				else
				{
					echo manage_ad_form('edit', $ad['id'], $ad['name'], $ad['flv_url'], $ad['redirect_url'], $ad['redirect_type'], $ad['status']);
				}
			}
		}
	
	break;
	case 'delete':
	case 'activate':
	case 'deactivate':
	case 'reset':
	default:
		
		$total_ads = count_entries('pm_videoads', '', '');
		
		if($action == 'delete')
		{
			$id = $_GET['id'];
			if($id <= 0 || !is_numeric($id) || $id == '')
			{
				$info_msg = pm_alert_error('Invalid or missing ID.');
			}
			else
			{
				$query = mysql_query("DELETE FROM pm_videoads WHERE id = '".$id."'");
				if( !$query )
				{
					$info_msg = pm_alert_error('There was a problem while deleting this ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$sql = "DELETE FROM pm_ads_log 
							WHERE ad_id = $id 
							  AND ad_type = ". _AD_TYPE_VIDEO;
					@mysql_query($sql); 
					$modframework->trigger_hook('admin_videoads_delete');
					$info_msg = pm_alert_success('The ad was deleted.');
				}
			}
		}
		if($action == 'activate' || $action == 'deactivate')
		{
			$id = $_GET['id'];
			if($id <= 0 || !is_numeric($id) || $id == '')
			{
				$info_msg = pm_alert_error('Invalid or missing ID.');
			}
			else
			{	
				$sql = '';
				if($action == "activate")
					$sql = "UPDATE pm_videoads SET status='1' WHERE id = '".$id."' LIMIT 1";
				else
					$sql = "UPDATE pm_videoads SET status='0' WHERE id = '".$id."' LIMIT 1";
				$modframework->trigger_hook('admin_videoads_activate_deactivate');
				$query = mysql_query($sql);
				if ( ! $query )
				{
					$info_msg = pm_alert_error('A problem was encountered while activating/deactivating this ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$info_msg = ($action == "activate") ? pm_alert_success('The ad is now active.') : pm_alert_success('The ad was deactivated.');
				}
			}
		}
	?>

<?php 
if ($config['video_player'] == 'jwplayer')
{
	echo pm_alert_error('Sorry, this feature is compatible with <strong>Flowplayer</strong> only.');
}
?>

<?php echo $info_msg; ?>


<div class="modal hide fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">Create a new video ad</h3>
</div>
<form name="ad_manager" method="post" action="videoads.php?act=addnew">
<div class="modal-body">
<label>Name</label>
<input type="text" name="name" value="" size="40" />

<label>Video Ad URL <a href="#" rel="tooltip" title="Supported file types: *.flv, *.mp4"><i class="icon-info-sign"></i></a></label>
<input type="text" name="flv_url" value="" size="120" placeholder="http://" />

<label>Advertised URL</label>
<input type="text" name="redirect_url" value="" size="120" placeholder="http://" />

<label>Enable Statistics</label>
<label><input type="radio" name="disable_stats" value="0" checked="checked"> Yes</label> 
<label><input type="radio" name="disable_stats" value="1"> No</label>
<?php  $modframework->trigger_hook('admin_videoads_add_options');?>
<input type="hidden" name="redirect_type" value="0" />
<input type="hidden" name="active" value="1" />
</div>

<div class="modal-footer">
<button class="btn btn-link btn-strong" data-dismiss="modal" aria-hidden="true">Cancel</button>
<button type="submit" name="Submit" value="Submit" class="btn btn-success btn-strong" />Submit</button>
</div>
</form>
</div>


<div class="tablename">
<div class="qsFilter move-right pull-right">
<?php if ($action != 'addnew' && $action != 'edit') : ?>
<a href="#addNew" class="btn btn-success btn-strong" data-toggle="modal">Create a new video ad</a>
<?php endif; ?>
</div><!-- .qsFilter -->
</div>
<br />
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead> 
  <tr>
	<th>Name</th>
	<th width="8%">Impressions</th>
	<th width="7%">Clicks</th>
	<th width="7%">CTR</th>
	<th width="10%">Status</th>
	<th width="23%" style="width: 160px;">Action</th>
  </tr>
 </thead>
 <tbody>
 
  <?php
	
	// display all ads
	$query = mysql_query("SELECT * FROM pm_videoads ORDER BY id ASC");
	$display = '';
	$i = 0;
	while($row = mysql_fetch_assoc($query))
	{	
		$clean_title = str_replace(array('"', "'"), array('', "\'"), $row['name']);
		$col = ($i++ % 2) ? 'table_row1' : 'table_row2';
		
		$sql_tmp = "SELECT SUM(impressions) as total_impressions, SUM(clicks) as total_clicks  
					FROM pm_ads_log 
					WHERE ad_id = ". $row['id'] ." 
					  AND ad_type = ". _AD_TYPE_VIDEO;

		$result_tmp = mysql_query($sql_tmp);
		$totals = mysql_fetch_assoc($result_tmp);
		mysql_free_result($result_tmp);
		
		if ($totals['total_impressions'] == '')
		{
			$totals['total_impressions'] = 0;
			$totals['total_clicks'] = 0;
			$ctr = 0;
		}
		else 
		{
			$ctr = round( ((int) $totals['total_clicks'] * 100 / (int) $totals['total_impressions']), 2);
		}

		?>
		<tr class="<?php echo $col; ?>">
			<td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
			<td align="center" style="text-align:center"><?php echo pm_number_format($totals['total_impressions']); ?></td>
			<td align="center" style="text-align:center"><?php echo pm_number_format($totals['total_clicks']); ?></td>
			<td align="center" style="text-align:center"><strong><?php echo $ctr; ?>%</strong></td>
			<td align="center" style="text-align:center" class="table-col-action">
				<small>
				<?php if ($row['status'] == 1) : ?>
					<a href="videoads.php?act=deactivate&id=<?php echo $row['id']; ?>" class="label label-success label-clickable" rel="tooltip" title="Click to deactivate">Active</a>
				<?php else : ?>
					<a href="videoads.php?act=activate&id=<?php echo $row['id']; ?>" class="label label-clickable" rel="tooltip" title="Click to activate">Inactive</a></span>
				<?php endif; ?>
				</small>
			</td>
			<td align="center" class="table-col-action" style="text-align:center">
				<?php if ($row['status'] == 0) : ?>
				 <a href="videoads.php?act=activate&id=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Activate Ad"><i class="icon-ok-sign"></i></a>
				<?php else : ?>
				 <a href="videoads.php?act=deactivate&id=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Deactivate Ad"><i class="icon-remove-sign"></i></a>
				<?php endif; ?>
				<a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> btn btn-mini btn-link" rel="tooltip" title="Edit"><i class="icon-pencil"></i> </a> <a href="#" onClick="delete_ad('<?php echo  $clean_title ; ?>', 'videoads.php?act=delete&id=<?php echo $row['id']; ?>')" class="btn btn-mini btn-link" rel="tooltip" title="Delete"><i class="icon-remove" ></i> </a>
			</td>
		</tr>
	
		<tr>
			<td colspan="6" style="margin:0;padding:0;">
				<div id="adzone_update_<?php echo  $row['id'] ; ?>" name="<?php echo  $row['id'] ; ?>">
					<div class="adzone_update_form" style="padding: 10px; margin: 10px;"> 
					<form name="adzone_update_<?php echo  $row['id'] ; ?>" method="post" action="videoads.php?act=edit&id=<?php echo $row['id']; ?>">
						<label>Name</label>
						<input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" size="40" />
						
						<label>Video Ad URL <a href="#" rel="tooltip" title="Supported file types: *.flv, *.mp4"><i class="icon-info-sign"></i></a></label>
						<input type="text" name="flv_url" value="<?php echo $row['flv_url']; ?>" size="40" />
						
						<label>Advertised URL</label>
						<input type="text" name="redirect_url" value="<?php echo $row['redirect_url']; ?>" size="40" />
						
						<label>Redirect Type</label>
						<label><input type="radio" name="redirect_type" value="0" <?php echo ($row['redirect_type'] == 0) ? 'checked' : '';?> /> <small>Open <em>Advertised URL</em> in new window</small></label>
							
						<label><input type="radio" name="redirect_type" value="1" <?php echo ($row['redirect_type'] == 1) ? 'checked' : '';?> /> <small>Open <em>Advertised URL</em> in the same window</small></label>
						
						<label>Enable Statistics</label>
						<label><input type="radio" name="disable_stats" value="0" <?php echo ($row['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
						<label><input type="radio" name="disable_stats" value="1" <?php echo ($row['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
						<?php $modframework->trigger_hook('admin_videoads_edit_options');?>
						<input type="submit" name="Submit" value="Submit" class="btn btn-mini btn-success border-radius0" />
						<a href="#" id="adzone_update_<?php echo  $row['id'] ; ?>" class="btn-mini">Cancel</a>

						<input type="hidden" name="status" value="<?php echo $row['status']; ?>" />
					</form>
					</div>
				</div>
			</td>
		</tr>
		<?php 
	}

	if($i == 0) {
		echo '<tr><td colspan="6">No video ads have been defined.</td></tr>';	
	}
	mysql_free_result($query);
	
	$total_active_ads = count_entries('pm_videoads', 'status', '1');
	update_config('total_videoads', $total_active_ads);
	
	break;
}

?>
 </tbody>
</table>
	</div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>