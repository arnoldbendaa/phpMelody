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
// | Copyright: (c) 2004-2015 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
$load_chzn_drop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_ibutton = 1;
$load_prettypop = 1;
$load_import_js = 1;
$load_googlesuggests = 1;
$load_lazy_load = 1;


$_page_title = 'Import Videos by Keyword';
include('header.php');
include_once(ABSPATH . 'include/cron_functions.php');

$action = '';
$action = trim($_GET['action']);

$post_n_get = 0;
$post_n_get = count($_POST) + count($_GET);

@set_time_limit(120);

$sources = a_fetch_video_sources();
$data_source = 'youtube';

if (in_array($_COOKIE['aa_import_from'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo')))
{
	$data_source = $_COOKIE['aa_import_from'];
}

if ($_GET['data_source'] != '' || $_POST['data_source'] != '')
{
	$data_source = ($_GET['data_source'] != '') ? $_GET['data_source'] : $_POST['data_source'];
	$data_source = ($data_source == 'youtube-channel') ? 'youtube' : $data_source;
}

$search_categories_dailymotion = array( 'all' => 'All',
										'music' => 'Music',
										'fun' => 'Comedy & Entertainment',
										'shortfilms' => 'Movies',
										'news' => 'News',
										'sport' => 'Sports',
										'auto' => 'Auto-Moto',
										'animals' => 'Animals',
										'people' => 'Celeb',
										'webcam' => 'Community & Blogs',
										'creation' => 'Creative',
										'school' => 'Education',
										'videogames' => 'Gaming',
										'lifestyle' => 'Lifestyle & How-to',
										'tech' => 'Tech',
										'travel' => 'Travel',
										'tv' => 'TV'
								);
$search_categories_youtube = array( 'all' => 'All',
									'32' => 'Action &amp; Adventure',
									'31' => 'Anime &amp; Animation',
									'2'  => 'Autos &amp; Vehicles',
									'33' => 'Classics',
									'23' => 'Comedy',
									'35' => 'Documentary',
									'36' => 'Drama',
									'27' => 'Education',
									'24' => 'Entertainment',
									'37' => 'Family',
									'1'  => 'Film &amp; Animation',
									'38' => 'Foreign',
									'20' => 'Gaming',
									'39' => 'Horror',
									'26' => 'Howto &amp; Style',
									'30' => 'Movies',
									'10' => 'Music',
									'25' => 'News &amp; Politics',
									'29' => 'Nonprofits &amp; Activism',
									'22' => 'People &amp; Blogs',
									'15' => 'Pets &amp; Animals',
									'40' => 'Sci-Fi &amp; Fantasy',
									'28' => 'Science &amp; Technology',
									'18' => 'Short Movies',
									'42' => 'Shorts',
									'43' => 'Shows',
									'17' => 'Sports',
									'41' => 'Thriller',
									'44' => 'Trailers',
									'19' => 'Travel &amp; Events',
									'21' => 'Videoblogging',
							);
if ($data_source == 'dailymotion')
{
	$search_categories = $search_categories_dailymotion;
}
else
{
	$search_categories = $search_categories_youtube;
}

$search_languages = array(
						  'all' => 'All',  
						  'af' => 'Afrikaans',
						  'sq' => 'Albanian',
						  'ar' => 'Arabic',
						  'hy' => 'Armenian',
						  'az' => 'Azerbaijani',
						  'be' => 'Belarusian',
						  'bs' => 'Bosnian',
						  'bg' => 'Bulgarian',
						  'ca' => 'Catalan; Valencian',
						  'cs' => 'Czech',
						  'zh-Hans' => 'Chinese (simplified)',
						  'zh-Hant' => 'Chinese (traditional)',
						  'cs' => 'Czech',
						  'da' => 'Danish',
						  'de' => 'German',
						  'nl' => 'Dutch',
						  'el' => 'Greek',
						  'en' => 'English',
						  'et' => 'Estonian',
						  'fi' => 'Finnish',
						  'fr' => 'French',
						  'ka' => 'Georgian',
						  'de' => 'German',
						  'gd' => 'Gaelic',
						  'ga' => 'Irish',
						  'ht' => 'Haitian',
						  'he' => 'Hebrew',
						  'hi' => 'Hindi',
						  'hr' => 'Croatian',
						  'hu' => 'Hungarian',
						  'is' => 'Icelandic',
						  'id' => 'Indonesian',
						  'it' => 'Italian',
						  'ja' => 'Japanese',
						  'kk' => 'Kazakh',
						  'ko' => 'Korean',
						  'lt' => 'Lithuanian',
						  'no' => 'Norwegian',
						  'pa' => 'Panjabi',
						  'pl' => 'Polish',
						  'pt' => 'Portuguese',
						  'ro' => 'Romanian',
						  'ru' => 'Russian',
						  'sk' => 'Slovak',
						  'sl' => 'Slovenian',
						  'es' => 'Spanish',
						  'sr' => 'Serbian',
						  'sv' => 'Swedish',
						  'ty' => 'Tahitian',
						  'ta' => 'Tamil',
						  'th' => 'Thai',
						  'tr' => 'Turkish',
						  'uk' => 'Ukrainian',
						  'vi' => 'Vietnamese',
						  'cy' => 'Welsh',
						);
$search_regions = array(
						'all' => 'All',
						'DZ' => 'Algeria',
						'AR' => 'Argentina',
						'AU' => 'Australia',
						'AT' => 'Austria',
						'BH' => 'Bahrain',
						'BE' => 'Belgium',
						'BA' => 'Bosnia and Herzegovina',
						'BR' => 'Brazil',
						'BG' => 'Bulgaria',
						'CA' => 'Canada',
						'CL' => 'Chile',
						'CO' => 'Colombia',
						'HR' => 'Croatia',
						'CZ' => 'Czech Republic',
						'DK' => 'Denmark',
						'EG' => 'Egypt',
						'EE' => 'Estonia',
						'FI' => 'Finland',
						'FR' => 'France',
						'DE' => 'Germany',
						'GH' => 'Ghana',
						'GB' => 'Great Britain',
						'GR' => 'Greece',
						'HK' => 'Hong Kong',
						'HU' => 'Hungary',
						'IN' => 'India',
						'ID' => 'Indonesia',
						'IE' => 'Ireland',
						'IL' => 'Israel',
						'IT' => 'Italy',
						'JP' => 'Japan',
						'JO' => 'Jordan',
						'KE' => 'Kenya',
						'KW' => 'Kuwait',
						'LV' => 'Latvia',
						'LB' => 'Lebanon',
						'MK' => 'Macedonia',
						'MY' => 'Malaysia',
						'MX' => 'Mexico',
						'ME' => 'Montenegro',
						'MA' => 'Morocco',
						'NL' => 'Netherlands',
						'NZ' => 'New Zealand',
						'NG' => 'Nigeria',
						'NO' => 'Norway',
						'OM' => 'Oman',
						'PE' => 'Peru',
						'PH' => 'Philippines',
						'PL' => 'Poland',
						'PT' => 'Portugal',
						'QA' => 'Qatar',
						'RO' => 'Romania',
						'RU' => 'Russia',
						'SA' => 'Saudi Arabia',
						'SN' => 'Senegal',
						'RS' => 'Serbia',
						'SG' => 'Singapore',
						'SK' => 'Slovakia',
						'ZA' => 'South Africa',
						'KR' => 'South Korea',
						'ES' => 'Spain',
						'SE' => 'Sweden',
						'CH' => 'Switzerland',
						'TW' => 'Taiwan',
						'TH' => 'Thailand',
						'TN' => 'Tunisia',
						'TR' => 'Turkey',
						'UG' => 'Uganda',
						'UA' => 'Ukraine',
						'AE' => 'United Arab Emirates',
						'GB' => 'United Kingdom',
						'US' => 'United States',
						'YE' => 'Yemen'
					);

$cron_jobs_nonce = csrfguard_raw('_admin_cron_jobs_form_import');

?>
<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
		<div class="span12">
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
					<li><a href="#help-onthispage" data-toggle="tab">On this page</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="help-overview">
						<p>This page allows you to import videos from a massive database of videos from the top three video sites: YouTube.com, DailyMotion.com and Vimeo.com. PHP Melody will also retrieve all the extra info from each video so you don't have to.</p>
						<p>Start importing videos simply by entering some keywords. You can also fine tune your seaches by clicking the &quot;Filters&quot; button.
						Note: For performance considerations, please work with 50 results per page.</p>
					</div>
					<div class="tab-pane fade" id="help-onthispage">
						<p>Each result is organized in a stack containing thumbnails, the video title, category, description and tags. Data such as video duration, original URL and more will be imported automatically.</p>
						<p>Youtube provides three thumbnails for each video and PHP MELODY allows you to choose the best one for your site. By default, the chosen thumbnail is the largest one, but changing it will be represented by a blue border.
						You can also do a quality control by using the video preview. Just click the play button overlaying the large thumbnail image and the video will be loaded in a window.</p>
						<p>By default none of the results is selected for import. Clicking on the top right switch from each stack will select it for importing. This is indicated by a green highlight of the stack. If you're satisfied with all the results and wish to import them all at once, you can do that as well by selecting the &quot;SELECT ALL VIDEOS” checkbox (bottom left).<br />
						Enjoy!</p>
					</div>
				</div>
			</div> <!-- /tabbable -->
		</div><!-- .span12 -->
	</div><!-- /help-assist -->
	<div class="content">
		<a href="#" id="show-help-assist">Help</a>
	
		<nav id="import-nav" class="tabbable" role="navigation">
		<h2 class="h2-import pull-left">Import Videos by Keyword</h2>
			<ul class="nav nav-tabs pull-right">
				<li class="active"><a href="import.php" class="tab-pane">Import by Keyword</a></li>
				<li><a href="import-user.php" class="tab-pane">Import from User</a></li>
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
		
		load_categories();
		if (count($_video_categories) == 0) 
		{
			echo pm_alert_error('Please <a href="edit_category.php?do=add&type=video">create a category</a> first.');
		}
		?>
		<form name="import-search-videos-form" id="import-search-videos-form" action="" method="post" class="form-inline">
			<div class="input-append import-group">
				<input name="keyword" type="text" value="<?php echo ($_POST['keyword'] != '') ? $_POST['keyword'] : str_replace("+", " ", $_GET['keyword']); ?>" size="35" placeholder="" class="span5 gautocomplete" />
				<select name="data_source">
					<option value="youtube" <?php echo ($data_source == 'youtube' || empty($data_source)) ? 'selected="selected"' : ''; ?>>Youtube</option>
					<option value="dailymotion" <?php echo ($data_source == 'dailymotion') ? 'selected="selected"' : ''; ?>>Dailymotion</option>
					<option value="vimeo" <?php echo ($data_source == 'vimeo') ? 'selected="selected"' : ''; ?>>Vimeo</option>
				</select>
				<button type="button" class="btn" data-toggle="button" id="import-options"><i class="fa fa-filter opac5"></i> Filters</button>
				<button type="submit" name="submit" class="btn" id="search-videos-btn" data-loading-text="Searching...">Search</button>
			</div>
			
			<span class="hide" id="import-ui-control">
				<div class="opac7 list-choice pull-right">
					<button class="btn btn-normal btn-small" data-toggle="button" id="list"><i class="icon-th"></i> </button>
					<button class="btn btn-normal btn-small" data-toggle="button" id="stacks"><i class="icon-th-list"></i> </button>
				</div>
				<div class="pull-right">					
					<a href="#modal_subscribe" data-toggle="modal" class="btn btn-small btn-info" rel="tooltip" title="Save this search for quick access to future videos." id="btn-subscribe"><i class="icon-star icon-white"></i> Save this search</a>
					<a href="#unsubscribe" data-subscription-id="0" class="btn btn-success btn-small" id="btn-unsubscribe" title="Unsubscribe"><i class="icon-ok icon-white"></i> Subscribed</a>
				</div>
			</span>

			<div class="clearfix"></div>
			
			<div id="import-opt-content">
				<div class="row-fluid">
					<div class="span3">
						<h4>Autocomplete Results</h4>
					
						<!--
						<input type="checkbox" name="autofilling" id="autofilling" value="1" <?php if($_POST['autofilling'] == "1" || $_GET['autofilling'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
						<label for="autofilling">Auto-populate the video title</label>-->
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
														'other_attr' => 'multiple="multiple" size="3" data-placeholder="Import videos into..."',
														'option_attr_id' => 'check_ignore'
														);
						echo categories_dropdown($categories_dropdown_options);
						?>
						<br>
						<label for="autodata">
						<input type="checkbox" name="autodata" id="autodata" value="1" <?php if($_POST['autodata'] == "1" || $_GET['autodata'] == "1" || $post_n_get == 0) echo 'checked="checked"'; ?> />
						Autocomplete available data from API</label> <i class="icon-info-sign" rel="tooltip" title="Retrieve and include the video description, tags or any other information the API may provide."></i>
					</div>
					<div class="span9">
						<h4>Filter Videos</h4>
						<div class="row-fluid">
							<div class="span3" id="div_search_category">
								<label>Category</label><br>
								<select name="search_category" class="span12" <?php echo ($data_source == 'vimeo') ? 'disabled="disabled"' : ''; ?>>
									<?php foreach ($search_categories as $value => $label) : ?>
									<option value="<?php echo $value; ?>" <?php echo ($_GET['search_category'] == $value || $_POST['search_category'] == $value) ? 'selected="selected"' : '';?>><?php echo $label;?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="span3" id="div_search_duration">
								<label>Duration</label><br>
								<select name="search_duration" class="span12" <?php echo ($data_source == 'vimeo') ? 'disabled="disabled"' : ''; ?>>
									<option value="all">All</option>
									<option value="short" <?php echo ($_GET['search_duration'] == 'short' || $_POST['search_duration'] == 'short') ? 'selected="selected"' : ''; ?>>Short (~4 minutes)</option>
									<option value="medium" <?php echo ($_GET['search_duration'] == 'medium' || $_POST['search_duration'] == 'medium') ? 'selected="selected"' : ''; ?>>Medium (4-20 minutes)</option>
									<option value="long" <?php echo ($_GET['search_duration'] == 'long' || $_POST['search_duration'] == 'long') ? 'selected="selected"' : ''; ?>>Long (20+ minutes)</option>
								</select>
							</div>
							<div class="span3" id="div_search_time">
								<label>Upload date</label><br>
								<select name="search_time" class="span12" <?php echo ($data_source == 'vimeo') ? 'disabled="disabled"' : ''; ?>>
									<option value="all_time">All time</option>
									<option value="today" <?php echo ($_GET['search_time'] == 'today' || $_POST['search_time'] == 'today') ? 'selected="selected"' : ''; ?>>Today</option>
									<option value="this_week" <?php echo ($_GET['search_time'] == 'this_week' || $_POST['search_time'] == 'this_week') ? 'selected="selected"' : ''; ?>>This week</option>
									<option value="this_month" <?php echo ($_GET['search_time'] == 'this_month' || $_POST['search_time'] == 'this_month') ? 'selected="selected"' : ''; ?>>This month</option>
								</select>
							</div>
							<div class="span3" id="div_search_orderby">
								<label>Order by</label><br>
								<select name="search_orderby" class="span12">
									<option value="relevance" <?php echo ($_GET['search_orderby'] == 'relevance' || $_POST['search_orderby'] == 'relevance') ? 'selected="selected"' : '';?>>Relevance</option>
									<option value="date" <?php echo ($_GET['search_orderby'] == 'date' || $_POST['search_orderby'] == 'date') ? 'selected="selected"' : '';?>>Upload date</option>
									<option value="viewCount" <?php echo ($_GET['search_orderby'] == 'viewCount' || $_POST['search_orderby'] == 'viewCount') ? 'selected="selected"' : '';?>>View count</option>
									<option value="rating" <?php echo ($_GET['search_orderby'] == 'rating' || $_POST['search_orderby'] == 'rating') ? 'selected="selected"' : '';?>>Rating</option>
								</select> 
							</div>
						</div>
						<hr />
						<div class="row-fluid">
							<!--<div class="span3" id="div_results">
								<label>Results per page <i class="icon-info-sign" rel="tooltip" title="For optimum performance, a maximum of 50 videos per page is recommended."></i></label><br>
								<select name="results" class="span11">
									<option value="10" <?php if($_POST['results'] == 10 || $_GET['results'] == 10) echo 'selected="selected"'; ?>>10 results</option>
									<option value="20" <?php if($_POST['results'] == 20 || $_GET['results'] == 20) echo 'selected="selected"'; ?>>20 results</option>
									<option value="30" <?php if($_POST['results'] == 30 || $_GET['results'] == 30|| $post_n_get == 0) echo 'selected="selected"'; ?>>30 results</option>
									<option value="40" <?php if($_POST['results'] == 40 || $_GET['results'] == 40) echo 'selected="selected"'; ?>>40 results</option>
									<option value="50" <?php if($_POST['results'] == 50 || $_GET['results'] == 50) echo 'selected="selected"'; ?>>50 results (recommended)</option>
									<option value="100" <?php if($_POST['results'] == 100 || $_GET['results'] == 100) echo 'selected="selected"'; ?>>100 results</option>
								</select>
							</div>
							-->
							
							<div class="span3" id="div_search_language">
								<label>Language</label><br>
								<select name="search_language" class="span12" <?php echo ($data_source == 'vimeo') ? 'disabled="disabled"' : ''; ?>>
									<?php foreach ($search_languages as $lang_code => $label): ?>
									<option value="<?php echo $lang_code; ?>" <?php echo ($_GET['search_language'] == $lang_code || $_POST['search_language'] == $lang_code ) ? 'selected="selected"' : ''; ?>><?php echo $label; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="span3" id="div_search_license">
								<label>License</label><br>
								<select name="search_license" class="span12" <?php echo ($data_source != 'youtube') ? 'disabled="disabled"' : ''; ?>>
									<option value="all">All</option>
									<option value="cc" <?php echo ($_GET['search_license'] == 'cc' || $_POST['search_license'] == 'cc') ? 'selected="selected"' : '';?>>Creative Commons</option>
									<option value="youtube" <?php echo ($_GET['search_license'] == 'youtube' || $_POST['search_license'] == 'youtube') ? 'selected="selected"' : '';?>>Youtube</option>
								</select>
							</div>
							<div class="span3" id="div_features">
								<label>Features</label><br>
								<label class="checkbox">
									<input type="checkbox" name="search_hd" value="true" <?php echo ($_GET['search_hd'] == 'true' || $_POST['search_hd'] == 'true') ? 'checked="checked"' : ''; ?> <?php echo ($data_source == 'vimeo') ? 'disabled="disabled"' : ''; ?> /> HD
								</label>
								<label class="checkbox">
									<input type="checkbox" name="search_3d" value="true" <?php echo ($_GET['search_3d'] == 'true' || $_POST['search_3d'] == 'true') ? 'checked="checked"' : ''; ?> <?php echo ($data_source == 'vimeo') ? 'disabled="disabled"' : ''; ?> /> 3D
								</label>
							</div>
						</div>
					</div>
				</div><!-- .row-fluid --> 
			</div><!-- #import-opt-content -->
			<input type="hidden" name="results" value="50" />
		</form>

		<hr />
		
		<form name="import-search-results-form" id="import-search-results-form" action="" method="post">
			<?php $modframework->trigger_hook('admin_import_importopts'); ?>
			<div id="vs-grid">
				<span id="import-content-placeholder">
					
					<?php
					if (empty($_GET['action'])) 
					{
						$subscriptions = get_import_subscriptions();
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
								// $subscriptions[$k]['search_time'] = 'this_week'; // @since v2.5
								$subscriptions[$k]['page'] = 1;
							}
						?>
						<h3><strong>Subscriptions</strong></h3>
						<table class="table table-striped table-bordered pm-tables">
							<thead>
								<th width="5"></th>
								<th style="text-align: left;padding:0 8px">Search</th>
								<th width="110">Saved by</th>
								<th width="220">Videos added this week</th>
								<th width="260">Action</th>
							</thead>
							<tbody>
								<?php foreach ($subscriptions as $k => $sub) : ?>
								<tr id="row-subscription-<?php echo $sub['sub_id']; ?>">
									<td>
										<div class="sprite <?php echo ( ! empty($sub['data_source'])) ? strtolower($sub['data_source']) : 'youtube'; ?>" rel="tooltip" title="Source: <?php echo ( ! empty($sub['data_source'])) ? ucfirst($sub['data_source']) : 'youtube'; ?>"></div>
									</td>
									<td>
										<?php
										$url_params = $sub;
										unset($url_params['sub_name'], $url_params['last_query_time'], $url_params['last_query_results'], $url_params['sub_user_id'], $url_params['sub_username']);
										?>
										<strong><a href="import.php?<?php echo http_build_query($url_params);?>" class="row-subscription-link" data-sub-id="<?php echo $sub['sub_id']; ?>" data-query="<?php echo http_build_query($url_params);?>"><?php echo $sub['sub_name'];?></a></strong>
									</td>
									<td align="center" style="text-align:center">
										<a href="<?php echo get_profile_url($sub);?>" target="_blank"><?php echo $sub['sub_username'];?></a>
									</td>
									<td align="center" style="text-align:center">
										<?php $url_params['search_time'] = 'this_week'; ?>
										<a href="import.php?<?php echo http_build_query($url_params);?>" class="row-subscription-link" data-sub-id="<?php echo $sub['sub_id']; ?>" data-query="<?php echo http_build_query($url_params);?>">
											<?php if (import_subscription_cache_fresh($sub['last_query_time'])) :
												echo ($sub['last_query_results'] > 0) ? number_format($sub['last_query_results']) : '0';
											else : ?>
											<span class="row-subscription-get-results" data-subscription-id="<?php echo $sub['sub_id']; ?>">
												<img src="img/ico-loading.gif" class="row-subscription-loading-gif" width="16" height="16" />
											</span>
											<?php endif; ?>
										</a>
									</td>
									<td align="center" style="text-align:center;">
										<?php 
										$sub_is_scheduled = false; 
										show_cron_schedule_button($sub['sub_id'], 'import', $sub_is_scheduled, array('data-cron-ui' => 'import', 'data-pmnonce-t' => $cron_jobs_nonce['_pmnonce_t'])); 
										?>
										<a href="#" data-subscription-id="<?php echo $sub['sub_id'];?>" class="link-search-unsubscribe btn btn-small btn-danger pull-right" title="Unsubscribe">Unsubscribe</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						
						<?php 
						}  // end if ($subscriptions['total_results'] > 0)
					} // end if (empty($_GET['action']))
					?>
					
				</span><!-- #import-content-placeholder -->
			</div><!-- #vs-grid -->
			
			<div id="import-ajax-message-placeholder" class="hide" style="position: fixed; left: 40%; top: 60px; width: 550px; z-index: 99999;"></div>
			
			<div class="clearfix"></div>

			<div id="import-load-more-div" class="hide">
				<button id="import-load-more-btn" name="import-load-more" class="btn btn-load-more">Load more</button>
			</div>
			
			<div id="stack-controls" class="row-fluid hide">
				<div class="span4" style="text-align: left;">
					<label class="checkbox import-all">
						<input type="checkbox" name="checkall" id="checkall" class="btn" /> <small>SELECT ALL VIDEOS</small>
					</label>				
				</div>
				<div class="span4">

				</div>
				<div class="span4">
					<div style="padding-right: 10px;">
						<span class="import-ajax-loading-animation"><img src="img/ico-loader.gif" width="16" height="16" /></span>
						<button type="submit" name="submit" class="btn btn-success btn-strong" value="Import" id="import-submit-btn" data-loading-text="Importing...">Import <span id="status"><span id="count"></span></span> videos </button>
					</div>
				</div>
			</div><!-- #stack-controls -->
		
		</form><!-- import-search-results-form -->
	</div><!-- .content -->
</div><!-- .primary -->


<!-- subscribe modal -->
<div class="modal hide" id="modal_subscribe" tabindex="-1" role="dialog" aria-labelledby="modal_subscribe" aria-hidden="true">

	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>Subscribe to this search</h3>
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
				<input type="hidden" name="sub-type" value="search" />
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
$select_category_youtube_inner_html = '';
$select_category_dailymotion_inner_html = '';
foreach ($search_categories_youtube as $value => $label)
{
	$select_category_youtube_inner_html .= '<option value="'. $value .'">'. $label .'</option>';
}
foreach ($search_categories_dailymotion as $value => $label)
{
	$select_category_dailymotion_inner_html .= '<option value="'. $value .'">'. $label .'</option>';
}

// incoming call from "Add Media" modal
if ($_GET['action'] != '' && $_POST['keyword'] != '') : ?>
<script type="text/javascript">
$(document).ready(function(){
	pm_import_search_count = 0;
	pm_import_next_page = '';
		
	// reset the Subscribe button if state = subscribed
	$('#btn-subscribe').show();
	$('#btn-unsubscribe').hide();
	
	import_search("p=import&do=search&"+ $('#import-search-videos-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
});
</script>
<?php
endif; 

include('footer.php');