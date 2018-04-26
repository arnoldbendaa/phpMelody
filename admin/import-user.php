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
$load_tagsinput = 1;
$load_ibutton = 1;
$load_prettypop = 1;
$load_import_js = 1;
$load_googlesuggests = 1;
$load_lazy_load = 1;

$_page_title = 'Import Videos from User';
include('header.php');
include_once(ABSPATH . 'include/cron_functions.php');

$action = trim($_GET['action']);
$page = (empty($_GET['page'])) ? 1 : (int) $_GET['page'];

$post_n_get = 0;
$post_n_get = count($_POST) + count($_GET);
$curl_error = '';
$sources = a_fetch_video_sources();

$subscription_id = (int) $_GET['sub_id'];

$data_source = 'youtube';

if (in_array($_COOKIE['aa_import_from'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo')))
{
	$data_source = $_COOKIE['aa_import_from'];
}

if ($_GET['data_source'] != '' || $_POST['data_source'] != '')
{
	$data_source = ($_GET['data_source'] != '') ? $_GET['data_source'] : $_POST['data_source'];
}

if ($_POST['username'] != '' && ! $subscription_id)
{
	$_POST['username'] = trim($_POST['username']);
	
	$sql = "SELECT sub_id, data
			FROM pm_import_subscriptions 
			WHERE sub_name = '". secure_sql($_POST['username']) ."'
			  AND sub_type = 'user'";
	if ( $result = @mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		$row['data'] = unserialize($row['data']);
		
		if ($row['data']['data_source'] == $data_source)
		{
			$subscription_id = (int) $row['sub_id'];
		}
		mysql_free_result($result);
		unset($row, $result);
	}
}

$cron_jobs_nonce = csrfguard_raw('_admin_cron_jobs_form_import-user');

?>
<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
		<div class="span12">
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
					<li><a href="#help-onthispage" data-toggle="tab">Filtering</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="help-overview">
						<p>This page allows you import videos from a particular channel/user from the following websites: YouTube.com, DailyMotion.com and Vimeo.com. <br />
						Enter the desired username below and start importing.</p>
						<p>The results will also include any available playlists and favorites belonging to the user.</p>
					</div>
					<div class="tab-pane fade" id="help-onthispage">
						<p>Each result is organized in a stack containing thumbnails, the video title, category, description and tags. Data such as video duration, original URL and more will be imported automatically.</p>
						
						<p>Youtube provides three thumbnails for each video and PHP MELODY allows you to choose the best one for your site. By default, the chosen thumbnail is the largest one, but changing it will be represented by a blue border.
						You can also do a quality control by using the video preview. Just click the play button overlaying the large thumbnail image and the video will be loaded in a window.</p>
						
						<p>By default none of the results is selected for import. Clicking on the top right switch from each stack will select it for importing. This is indicated by a green highlight of the stack. If you’re satisfied with all the results and wish to import them all at once, you can do that as well by selecting the &quot;SELECT ALL VIDEOS&quot; checkbox (bottom left).</p>
						<p>Enjoy!</p>
					</div>
				</div>
			</div> <!-- /tabbable -->
		</div><!-- .span12 -->
	</div><!-- /help-assist -->
	<div class="content">
		<a href="#" id="show-help-assist">Help</a>
	
		<nav id="import-nav" class="tabbable" role="navigation">
		<h2 class="h2-import pull-left">Import Videos from User</h2>
			<ul class="nav nav-tabs pull-right">
				<li><a href="import.php" class="tab-pane">Import by Keyword</a></li>
				<li class="active"><a href="import-user.php" class="tab-pane">Import from User</a></li>
				<li><a href="import-csv.php" class="tab-pane">Import from CSV</a></li>
			</ul>
		</nav>
		<br /><br />

		<div class="clearfix"></div>
		
		<?php if ( empty($config['youtube_api_key']) ) : ?>
			<div class="alert alert-help">
				<strong>Before importing videos from YouTube.com...</strong> 
				<p>To import videos from YouTube.com, an API key is required. <strong><a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank">Watch the video</a></strong> and see how to create your API key.</p>
				<p>Enter your API key in the <strong><a href="settings.php?highlight=youtube_api_key&view=video">Settings > Video Settings</a></strong> page (under "<em>Youtube Public API Key</em>" ).</p>
			</div>
		<?php endif; ?>	
		<?php 
	
		echo $info_msg;
		
		load_categories();
		if (count($_video_categories) == 0)
		{
			echo pm_alert_error('Please <a href="edit_category.php?do=add&type=video">create a category</a> first.');
		}
	
		// if (empty($_GET['action']))
		// {
		// 	echo pm_alert_info('Import playlists, favorites and videos from any YouTube, DailyMotion or Vimeo user.<br /> <small>Please note that <strong>private</strong> playlists will appear as being empty.</small>');
		// }
		?>
		
		<form name="import-user-search-form" id="import-user-search-form" action="" method="post" class="form-inline">
			<div class="input-append import-group">
				<input name="username" type="text"  value="<?php if($_POST['username'] != '') echo $_POST['username']; elseif($_GET['username'] != '') echo $_GET['username']; else echo 'Enter username';?>" placeholder="Enter username" class="span5 gautocomplete" />
				<select name="data_source">
					<option value="youtube" <?php echo ($data_source == 'youtube' || empty($data_source)) ? 'selected="selected"' : ''; ?>>Youtube User</option>
					<option value="youtube-channel" <?php echo ($data_source == 'youtube-channel') ? 'selected="selected"' : ''; ?>>Youtube Channel</option>
					<option value="dailymotion" <?php echo ($data_source == 'dailymotion') ? 'selected="selected"' : ''; ?>>Dailymotion User</option>
					<option value="vimeo" <?php echo ($data_source == 'vimeo') ? 'selected="selected"' : ''; ?>>Vimeo User</option>
				</select>
				
				<button type="button" class="btn" data-toggle="button" id="import-options"><i class="fa fa-filter opac5"></i> Filters</button> 
				<button type="submit" name="submit" class="btn" value="Find" id="search-user-btn" data-loading-text="Searching...">Search</button> 
				<input type="hidden" name="results" value="50">
			</div>
			
			<span class="hide" id="import-ui-control">
				<div class="opac7 list-choice pull-right">
					<button class="btn btn-normal btn-small" data-toggle="button" id="list"><i class="icon-th"></i> </button>
					<button class="btn btn-normal btn-small" data-toggle="button" id="stacks"><i class="icon-th-list"></i> </button>
				</div>
			
				<div class="pull-right">
					<a href="#modal_subscribe" data-toggle="modal" class="btn btn-small btn-info" rel="tooltip" title="Subscribe" id="btn-subscribe"><i class="icon-star icon-white"></i> Save this user</a>
					<a href="#unsubscribe" data-subscription-id="0" class="btn btn-success btn-small" id="btn-unsubscribe" title="Unsubscribe"><i class="icon-ok icon-white"></i> Subscribed</a>
				</div>
			</span>
			
			<div class="clearfix"></div>
				
			<div id="import-opt-content">
				<h4>Autocomplete Results</h4>
				<!--<label for="autofilling">
					<input type="checkbox" name="autofilling" id="autofilling" value="1" <?php if($_POST['autofilling'] == "1" || $_GET['autofilling'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> /> Autocomplete the video title
				</label>
				<br />-->
				<label>Autocomplete results with this category</label>
				<?php 
				$selected_categories = array();
				if (is_array($_POST['use_this_category']))
				{
					$selected_categories = $_POST['use_this_category'];
				}
				else if (is_string($_POST['use_this_category']) && $_POST['use_this_category'] != '') 
				{
					$selected_categories = (array) explode(',', $_POST['use_this_category']);
				}
				if ($_GET['utc'] != '')
				{
					$selected_categories = (array) explode(',', $_GET['utc']);
				}
				
				$categories_dropdown_options = array(
												'attr_name' => 'use_this_category[]',
												'attr_id' => 'main_select_category',
												'select_all_option' => false,
												'spacer' => '&mdash;',
												'selected' => $selected_categories,
												'other_attr' => 'multiple="multiple" size="3"',
												'option_attr_id' => 'check_ignore'
												);
				echo categories_dropdown($categories_dropdown_options);
				?>
				<br /> 
				<label for="autodata">
					<input type="checkbox" name="autodata" id="autodata" value="1" <?php if($_POST['autodata'] == "1" || $_GET['autodata'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> /> Autocomplete available data from API
				</label> <i class="icon-info-sign" rel="tooltip" title="Retrieve and include the video description, tags or any other information the API may provide."></i>
			</div><!-- #import-opt-content -->
		</form><!-- import-user-search-form -->
		<nav id="import-nav" class="tabbable import-user-nav hide" role="navigation">
			<h2 class="h2-import pull-left">Results</h2>
			<ul class="nav nav-tabs pull-right">
				<li class="active"><a href="#" id="import-user-nav-latest-uploads">Latest Uploads</a></li>
				<li class=""><a href="#" id="import-user-nav-playlists">Playlists</a></li>
				<li class=""><a href="#" id="import-user-nav-favorites">Favorites</a></li>
			</ul>
		</nav>

		<hr />
		
		<form name="import-search-results-form" id="import-search-results-form" action="" method="post">
			<?php $modframework->trigger_hook('admin_import_importopts'); ?>
			<div id="vs-grid">
				<span id="import-content-placeholder">
					<?php
					if (empty($_GET['action'])) 
					{
						$subscriptions = get_import_subscriptions('user');
						if ($subscriptions['total_results'] > 0)
						{
							$subscriptions_count = $subscriptions['total_results'];
							$subscriptions = $subscriptions['data'];
							
							foreach ($subscriptions as $k => $sub)
							{
								$subscriptions[$k] = unserialize($sub['data']);
								$subscriptions[$k]['sub_id'] = $sub['sub_id'];
								$subscriptions[$k]['sub_name'] = $sub['sub_name'];
								$subscriptions[$k]['last_query_time'] = (int) $sub['last_query_time'];
								$subscriptions[$k]['last_query_results'] = (int) $sub['last_query_results'];
								$subscriptions[$k]['sub_user_id'] = $sub['user_id'];
								$subscriptions[$k]['sub_username'] = $sub['username'];	
								$subscriptions[$k]['page'] = 1;
								$subscriptions[$k]['action'] = 'search'; // @since v2.3.1
								unset($subscriptions[$k]['playlistid']);  // @since v2.3.1
							}
						?>						
						<h3>Subscriptions</h3>
						<table class="table table-striped table-bordered pm-tables">
							<thead>
								<th width="5"></th>
								<th width="50"></th>
								<th style="text-align: left;padding:0 8px">Username</th>
								<th width="110">Saved by</th>
								<th width="220">Videos added this week</th>
								<th width="260">Action</th>
							</thead>
							<tbody>
								<?php foreach ($subscriptions as $k => $sub) : ?>
								<tr id="row-subscription-<?php echo $sub['sub_id']; ?>">
									<td>
										<div class="sprite <?php echo ( ! empty($sub['data_source'])) ? strtolower($sub['data_source']) : 'youtube'; ?>" rel="tooltip" title="Source: <?php echo ( ! empty($sub['data_source'])) ? ucfirst($sub['data_source']) : 'Youtube'; ?>"></div>
									</td>
									<td>
										<?php if ($sub['profile_avatar_url'] != '') : ?>
										<img src="<?php echo $sub['profile_avatar_url'];?>" width="36" height="36" />
										<?php endif; ?>
									</td>
									<td>
										<?php 
										$url_params = $sub;
										unset($url_params['profile_avatar_url'], $url_params['sub_name'], $url_params['last_query_time'], $url_params['last_query_results'], $url_params['sub_user_id'], $url_params['sub_username'], $url_params['title']);
										?>
										<strong><a href="import-user.php?<?php echo http_build_query($url_params);?>" class="row-user-subscription-link" data-sub-id="<?php echo $sub['sub_id']; ?>" data-query="<?php echo http_build_query($url_params);?>"><?php echo $sub['sub_name'];?></a></strong>
									</td>
									<td align="center" style="text-align:center">
										<a href="<?php echo get_profile_url($sub);?>" target="_blank"><?php echo $sub['sub_username'];?></a>
									</td>
									<td align="center" style="text-align:center">
										<?php if (import_subscription_cache_fresh($sub['last_query_time'])) :
											echo ($sub['last_query_results'] > 0) ? number_format($sub['last_query_results']) : '0';
										else : ?>
										<span class="row-subscription-get-results" data-subscription-id="<?php echo $sub['sub_id']; ?>">
											<img src="img/ico-loading.gif" class="row-subscription-loading-gif" width="16" height="16" />
										</span>
										<?php endif; ?>
									</td>
									<td align="center">
										<?php 
										$sub_is_scheduled = false; 
										show_cron_schedule_button($sub['sub_id'], 'import', $sub_is_scheduled, array('data-cron-ui' => 'import-user', 'data-pmnonce-t' => $cron_jobs_nonce['_pmnonce_t'])); 
										?>
										<a href="#" data-subscription-id="<?php echo $sub['sub_id'];?>" class="link-search-unsubscribe btn btn-small btn-danger pull-right" title="Unsubscribe">Unsubscribe</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php 
						} // end if ($subscriptions['total_results'] > 0)
					}// end if (empty($_GET['action'])) 
					?>
					
				</span><!-- #import-content-placeholder --> 
			
				<div id="import-ajax-message-placeholder" class="hide" style="position: fixed; left: 40%; top: 60px; width: 550px; z-index: 99999;"></div>
			
				<div class="clearfix"></div>

				<div id="import-load-more-div" class="hide">
					<button id="import-user-load-more-btn" name="import-user-load-more" class="btn btn-load-more">Load more</button>
				</div>
			
				<div id="stack-controls" class="row-fluid hide">
					<div class="span4" style="text-align: left">
						<label class="checkbox import-all">
							<input type="checkbox" name="checkall" id="checkall" class="btn" /> <small>SELECT ALL VIDEOS</small>
						</label>
					</div>
					<div class="span4">

					</div>
					<div class="span4">
						<div style="padding-right: 10px;">
							<button type="submit" name="submit" class="btn btn-success btn-strong" value="Import" id="import-submit-btn" data-loading-text="Importing...">Import <span id="status"><span id="count"></span></span> videos </button>
						</div>
					</div>
				</div><!-- #stack-controls -->
		</form><!-- import-user-search-results-form -->
	</div><!-- .content -->
</div><!-- .primary -->

<!-- subscribe modal -->
<div class="modal hide" id="modal_subscribe" tabindex="-1" role="dialog" aria-labelledby="modal_subscribe" aria-hidden="true">

	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>Subscribe to this user</h3>
	</div>

	<form name="subscribe-to-search" method="post" action="">
		<div class="modal-body">
			<div class="modal-response-placeholder hide"></div>
			<div class="alert alert-help">
				"<strong>Subscriptions</strong>" provide you quick access to common searches. <br /> You can also <strong>monitor for new videos</strong> being added and <strong>create auto-importing jobs</strong> based on your exact search terms (<em>including filters</em>) .
			</div>
			<div>
				<label>Subscription Name</label>
				<input type="text" name="sub-name" value="" size="40" />
				<input type="hidden" name="sub-params" value="" />
				<input type="hidden" name="sub-type" value="" />
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" name="status" value="1" />
			<a class="btn btn-link btn-strong" data-dismiss="modal" aria-hidden="true">Cancel</a>
			<button type="submit" name="Submit" value="Submit" class="btn btn-success btn-strong" id="btn-subscribe-modal-save" />Save</button>
		</div>
		<?php echo csrfguard_form('_admin_import_subscriptions'); ?>
	</form>
</div>

<!-- add cron job modal -->
<div class="modal hide" id="add-cron-job-modal" tabindex="-1" role="dialog" aria-labelledby="add-cron-job-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="add-cron-job-modal-label">New Automated Job (Auto-importing)</h3>
	</div>
	<form name="add-cron-job-form" id="add-cron-job-form" data-sub-id="" action="" method="post">
		<div class="modal-body">
			<span id="cron-add-modal-loading">
				<img src="img/ico-loading.gif" width="16" height="16"  /> Loading... 
			</span>
			<div id="cron-add-modal-content"></div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-link btn-strong" data-dismiss="modal" aria-hidden="true">Cancel</button>
			<button class="btn btn-strong btn-success" name="Submit" value="Save" id="cron-add-submit-btn" data-job-id="" data-sub-id="">Create Job</button> <!--aria-hidden="true"-->
		</div>
	</form>
</div>

<?php
include('footer.php');
?>