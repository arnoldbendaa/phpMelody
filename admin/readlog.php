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

$showm = '7';
$_page_title = 'System log';
include('header.php');

$errors = array();
$page	= (int) $_GET['page'];
$page 	= ($page == 0) ? 1 : $page;
$limit 	= 30;
$from = $page * $limit - ($limit);

$config['unread_system_messages'] = (int) $config['unread_system_messages'];

switch ($_GET['do'])
{
	case 'delete-ok':
		
		$info_msg = 'The log has been cleared.';
		
	break;
	
	case 'marked-read':
		
		$info_msg = 'All messages marked as read.';

	break;
}

$data = $mark_read = array();

$sql = "SELECT * 
		FROM pm_log 
		ORDER BY added DESC
		LIMIT $from, $limit";

if ($result = mysql_query($sql))
{
	while ($row = mysql_fetch_assoc($result))
	{
		$data[] = $row;
		
		if ($row['msg_type'] == '1')
		{
			$mark_read[] = (int) $row['id'];
		}
	}
	mysql_free_result($result);
}
else
{
	$errors[] = 'An error occurred while retrieving data.<br /><strong>MySQL reported:</strong> '. mysql_error();
}

$total_items = count_entries('pm_log', '', '');

$pagination = a_generate_smart_pagination($page, $total_items, $limit, 5, 'readlog.php', '');

?>
<div id="adminPrimary">
    <div class="content">
    <div class="entry-count">
        <ul class="pageControls">
            <li>
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_items); ?></strong><span><?php echo ($total_items == 0 || $total_items > 1) ? 'entries' : 'entry';?></span></div>
                <div class="blueImg"><img src="img/ico-settings-new.png" width="18" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
    <h2>System Log</h2>
    
	<?php 
	if ($info_msg != '')
	{
		echo pm_alert_success($info_msg);
	}
	 
	if (count($errors) > 0)
	{
		echo pm_alert_error($errors);
	}
	?>
	
	<div id="ajax_response_placeholder"></div>
	
    <div class="tablename">
    <h6></h6>
    <div class="qsFilter move-right">
    <div class="btn-group input-prepend">
		<?php if ($config['unread_system_messages'] > 0) : ?>
		<a href="#" id="mark_all_read" class="btn btn-small btn-strong">Mark all read</a>
		<?php endif; ?>
		<?php if ($total_items > 0) : ?>
		<a href="#" id="delete_all" class="btn btn-small btn-danger btn-strong">Delete all</a>
		<?php endif; ?>
    </div><!-- .btn-group -->
    </div><!-- .qsFilter -->
    </div>
    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
     <thead>
      <tr> 
        <th width="15%">Occurred on</th>
        <th width="10%">Area</th>
		<th>Details</th>
      </tr>
     </thead>
     <tbody>
     	<?php if ($pagination != '') : ?> 
		<tr class="tablePagination">
			<td colspan="3" class="tableFooter">
				<div class="pagination pull-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
		
        <?php if (count($data)) : ?>
		<?php foreach ($data as $k => $row) :?>
		<tr style="<?php echo ($row['msg_type'] == '1') ? 'font-weight:700;' : ''; ?>">
			<td><?php echo date('M d, Y h:i:s A', $row['added']); ?></td>
			<td><?php echo $row['area']; ?></td>
			<td><?php echo stripslashes($row['log_msg']); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php else : ?>
		<tr>
			<td colspan="3" style="text-align:center">Yay! No errors to report.</td>
		</tr>
		<?php endif; ?>
		
		<?php if ($pagination != '') : ?> 
		<tr class="tablePagination">
			<td colspan="3" class="tableFooter">
				<div class="pagination pull-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
     </tbody>	
    </table>
	
    </div><!-- .content -->
</div><!-- .primary -->

<?php echo csrfguard_form('_admin_readlog');?>

<script type="text/javascript">
$(document).ready(function(){

	$('#ajax_response_placeholder').hide();
	
	<?php if ($config['unread_system_messages'] > 0) : ?>
	$('#mark_all_read').click(function(){
		
		if (confirm('Are you sure you want to mark all messages as read?')) {

			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": 'readlog',
					"do": 'mark-all-read',
					"_pmnonce": $('input[name=_pmnonce]').val(),
					"_pmnonce_t": $('input[name=_pmnonce_t]').val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['success'] == false) {
						$('#ajax_response_placeholder').html(data['msg']).show();
					} else {
						window.location.href = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/readlog.php?page='. $page .'&do=marked-read'; ?>";
					}
				}
			});
		}

		return false;
	});
	<?php endif; ?>
	<?php if ($total_items > 0) : ?>
	$('#delete_all').click(function(){
		
		if (confirm('Are you sure you want to delete all messages?')) {
			
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": 'readlog',
					"do": 'delete-all',
					"_pmnonce": $('input[name=_pmnonce]').val(),
					"_pmnonce_t": $('input[name=_pmnonce_t]').val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['success'] == false) {
						$('#ajax_response_placeholder').html(data['msg']).show();
					} else {
						window.location.href = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/readlog.php?do=delete-ok'; ?>";
					}
				}
			});
		}

		return false;
	});
	<?php endif; ?>
});
</script>

<?php



// mark all these as read.
if ($total_read = count($mark_read))
{
	$sql = "UPDATE pm_log 
			SET msg_type = '0' 
			WHERE id IN (". implode(',', $mark_read) .')';
	if (mysql_query($sql))
	{
		$update = (int) $config['unread_system_messages'] - $total_read;
		$update = ($update < 0) ? 0 : $update;

		update_config('unread_system_messages', $update);
	}
}

include('footer.php');
