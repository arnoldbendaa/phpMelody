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
$load_flot = 1;
$load_datepicker = 1;
$_page_title = 'Ad reports';
include('header.php');

$time_start = $time_end = $mysql_date_start = $mysql_date_end = false;
$errors = array();
$input_date_regex = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';

$current_ads = array(_AD_TYPE_CLASSIC => array(),
					 _AD_TYPE_PREROLL => array(),
					 _AD_TYPE_VIDEO => array()
					);

// get Classic HTML/Banner Ads
$sql = "SELECT id, position FROM pm_ads";
if ($result = mysql_query($sql))
{
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$current_ads[_AD_TYPE_CLASSIC][$row['id']] = $row['position'];
		}
		mysql_free_result($result);
	}
}

// get Pre-roll Static Ads 
$sql = "SELECT id, name FROM pm_preroll_ads";
if ($result = mysql_query($sql))
{
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$current_ads[_AD_TYPE_PREROLL][$row['id']] = $row['name'];
		}
		mysql_free_result($result);
	}
}
// get Video Ads
$sql = "SELECT id, name FROM pm_videoads";
if ($result = mysql_query($sql))
{
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_assoc($result))
		{
			$current_ads[_AD_TYPE_VIDEO][$row['id']] = $row['name'];
		}
		mysql_free_result($result);
	}
}

if ($_GET['date_start'] != '' && $_GET['date_end'] != '')
{
	if ( ! $s = preg_match($input_date_regex, $_GET['date_start'], $parts_start))
	{
		$errors[] = 'Invalid start date. Accepted format is \'MM/DD/YYYY\', where MM represents the Month (e.g. 04 for April), DD represents the day and YYYY represents the year.';
	}
	
	if ( ! $e = preg_match($input_date_regex, $_GET['date_end'], $parts_end))
	{
		$errors[] = 'Invalid end date. Accepted format is \'MM/DD/YYYY\', where MM represents the Month (e.g. 04 for April), DD represents the day and YYYY represents the year.';
	}
	
	if (count($errors) == 0)
	{
		$mon_s = (int) $parts_start[1];
		$day_s = (int) $parts_start[2];
		$year_s = (int) $parts_start[3];
		
		$mon_e = (int) $parts_end[1];
		$day_e = (int) $parts_end[2];
		$year_e = (int) $parts_end[3];
		
		$day_s = ($day_s > 31) ? 31 : $day_s;
		$day_e = ($day_e > 31) ? 31 : $day_e;
		
		$mon_s = ($mon_s > 12) ? 12 : $mon_s;
		$mon_e = ($mon_e > 12) ? 12 : $mon_e;
		
		$time_start = mktime(0, 0, 0, $mon_s, $day_s, $year_s);
		$time_end = mktime(23, 59, 59, $mon_e, $day_e, $year_e);
		
		if ($time_end < $time_start)
		{
			$errors[] = 'The end date cannot be earlier than the start date.';
		}
	}
}
else
{
	$current_date = getdate();
	
	$time_start = mktime(0, 0, 0, $current_date['mon'], $current_date['mday'] - 30, $current_date['year']);
	$time_end = mktime(23, 59, 59, $current_date['mon'], $current_date['mday'], $current_date['year']);
}

if (count($errors) > 0) 
{
	$current_date = getdate();
	
	$time_start = mktime(0, 0, 0, $current_date['mon'], $current_date['mday'] - 30, $current_date['year']);
	$time_end = mktime(23, 59, 59, $current_date['mon'], $current_date['mday'], $current_date['year']);
}

$mysql_date_start = date('Y-m-d', $time_start);
$mysql_date_end = date('Y-m-d', $time_end);

$graph_data = array();

$selected_ad_id = $selected_ad_type = 0;
$i = 0;

if ($time_start <= $time_end)
{
	$graph_max_y = 0;
	$tmp_time_start = $time_start;
	
	$tmp_date = getdate($tmp_time_start);
	
	$graph_data[date('Y-m-d', $tmp_time_start)] = array('label' => substr($tmp_date['month'], 0, 3) .' '. $tmp_date['mday'],
														'date_full' => $tmp_date['weekday'].', '. $tmp_date['month'] .' '. $tmp_date['mday'] .', '. $tmp_date['year'], 
														'impressions' => 0,
														'clicks' => 0,
														'skips' => 0);
	
	if ($time_end - $time_start > 86399)
	{
		while ($tmp_time_start < $time_end)
		{
			$tmp_time_start += 86400;
	
			$tmp_date = getdate($tmp_time_start);
	
			$graph_data[date('Y-m-d', $tmp_time_start)] = array('label' => substr($tmp_date['month'], 0, 3) .' '. $tmp_date['mday'],
																'date_full' => $tmp_date['weekday'].', '. $tmp_date['month'] .' '. $tmp_date['mday'] .', '. $tmp_date['year'],
																'impressions' => 0,
																'clicks' => 0,
																'skips' => 0);
		}
	}

	$sql_where = "	date >= '$mysql_date_start' 
		  	  	  AND date <= '$mysql_date_end' ";

	switch ($_GET['selected-ad'])
	{
		case 'classic-all':
			
			$sql_where .= ' AND ad_type = '. _AD_TYPE_CLASSIC; 
			$selected_ad_type = _AD_TYPE_CLASSIC;
				
		break;
		
		case 'preroll-all':

			$sql_where .= ' AND ad_type = '. _AD_TYPE_PREROLL;
			$selected_ad_type = _AD_TYPE_PREROLL;
			
		break;
		
		case 'video-all':

			$sql_where .= ' AND ad_type = '. _AD_TYPE_VIDEO;
			$selected_ad_type = _AD_TYPE_VIDEO;

		break;
		
		default:

			$parts = explode('-', $_GET['selected-ad']);
			$selected_ad_id = (int) $parts[0];
			$selected_ad_type = (int) $parts[1];
			
			if ($selected_ad_id && $selected_ad_type)
			{
				$sql_where .= ' AND ad_id = '. $selected_ad_id .' AND ad_type = '. $selected_ad_type;
			}

		break;
	}

	$sql = "SELECT date, SUM(impressions) as i, SUM(clicks) as c, SUM(skips) as s 
			FROM pm_ads_log 
			WHERE $sql_where 
			GROUP BY date
			ORDER BY date ASC";
	if ($result = mysql_query($sql))
	{
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$row['i'] = (int) $row['i'];
				$row['c'] = (int) $row['c'];
				$row['s'] = (int) $row['s'];
				
				$graph_max_y = ($row['c'] > $graph_max_y) ? $row['c'] : $graph_max_y;
				$graph_max_y = ($row['s'] > $graph_max_y) ? $row['s'] : $graph_max_y;
				$graph_max_y = ($row['i'] > $graph_max_y) ? $row['i'] : $graph_max_y;
				
				$graph_data[ $row['date'] ]['impressions'] = $row['i'];
				$graph_data[ $row['date'] ]['clicks'] = $row['c'];
				$graph_data[ $row['date'] ]['skips'] = $row['s'];
				
			}
			mysql_free_result($result);
		}
	}
	else
	{
		$errors[] = 'Could not retrieve data from the database.<br /><strong>MySQL returned:</strong> '. mysql_error();
	}
}

$graph_data_count = count($graph_data);
$graph_max_x = ($graph_data_count > 1) ? $graph_data_count - 2 : $graph_data_count - 1;
$graph_max_y = ($graph_max_y <= 5) ? (int) $graph_max_y + 1 : (int) round($graph_max_y * 110 / 100, 0);

?>
<div id="adminPrimary">
	<div class="content">
	<h2>Ad Reports</h2>
	
		<?php 
		if (count($errors) > 0)
		{
			echo pm_alert_error($errors);
		}
		?>
		
		<div class="enclosed enclosed-quick-add shadow-div border-radius2">
			<h3>View Statistics</h3>
			<div class="enclosed-body">
			<form class="form-horizontal" name="graph-input-data" method="get" action="ad-report.php">
			  	<strong>From</strong>: <input type="text" id="datepicker_start" name="date_start" value="<?php echo date('m/d/Y', $time_start); ?>" class="datepicker input-small" data-date-format="mm/dd/yyyy" autocomplete="off">
				<strong>Until</strong>:	<input type="text" id="datepicker_end" name="date_end" value="<?php echo date('m/d/Y', $time_end); ?>" class="datepicker input-small" data-date-format="mm/dd/yyyy" autocomplete="off">
				<strong>Ad</strong>:	<select name="selected-ad">
					<option value="all">All</option>
					<optgroup label="Classic Ads">
						<option value="classic-all" <?php echo ( ! $selected_ad_id && $selected_ad_type == _AD_TYPE_CLASSIC) ? 'selected="selected"' : ''; ?>>All</option>
						<?php if (count($current_ads[_AD_TYPE_CLASSIC])) :
							  foreach ($current_ads[_AD_TYPE_CLASSIC] as $id => $name) : ?>
								<option value="<?php echo $id .'-'. _AD_TYPE_CLASSIC; ?>" <?php echo ($selected_ad_id == $id && $selected_ad_type == _AD_TYPE_CLASSIC) ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
						<?php endforeach; 
							endif;?>
					</optgroup>
					<optgroup label="Pre-roll Static Ads">
						<option value="preroll-all" <?php echo ( ! $selected_ad_id && $selected_ad_type == _AD_TYPE_PREROLL) ? 'selected="selected"' : ''; ?>>All</option>
						<?php if (count($current_ads[_AD_TYPE_PREROLL])) :
							  foreach ($current_ads[_AD_TYPE_PREROLL] as $id => $name) : ?>
								<option value="<?php echo $id .'-'. _AD_TYPE_PREROLL; ?>" <?php echo ($selected_ad_id == $id && $selected_ad_type == _AD_TYPE_PREROLL) ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
						<?php endforeach; 
							endif;?>
					</optgroup>
					<optgroup label="Pre-roll Video Ads">
						<option value="video-all" <?php echo ( ! $selected_ad_id && $selected_ad_type == _AD_TYPE_VIDEO) ? 'selected="selected"' : ''; ?>>All</option>
						<?php if (count($current_ads[_AD_TYPE_VIDEO])) :
							  foreach ($current_ads[_AD_TYPE_VIDEO] as $id => $name) : ?>
								<option value="<?php echo $id .'-'. _AD_TYPE_VIDEO; ?>" <?php echo ($selected_ad_id == $id && $selected_ad_type == _AD_TYPE_VIDEO) ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
						<?php endforeach; 
							endif;?>
					</optgroup>
				</select>
				<input type="submit" class="btn btn-blue" value="Show" name="submit" />
			</form>
			</div>
		</div>
		<hr />	
		
		<div class="report-container">
			<div id="report_plot_area" class="div-report-plot-area"></div>
		</div>
		<hr />
		<?php if ($graph_data_count && count($errors) == 0) : ?>
		<h4><?php echo date('D, F j, Y', $time_start) .' &mdash; '. date('D, F j, Y', $time_end); ?></h4>
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter table-statistics">
			<thead>
				<th width="10%">Date</th>
				<th>Impressions</th>
				<?php if ($selected_ad_type == 0 || $selected_ad_type == _AD_TYPE_VIDEO) : ?>
				<th>Clicks</th>
				<th>CTR</th>
				<?php endif; ?>
				<?php if ($selected_ad_type == 0 || $selected_ad_type == _AD_TYPE_PREROLL) : ?>
				<th>Skips</th>
				<th>Skips Ratio</th>
				<?php endif; ?>
			</thead>
			<tbody>
			<?php foreach ($graph_data as $date => $data_arr) : ?>
				<tr>
					<td><?php echo $date;?></td>
					<td><?php echo pm_number_format($data_arr['impressions']); ?></td>
					<?php if ($selected_ad_type == 0 || $selected_ad_type == _AD_TYPE_VIDEO) : ?>
					<td><?php echo pm_number_format($data_arr['clicks']); ?></td>
					<td><?php echo ($data_arr['impressions'] == 0) ? 0 : round(($data_arr['clicks'] * 100 / $data_arr['impressions']), 2); ?>%</td>
					<?php endif; ?>
					<?php if ($selected_ad_type == 0 || $selected_ad_type == _AD_TYPE_PREROLL) : ?>
					<td><?php echo pm_number_format($data_arr['skips']); ?></td>
					<td><?php echo ($data_arr['impressions'] == 0) ? 0 : round(($data_arr['skips'] * 100 / $data_arr['impressions']), 2); ?>%</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>

    </div><!-- .content -->
</div><!-- .primary -->
<script type="text/javascript">

	$(document).ready(function () {

		// datepicker start		
		<?php if ($time_start) : ?>
			var now = new Date(<?php echo $time_start;?>);
		<?php else : ?>
			var nowTemp = new Date();
			var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
		<?php endif; ?>

		var dp_start = $('#datepicker_start').datepicker({
		  onRender: function(date) {
		    return date.valueOf() < now.valueOf() ? 'disabled' : '';
		  },
		  //autoclose: true
		}).on('changeDate', function(ev) {
		  if (ev.date.valueOf() > dp_end.date.valueOf()) {
		    var newDate = new Date(ev.date)
		    newDate.setDate(newDate.getDate() + 1);
		    dp_end.setValue(newDate);
		  }
		  dp_start.hide();
		  $('#datepicker_end')[0].focus();
		}).data('datepicker');

		var dp_end = $('#datepicker_end').datepicker({
		  onRender: function(date) {
		  	return date.valueOf() < dp_start.date.valueOf() ? 'disabled' : '';
		  },
		  //autoclose: true
		}).on('changeDate', function(ev) {
			dp_end.hide();
		}).data('datepicker'); 
		
		$('form[name="graph-input-data"]').submit(function(event){
			
			var date_start = Date.parse($('input[name="date_start"]').val());
			var date_end = Date.parse($('input[name="date_end"]').val());
			if (date_end < date_start) {
				alert('The end date cannot be earlier than the start date.');
				return false;
			} 
			
		});
		// datepicker end
		
	});

	// flot graph start
	$(function() {
		
		var graph_xaxis_ticks = new Array, date_full = new Array;

		<?php  
			$i = 0;
			foreach ($graph_data as $date => $data_arr) : ?>
				graph_xaxis_ticks[<?php echo $i;?>] = "<?php echo $data_arr['label'];?>";
				date_full[<?php echo $i;?>] = "<?php echo $data_arr['date_full'];?>";
		<?php $i++;
		endforeach; ?>
		
		//  [ [x1, y1] ]	
		var data_skips = [ <?php 
								$i = 0;
								foreach ($graph_data as $date => $data_arr) :
									echo '['. $i++ .', '. $data_arr['skips'] .'],';
								endforeach; 
							?>
							],
			data_clicks = [<?php 
								$i = 0;
								
								foreach ($graph_data as $date => $data_arr) :
									echo '['. $i++ .', '. $data_arr['clicks'] .'],';
								endforeach; 
							?>
						   ],
			data_imp 	= [ <?php 
								$i = 0;
								foreach ($graph_data as $date => $data_arr) :
									echo '['. $i++ .', '. $data_arr['impressions'] .'],';
								endforeach; 
							?>
							];

		var plot = $.plot("#report_plot_area", [
												{ data: data_imp, 
												  label: "Impressions",
												  color: "#009f00"
												
												},
												<?php if ($selected_ad_type == 0 || $selected_ad_type == _AD_TYPE_VIDEO) : ?>
												{ data: data_clicks, 
												  label: "Clicks",
												  color: "#0080ff"
												},
												<?php endif; ?>
												<?php if ($selected_ad_type == 0 || $selected_ad_type == _AD_TYPE_PREROLL) : ?>
												{
													data: data_skips,
													label: "Skips",
													color: "#c60000"
												},
												<?php endif; ?>
												], {
													//colors: ["#d18b2c", "#dba255", "#919733"],
													series: {
														shadowSize: 0,
														lines: {
															show: true,
															fill: true,
														},
														points: {
															show: true,
															//radius: 2
														}
													},
													grid: {
														hoverable: true,
														clickable: true,
														borderWidth: 1,
														borderColor: "#CCCCCC",
														mouseActiveRadius: 20,
														autoHighlight: true,
														//backgroundColor: { colors: ["#FFF", "#CCC"] }
														
													},
													yaxis: {
														/*show: false,*/
														min: 0,
														max: <?php echo $graph_max_y;?>,
														show: true,
														color: "#CCCCCC",
													},
													xaxis: {
														/*show: false,*/
														show: true,
														color: "#CCCCCC",
														<?php if ($graph_max_x == 0) : ?>
														min: -1,
														max: 1,
														<?php else : ?>
														min: 0,
														max: <?php echo $graph_max_x;?>,
														<?php endif; ?>
														tickFormatter: function formatter(val, axis) {
																			<?php if ($graph_max_x == 0) : ?>
																				return val == 0 ? graph_xaxis_ticks[val.toFixed(0)] : '';
																			<?php else : ?>	
																				return graph_xaxis_ticks[val.toFixed(0)];
																			<?php endif; ?>
																		},
													},
												});

		$("<div id='graph_tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #ccc",
			padding: "8px",
			"background-color": "#fff",
			opacity: 1
		}).appendTo("body");
		
		$("#report_plot_area").bind("plothover", function (event, pos, item) {
			if (item) {
				$("#graph_tooltip").html('<strong>' + date_full[item.dataIndex] + '</strong><br />' + item.series.label + ': <strong>' + item.datapoint[1] + '</strong>').css({
					top: item.pageY - 70,
					left: item.pageX - 40,
				}).show();
			} else {
				$("#graph_tooltip").hide();
			}
		});
	});
	// flot graph end
</script>
<?php
include('footer.php');