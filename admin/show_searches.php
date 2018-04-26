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
$_page_title = 'Search log';
include('header.php');

switch ($_GET['do'])
{
	case 'delete-ok':
		
		$info_msg = 'The search log has been cleared.';
		
	break;
}

$page	= (int) $_GET['page'];
$page 	= ($page == 0) ? 1 : $page;
$limit 	= 30;
$from = $page * $limit - ($limit);

$sql = "SELECT * 
		FROM pm_searches 
		ORDER BY hits DESC 
		LIMIT $from, $limit";

if ($result = mysql_query($sql))
{
	while ($row = mysql_fetch_assoc($result))
	{
		$data[] = $row;
	}
	mysql_free_result($result);
}
else
{
	$errors[] = 'An error occurred while retrieving data.<br /><strong>MySQL reported:</strong> '. mysql_error();
}

$total_items = count_entries('pm_searches', '', '');

$pagination = a_generate_smart_pagination($page, $total_items, $limit, 5, 'show_searches.php', '');

$rank = $from + 1;
?>
<div id="adminPrimary">
    <div class="content">
    <div class="entry-count">
        <ul class="pageControls">
            <li>
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_items); ?></strong><span>keyword<?php echo ($total_items == 0 || $total_items > 1) ? 's' : ''; ?></span></div>
                <div class="blueImg"><img src="img/ico-search-new.png" width="18" height="17" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>Search Log</h2>

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
		<?php if ($total_items > 0) : ?>
    	<a href="#" id="delete_all" class="btn btn-small btn-danger btn-strong">Clear log</a>
		<?php endif; ?>
    </div><!-- .btn-group -->
    </div><!-- .qsFilter -->
    </div>
    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
     <thead>
      <tr> 
        <th width="40">Rank</th>
        <th>Search keywords</th>
        <th width="10%">Hits</th>
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
		<?php foreach ($data as $k => $row) : ?>
		<tr>
			<td style="text-align:center;"><?php echo pm_number_format($rank++); ?>.</td>
			<td><?php echo stripslashes($row['string']); ?></td>
			<td style="text-align:center"><?php echo pm_number_format( (int) $row['hits']); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php else : ?>
		<tr>
			<td colspan="3" style="text-align:center">The search log is empty.</td>
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

<?php echo csrfguard_form('_admin_searchlog');?>

<script type="text/javascript">
$(document).ready(function(){

	$('#ajax_response_placeholder').hide();
	
	<?php if ($total_items > 0) : ?>
	$('#delete_all').click(function(){
		
		if (confirm('Are you sure you want to clear the entire search log?')) {

			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": 'searchlog',
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
						window.location.href = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/show_searches.php?do=delete-ok'; ?>";
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
include('footer.php');