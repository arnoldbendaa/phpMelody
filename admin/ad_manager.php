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
$_page_title = 'Classic banners';
include('header.php');

function manage_ad_form($action = 'addnew', $item = false)
{
	global $modframework;
	if (empty($item['id']))
	{
		$item['id'] = 0;
	}
	
	$target = '';
	switch($action)
	{
		case 'addnew':
			$target = 'ad_manager.php?act=addnew';
		break;
		case 'edit':
			$target = ($id != 0) ? 'ad_manager.php?act=edit&id='.$item['id'] : 'ad_manager.php?act=edit';
		break;
	}
	
	?>
	<form name="ad_manager" method="post" action="<?php echo $target; ?>" enctype="application/x-www-form-urlencoded">
	<table width="100%" border="0" cellpadding="4">
	  <tr>
		<td class="fieldtitle" width="10%">Name:</td>
		<td><input type="text" name="position" value="<?php echo htmlspecialchars($item['position']); ?>" size="40" /></td>
	  </tr>
	  <tr>
		<td class="fieldtitle" width="10%">Description:</td>
		<td><input type="text" name="description" value="<?php echo htmlspecialchars($item['description']); ?>" size="40" /></td>
	  </tr>
	  <tr>
		<td class="fieldtitle" valign="top">HTML Code:</td>
		<td><textarea name="code" cols="60" rows="7"><?php echo $item['code']; ?></textarea></td>
	  </tr>
	  <tr>
		<td class="fieldtitle" valign="top">Enable Statistics:</td>
		<td>
			<label><input type="radio" name="disable_stats" value="0" <?php echo ($item['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> <label><input type="radio" name="disable_stats" value="1" <?php echo ($item['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
		</td>
	  </tr>
	  <?php $modframework->trigger_hook('admin_classic_ads_editoptions_backup');?>
	  <tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="Submit" value="Save" class="btn btn-success" /></td>
	  </tr>
	</table>
	</form>
	<?php
	return;
}


$action = $_GET['act'];

$total_ads = count_entries('pm_ads', '', '');

?>
<!-- create new ad zone form modal -->
<div class="modal hide fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Create new ad zone</h3>
	</div>
	<form name="ad_manager" method="post" action="ad_manager.php?act=addnew" enctype="application/x-www-form-urlencoded">
		<div class="modal-body">
			<label>Name</label>
			<input type="text" name="position" value="" size="40" />
			
			<label>Description</label>
			<input type="text" name="description" value="" size="40" />
			
			<label>HTML Code for your Ad</label>
			<textarea name="code" cols="60" rows="7" class="span5"></textarea>
			
			<label>Enable Statistics</label>
			<label><input type="radio" name="disable_stats" value="0" checked="checked"> Yes</label> 
			<label><input type="radio" name="disable_stats" value="1"> No</label>
			<?php $modframework->trigger_hook('admin_classic_ads_options');?>
		</div>
		<div class="modal-footer">
		<input type="hidden" name="active" value="1" />
		<button data-dismiss="modal" aria-hidden="true" class="btn btn-link btn-strong">Cancel</button>
		<button type="submit" name="Submit" value="Submit" class="btn btn-success btn-strong" />Save</button>
	</div>
	</form>
</div>

<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
		<div class="span12">
		<div class="tabbable tabs-left">
		  <ul class="nav nav-tabs">
			<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
			<li><a href="#help-onthispage" data-toggle="tab">TPL Code</a></li>
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane fade in active" id="help-overview">
			<p>The build-in ad manager allows you to define ads zones and assign advertisements within those ad zones.<br />An ad zone is an area on your site where you intend to place advertisements (e.g. header, under the video, registration page, under article, etc.). Once an ad zone is created all you have to do is insert the ad code. By using ad zones you can easily replace obsolete or low performing ads.</p>
			</div>
			<div class="tab-pane fade" id="help-onthispage">
			<p>The TPL code is the assigned variable you need to use in your current template. There are several presets that come with every installation of PHP Melody, as listed below.<br />In this case, no template modifications are required. Just paste in your ad code and you're ready to go.</p>
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
				<div class="blueImg"><div class="pm-sprite ico-ads-small"></div></div>
			</li>
		</ul><!-- .pageControls -->
	</div>
	<h2>Classic Banners <a class="label opac5" href="#addNew" onclick="location.href='#addNew';" data-toggle="modal">+ add new</a></h2>
		
<?php

switch($action)
{
	case 'addnew':
		if($_GET['act'] == 'addnew')
		{
			if(isset($_POST['Submit']))
			{
				$modframework->trigger_hook('admin_classic_ads_addnew_before');
				$arr_fields = array('position' => "Name", 'code' => "Code", 'active' => 'Status');
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
					echo pm_alert_error($errors);
					echo manage_ad_form('addnew', $_POST);// 0, $_POST['position'], $_POST['code'], $_POST['active']);
				}
				else
				{
					$position = secure_sql($_POST['position']);
					$code = secure_sql($_POST['code']);
					$description = secure_sql($_POST['description']);
					$active = ($_POST['active'] == 1) ? 1 : 0;
					$disable_stats = (int) $_POST['disable_stats'];
					$modframework->trigger_hook('admin_classic_ads_addnew_mid');
					$query = mysql_query("INSERT INTO pm_ads SET position = '".$position."', description = '".$description."', code = '".$code."', active = '".$active."', disable_stats = '". $disable_stats ."'");
					if ( ! $query)
					{
						echo pm_alert_error('There was an error while inserting the new ad in database.<br />MySQL returned: '. mysql_error());
					}	
					else
					{
						$new_ad_id = mysql_insert_id();
						$modframework->trigger_hook('admin_classic_ads_addnew_after');
						$msg = '
						<h4>Done!</h4>
						<p>Your ad zone has been created. Since this is a new <strong>ad zone</strong>, you have manually add this ad zone to the desired location within the template.</p>
						<ol>
						<li>Pick the location for this new ad zone (e.g. header.tpl, index.tpl, footer.tpl)</li>
						<li>Paste the following code wherever you wish to display ads associate with this ad zone: <strong>{$ad_'.$new_ad_id.'}</strong></li>
						</ol>';
						if($_POST['active'] == 0)
							$msg .= "<br />New ads are not enabled by default. Remember to enable ads after creating them.";
						$msg = pm_alert_success($msg);
						$msg .= '<input name="continue" type="button" value="&larr; Return to Ad Manager" onClick="location.href=\'ad_manager.php\'" class="btn" />';
						
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
		if ($id <= 0 || !is_numeric($id) || $id == '')
		{
			echo pm_alert_error('ID is not a valid value or it is missing.');
		}
		else
		{
			if(isset($_POST['Submit']))
			{
				$modframework->trigger_hook('admin_classic_ads_edit_before');
				$arr_fields = array('position' => "Name");
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
					
					echo pm_alert_error($errors);
					echo manage_ad_form('edit', $_POST);//, $id, $_POST['position'], $_POST['description'], $_POST['code'], $_POST['active']);
					
					include('footer.php');
					
					exit();
				}
				$position = secure_sql($_POST['position']);
				$code = secure_sql($_POST['code']);
				$description = secure_sql($_POST['description']);
				$active = ($_POST['active'] == 1) ? 1 : 0;
				$disable_stats = (int) $_POST['disable_stats']; 
				$modframework->trigger_hook('admin_classic_ads_edit_mid');
				$query = mysql_query("UPDATE pm_ads SET position = '".$position."',
														description = '".$description."',
														code = '".$code."',
														active = '".$active."',
														disable_stats = '". $disable_stats ."' 
													WHERE id='".$id."'");
				if ( ! $query)
				{
					echo pm_alert_error('There was an error while inserting the new ad in database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					$modframework->trigger_hook('admin_classic_ads_edit_after');
					echo pm_alert_success('The ad zone was updated.');
					echo '<input name="continue" type="button" value="&larr; Return to Ad Manager page" onClick="location.href=\'ad_manager.php\'" class="btn" />';
				}
					
			}
			else
			{
				$query = mysql_query("SELECT * FROM pm_ads WHERE id='".$id."'");
				if ( ! $query)
				{
					echo pm_alert_error('There was an error while retrieving your data.<br />MySQL returned: '. mysql_error());

					include('footer.php');
					exit();
				}
				
				$ad = mysql_fetch_assoc($query);
				if ($ad['id'] == '')
				{
					echo pm_alert_error('The selected as was not found in your database.');
				}
				else
				{
					echo manage_ad_form('edit', $ad); //$ad['id'], $ad['position'], $ad['description'], $ad['code'], $ad['active']);
				}
			}
		}
	
	break;
	
	case 'delete':
	case 'activate':
	case 'deactivate':
	case '':
	default:
	
		$total_ads = count_entries('pm_ads', '', '');

		if($action == 'delete')
		{
			$id = $_GET['id'];
			if ($id <= 0 || !is_numeric($id) || $id == '')
			{
				echo pm_alert_error('Invalid or missing ID.');
			}
			else if(in_array($id, array(1, 2, 3, 4, 5, 6, 7)) !== FALSE)
			{
				echo pm_alert_error('Sorry, the default ad spots cannot be removed. You can choose to disable them or create new ad zones.');
			}
			else
			{
				$modframework->trigger_hook('admin_classic_ads_delete');
				$query = mysql_query("DELETE FROM pm_ads WHERE id = '".$id."'");
				if ( ! $query )
				{
					echo pm_alert_error('There was a problem while deleting this ad zone.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					
					$sql = "DELETE FROM pm_ads_log 
							WHERE ad_id = $id 
							  AND ad_type = ". _AD_TYPE_CLASSIC;
					@mysql_query($sql); 

					echo pm_alert_success('The ad zone was deleted.');
				}
			}
		}
		if($action == 'activate' || $action == 'deactivate')
		{
			$id = $_GET['id'];
			if ($id <= 0 || !is_numeric($id) || $id == '')
			{
				echo pm_alert_error('Invalid or missing ID.');
			}
			else
			{	
				$modframework->trigger_hook('admin_classic_ads_activate_deactivate');
				
				$sql = '';
				if($action == "activate")
					$sql = "UPDATE pm_ads SET active='1' WHERE id = '".$id."' LIMIT 1";
				else
					$sql = "UPDATE pm_ads SET active='0' WHERE id = '".$id."' LIMIT 1";
				
				$query = mysql_query($sql);
				if ( ! $query )
				{
					echo pm_alert_error('A problem was encountered while activating/deactivating this ad zone.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo ($action == "activate") ? pm_alert_success('The ad zone is now active.') : pm_alert_success('The ad zone was deactivated.');
				}
			}
		}
	?>


<div class="tablename">
<div class="qsFilter move-right pull-right">
<?php if ($action != 'addnew' && $action != 'edit') : ?>
<a href="#addNew" class="btn btn-success btn-strong" data-toggle="modal">Create new ad zone</a>
<?php endif; ?>
</div><!-- .qsFilter -->
</div>
<br />
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead> 
  <tr>
	<th>Name</th>
	<th align="center" style="text-align:center" width="10%">TPL Code</th>
	<th align="center" style="text-align:center" width="15%">Status</th>
	<th align="center" style="text-align:center; width: 120px;">Action</th>
  </tr>	
 </thead>
 <tbody>
  <?php
	 
	// display all ads
	$query = mysql_query("SELECT * FROM pm_ads ORDER BY id DESC");
	$i = 0;
	while($row = mysql_fetch_assoc($query))
	{	
		$clean_title = str_replace(array('"', "'"), array('', "\'"), $row['position']);
		$row_class = ($i++ % 2) ? 'table_row1' : 'table_row2';
		
		?>
		<tr class="<?php echo $row_class;?>">
			<td>
				<strong><?php echo htmlspecialchars($row['position']); ?></strong> <br /><em><small><?php echo $row['description']; ?></small></em>
			</td>
			<td align="center" style="text-align:center">
				{$ad_<?php echo $row['id']; ?>}
			</td>
			<td align="center" style="text-align:center">
				<small><?php if ($row['active'] == 1) :?>
					<a href="ad_manager.php?act=deactivate&id=<?php echo $row['id']; ?>" class="label label-success label-clickable" rel="tooltip" title="Click to deactivate">Active</a>
				<?php else : ?>
					<a href="ad_manager.php?act=activate&id=<?php echo $row['id']; ?>" class="label label-clickable" rel="tooltip" title="Click to activate">Inactive</a></span>
				<?php endif; ?>
				</small>
			</td>
			<td align="center" class="table-col-action" style="text-align:center">
				<?php if ($row['active'] == 0) :?>
				 <a href="ad_manager.php?act=activate&id=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Activate Ad"><i class="icon-ok-sign"></i></a>
				<?php else : ?>
				 <a href="ad_manager.php?act=deactivate&id=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Deactivate Ad"><i class="icon-remove-sign"></i></a>
				<?php endif; ?>
				<a href="#" class="adzone_update_<?php echo  $row['id'] ; ?> btn btn-mini btn-link" title="Edit"><i class="icon-pencil"></i> </a> <a href="#" onClick="delete_ad('<?php echo  $clean_title ; ?>', 'ad_manager.php?act=delete&id=<?php echo $row['id']; ?>')" class="btn btn-mini btn-link" rel="tooltip" title="Delete"><i class="icon-remove" ></i> </a>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="margin:0;padding:0;">
				<div id="adzone_update_<?php echo  $row['id'] ; ?>" name="<?php echo  $row['id'] ; ?>">
					<div class="adzone_update_form" style="padding: 10px; margin: 10px;">
					<form name="adzone_update_<?php echo  $row['id'] ; ?>" method="post" action="ad_manager.php?act=edit&id=<?php echo $row['id']; ?>" enctype="application/x-www-form-urlencoded">
						<label>Name</label>
						<input type="text" name="position" value="<?php echo htmlspecialchars($row['position']); ?>" size="40" />
						
						<label>Description</label>
						<input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" size="40" />
						
						<label>HTML Code</label>
						<textarea name="code" cols="60" rows="4" style="width: 95%;" ><?php echo $row['code']; ?></textarea>
						
						<label>Enable Statistics</label>
						<label><input type="radio" name="disable_stats" value="0" <?php echo ($row['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
						<label><input type="radio" name="disable_stats" value="1" <?php echo ($row['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
						<?php $modframework->trigger_hook('admin_classic_ads_editoptions');?>
						<input type="hidden" name="active" value="<?php echo $row['active']; ?>" />
						<input type="submit" name="Submit" value="Save" class="btn btn-mini btn-success border-radius0" />
						<a href="#" id="adzone_update_<?php echo  $row['id'] ; ?>" class="btn-mini">Cancel</a>
					</form>
					</div>
				</div>
			</td>
		</tr>
		<?php
	}
	mysql_free_result($query);
	break;
}

?>
 </tbody>
</table>
</div>	<!-- end div id="content" -->
<?php
include('footer.php');
?>