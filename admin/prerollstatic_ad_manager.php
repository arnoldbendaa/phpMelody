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
$load_chzn_drop = 1;
$_page_title = 'Pre-roll static ads manager';
include('header.php');

$action = $_GET['act'];

$total_ads = count_entries('pm_preroll_ads', '', '');
 
$sources = a_fetch_video_sources('source_name');
load_categories();

?>

<!-- create new ad form modal -->
<div class="modal hide fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Create new pre-roll ad</h3>
	</div>
	<form name="ad_manager" method="post" action="prerollstatic_ad_manager.php?act=addnew">
		<div class="modal-body">
			<label>Name</label>
			<input type="text" name="name" value="" size="40" />
			
			<label>Duration</label>
			<div class="input-append">
			<input type="text" name="duration" id="appendedInput" value="30" autocomplete="off" size="25" class="input-mini" />
			<span class="add-on">seconds</span>
			</div>
			
			<label>HTML Code for your Ad</label>
			<textarea name="code" cols="60" rows="7" class="span5"></textarea>
			
			<label>Display to</label>
			<select name="user_group">
				<option value="0">All visitors</option>
				<option value="1">Logged-in users only</option>
				<option value="2">Visitors only</option>
			</select>
			<hr />
			<label>Allow viewers to 'Skip' this Ad</label>
			<label><input type="radio" name="skip_delay_radio" value="1"> Yes</label> <label><input type="radio" name="skip_delay_radio" value="0" checked="checked"> No</label>
			<hr />
			<span id="skip_delay_seconds_new_span">
				<label>Display 'Skip' option after</label>
				<div class="input-append">
				<input type="text" name="skip_delay_seconds" id="appendedInput" value="5" class="input-mini" />
				<span class="add-on">seconds</span>
				</div>
			<hr />
			</span> 
			<label>Don't display on videos in</label>
			<?php 
				$categories_dropdown_options = array(
												'attr_name' => 'ignore_category[]',
												'attr_id' => 'main_select_category',
												'attr_class' => 'category_dropdown span3',
												'select_all_option' => false,
												'spacer' => '&mdash;',
												'selected' => false,
												'other_attr' => 'multiple="multiple" data-placeholder="Select categories..."'
												);
				echo categories_dropdown($categories_dropdown_options);
			?>
			<hr />			
			<label>Don't display on videos from</label>
			<select name="ignore_source[]" data-placeholder="Select sources..." id="main_select_sources" class="source_dropdown span3" multiple="multiple" style="width:284px">
			<?php
				foreach ($sources as $id => $src)
				{
					$option = '';
					if (is_int($id) && $id > 1 && $id != 44 && $id != 43): ?>
						<option value="<?php echo $src['source_id'];?>"><?php echo ucfirst($src['source_name']);?></option>
					<?php 
					endif;
				}
			?>
			</select>
			<hr />
			<label>Enable Statistics</label>
			<label><input type="radio" name="disable_stats" value="0" checked="checked"> Yes</label>
			<label><input type="radio" name="disable_stats" value="1"> No</label>
			<?php $modframework->trigger_hook('admin_preroll_static_add_options');?>
		</div>
		<div class="modal-footer">
		<input type="hidden" name="status" value="1" />
		<button class="btn btn-link btn-strong" data-dismiss="modal" aria-hidden="true">Cancel</button>
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
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane fade in active" id="help-overview">
			<p>Pre-roll static ads are ads which you can define to appear before the video player is loaded. These ads should work with any kind of video.</p>
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
				<div class="floatL"><strong class="blue"><?php echo pm_number_format($total_ads); ?></strong><span>ad<?php echo ($total_ads) > 1 ? 's' : '';?></span></div>
				<div class="blueImg"><img src="img/ico-ads-new.png" width="19" height="18" alt="" /></div>
			</li>
		</ul><!-- .pageControls -->
	</div>
	<h2>Pre-roll Static Ads Manager <a class="label opac5" href="#addNew" onclick="location.href='#addNew';" data-toggle="modal">+ add new</a></h2>

<?php


if ($action != '' && $action != 'addnew')
{
	$id = (int) $_GET['id'];
			
	if ( ! $id)
	{
		$action = '';
	}
}

switch($action)
{
	case 'addnew':
		if ($_GET['act'] == 'addnew')
		{
			if (isset($_POST['Submit']))
			{
				$modframework->trigger_hook('admin_preroll_static_add_before');
				$result = create_preroll_ad($_POST);
				$modframework->trigger_hook('admin_preroll_static_add_after');
				if ( ! $result)
				{
					echo pm_alert_error('There was a problem while inserting the new ad in your database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo pm_alert_success('Your ad has been created. ');
				}
			}
		}
	break;
	
	case 'edit':
	
		if ($id)
		{
			if (isset($_POST['Submit']))
			{
				$modframework->trigger_hook('admin_preroll_static_edit_before');
				$result = update_preroll_ad($id, $_POST);
				$modframework->trigger_hook('admin_preroll_static_edit_after');
				if ( ! $result)
				{
					echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					echo pm_alert_success('The ad was updated.');
				}
					
			}
		}
	
	break;
	
	case 'activate':
	case 'deactivate':
		
		if ($id)
		{	
			$modframework->trigger_hook('admin_preroll_static_activate_deactivate');
			$sql = "UPDATE pm_preroll_ads 
					SET ";
			$sql .= ($action == "activate") ? " status='1' " : " status='0' ";
			$sql .= " WHERE id = '$id' ";
							
			if ( ! $result = mysql_query($sql))
			{
				echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. mysql_error());
			}
			else
			{
				if ($action == "activate")
				{
					echo pm_alert_success('The ad is now active.');
				}
				else
				{
					echo pm_alert_success('The ad was deactivated.');
				}
			}
		}
		
	break;
	
	case 'delete':
	
		if ($id)
		{
			$ad = get_preroll_ad($id);

			if ( ! $ad)
			{
				$sql_err = mysql_error();
				
				if (strlen($sql_err) > 0)
				{
					echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. $sql_err);
				}
				else
				{
					echo pm_alert_error('Could not delete this ad because it wasn\'t found in your database.');
				}

				break;
			}

			if ($_GET['key'] != md5($ad['name']))
			{
				echo pm_alert_error('Invalid key provided. Please reload the page and try again.');
				break;
			}
			$modframework->trigger_hook('admin_preroll_static_delete');
			$result = delete_preroll_ad($id);
			if ( ! $result)
			{
				echo pm_alert_error('A problem was encountered while updating this ad.<br />MySQL returned: '. mysql_error());
			}
			else
			{
				$sql = "DELETE FROM pm_ads_log 
						WHERE ad_id = $id 
						  AND ad_type = ". _AD_TYPE_PREROLL;
				@mysql_query($sql);
				
				echo pm_alert_success('The ad was deleted.');
			}
		}
		
	break;
}

if ($action != '')
{
	update_config('total_preroll_ads', count_entries('pm_preroll_ads', 'status', '1'));
}
?>


<div class="tablename">
<div class="qsFilter move-right pull-right">
<a href="#addNew" class="btn btn-success btn-strong" data-toggle="modal">Create new ad</a>
</div><!-- .qsFilter -->
</div>
<br />
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead> 
  <tr>
	<th>Name</th>
	<th align="center" style="text-align:center" width="10%">Duration</th>
	<th align="center" style="text-align:center" width="10%">Display to</th>
	<th align="center" style="text-align:center" width="15%">Status</th>
	<th align="center" style="text-align:center; width: 120px;">Action</th>
  </tr>	
 </thead>
 <tbody>
	<?php
	
	$ads = array();
	
	if ($total_ads > 0 || ($total_ads == 0 && $action == 'addnew'))
	{
		$sql = "SELECT * FROM pm_preroll_ads 
				ORDER BY id DESC";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result))
		{
			$options = array();
			if (strlen($row['options']) > 0)
			{
				$options = (array) unserialize($row['options']);
			}

			$ads[] = array_merge($row, $options);
			unset($options);
		}
		mysql_free_result($result);
	
	}

	if (count($ads) > 0) : 
		foreach ($ads as $k => $ad) : 
	?>
		<tr>
			<td>
				<strong><?php echo $ad['name']; ?></strong>
			</td>
			<td align="center" style="text-align:center"><?php echo sec2min($ad['duration']);?></td>
			<td align="center" style="text-align:center">
				<?php 
				switch($ad['user_group'])
				{
					case 0:
						echo 'All visitors'; 
					break;
					
					case 1:
						echo 'Logged-in users only';
					break;
					
					case 2:
						echo 'Visitors only';
					break;
				}
				?>
			</td>
			<td align="center" style="text-align:center"><?php echo ($ad['status'] == 1) ? '<a href="prerollstatic_ad_manager.php?act=deactivate&id='. $ad['id'] .'" class="label label-success label-clickable" rel="tooltip" title="Click to deactivate">Active</a>' : '<a href="prerollstatic_ad_manager.php?act=activate&id='. $ad['id'] .'" class="label label-clickable" rel="tooltip" title="Click to activate">Inactive</a>';?></td>
			<td align="center" class="table-col-action" style="text-align:center;">
				<?php if ($ad['status'] == 0) : ?>
					<a href="prerollstatic_ad_manager.php?act=activate&id=<?php echo $ad['id'];?>" class="btn btn-mini btn-link" rel="tooltip" title="Activate Ad"><i class="icon-ok-sign"></i></a>
				<?php else : ?>
					<a href="prerollstatic_ad_manager.php?act=deactivate&id=<?php echo $ad['id'];?>" class="btn btn-mini btn-link" rel="tooltip" title="Deactivate Ad"><i class="icon-remove-sign"></i></a>
				<?php endif; ?>
				<a href="#modal_edit_ad_<?php echo $ad['id'];?>" onclick="location.href='#modal_edit_ad_<?php echo $ad['id'];?>';" data-toggle="modal" class="btn btn-mini btn-link" title="Edit"><i class="icon-pencil"></i></a> <a href="#" onClick="delete_ad('<?php echo str_replace(array('"', "'"), array('', "\'"), $ad['name']);?>', 'prerollstatic_ad_manager.php?act=delete&id=<?php echo $ad['id'];?>&key=<?php echo md5($ad['name']);?>')" class="btn btn-mini btn-link" rel="tooltip" title="Delete"><i class="icon-remove" ></i> </a>
			</td>
		</tr>
	<?php 
		endforeach;
	else : ?>
	<tr>
		<td colspan="6">
			No pre-roll ads have been defined.
		</td>
	</tr>
	<?php endif;?>
 </tbody>
</table>

<!-- edit modals -->
<?php if (count($ads) > 0) : ?>
<?php foreach ($ads as $k => $ad) : ?>
<div class="modal hide fade" id="modal_edit_ad_<?php echo $ad['id'];?>" tabindex="-1" role="dialog" aria-labelledby="modal_edit_ad_<?php echo $ad['id'];?>" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="modal_edit_ad_<?php echo $ad['id'];?>">Edit Ad</h3>
	</div>
	<form name="preroll_update_<?php echo $ad['id'];?>" method="post" action="prerollstatic_ad_manager.php?act=edit&id=<?php echo $ad['id'];?>">
		<div class="modal-body">
			<div id="preroll_update_<?php echo $ad['id'];?>">
					<div class="preroll_update_form" style="padding: 10px; margin: 10px;">

							<label>Name</label>
							<input type="text" name="name" value="<?php echo htmlspecialchars($ad['name']);?>" size="40" />
							
							<label>Duration</label>
							<div class="input-append">			
							<input type="text" name="duration" id="appendedInput" value="<?php echo $ad['duration'];?>" autocomplete="off" size="25" class="input-mini" />
							<span class="add-on">seconds</span>
							</div>
							<label>HTML Code for your Ad</label>
							<textarea name="code" cols="60" rows="7" class="span5"><?php echo $ad['code'];?></textarea>
							
							<label>Display to</label>
							<select name="user_group">
								<option value="0" <?php echo ($ad['user_group'] == 0) ? 'selected="selected"' : '';?>>All visitors</option>
								<option value="1" <?php echo ($ad['user_group'] == 1) ? 'selected="selected"' : '';?>>Logged-in users only</option>
								<option value="2" <?php echo ($ad['user_group'] == 2) ? 'selected="selected"' : '';?>>Visitors only</option>
							</select>
							<hr />
							<label>Allow viewers to 'Skip' this Ad</label>
							<label><input type="radio" name="skip_delay_radio" value="1" <?php echo ($ad['skip'] == 1) ? 'checked="checked"' : '';?> child-input="skip_delay_seconds_span_<?php echo $ad['id'];?>"> Yes</label> <label><input type="radio" name="skip_delay_radio" value="0" <?php echo ( ! $ad['skip']) ? 'checked="checked"' : '';?> child-input="skip_delay_seconds_span_<?php echo $ad['id'];?>"> No</label>
							
							<span id="skip_delay_seconds_span_<?php echo $ad['id'];?>" <?php echo ($ad['skip'] == 0) ? 'class="hide"' : '';?>>
								<label>Display 'Skip' option after</label>
								<div class="input-append">
								<input type="text" name="skip_delay_seconds" id="appendedInput" value="<?php echo (int) $ad['skip_delay_seconds'];?>" class="input-mini" />
								<span class="add-on">seconds</span>
								</div>
							<hr />
							</span>

							<label>Don't display on videos in</label>
							<?php 
								$categories_dropdown_options = array(
																'attr_name' => 'ignore_category[]',
																'attr_id' => 'main_select_category_'. $ad['id'],
																'attr_class' => 'category_dropdown span3',
																'select_all_option' => false,
																'spacer' => '&mdash;',
																'selected' => (array) $ad['ignore_category'],
																'other_attr' => 'multiple="multiple" data-placeholder="Select categories..."'
																);
								echo categories_dropdown($categories_dropdown_options);
							?>
							<hr />
							<label>Don't display on videos from</label>
							<select name="ignore_source[]" data-placeholder="Select sources..." id="main_select_sources_<?php echo $ad['id'];?>" class="source_dropdown span3" multiple="multiple" style="width:284px">
							<?php

								foreach ($sources as $id => $src)
								{
									$selected = (is_array($ad['ignore_source']) && in_array($id, $ad['ignore_source'])) ? 'selected="selected"' : '';
									$option = '';
									if (is_int($id) && $id > 1 && $id != 44 && $id != 43): ?>
										<option value="<?php echo $src['source_id'];?>" <?php echo $selected;?>><?php echo ucfirst($src['source_name']);?></option>
									<?php 
									endif;
								}
							?>
							</select>
							<hr />
							<label>Enable Statistics</label>
							<label><input type="radio" name="disable_stats" value="0" <?php echo ($ad['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
							<label><input type="radio" name="disable_stats" value="1" <?php echo ($ad['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
						<?php $modframework->trigger_hook('admin_preroll_static_edit_options');?>
					</div>
				</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="status" value="1" />
			<button class="btn btn-link btn-strong" data-dismiss="modal" aria-hidden="true">Cancel</button>
			<button type="submit" name="Submit" value="Submit" class="btn btn-success btn-strong" />Save</button>
		</div>
	</form>
</div>
<?php endforeach; ?>
<?php endif; ?>

</div>	<!-- end div id="content" -->

<script type="text/javascript">

$(document).ready(function(){
	$('.category_dropdown').addClass("chzn-select");
	$('.source_dropdown').addClass("chzn-select");
	
	$(".chzn-select").chosen({width: "95%"});
	$(".chzn-select-deselect").chosen({allow_single_deselect:true});
	
	$('#skip_delay_seconds_new_span').hide();
	
	$('input[name="skip_delay_radio"]').change(function(){
		var selector = $(this).attr('child-input');
		if ($(this).val() == '1') {
			if (selector) {
				$('#'+ selector).fadeIn();
			} else {
				$('#skip_delay_seconds_new_span').fadeIn();
			}
		} else {
			if (selector) {
				$('#'+ selector).fadeOut();
			} else {
				$('#skip_delay_seconds_new_span').fadeOut();
			}
		}
	})
});
</script>
<?php
include('footer.php');
?>