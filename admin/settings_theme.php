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

$showm = '8';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$load_colorpicker = 1;
$load_scrolltofixed = 1;
$load_settings_theme_resources = 1;
$_page_title = 'Layout Settings';
include('header.php');
include_once('syndicate_news.php');

//$config	= get_config();

$inputs = array();
$info_msg = '';
$video_sources = a_fetch_video_sources();

if ($_POST['submit'] == "Save" && ( ! csrfguard_check_referer('_admin_settings')))
{
	$info_msg = 'Invalid token or session expired. Please load this page from the menu and try again.'; 
}
else if ($_POST['submit'] == "Save")
{
	$req_fields = array("browse_page" => "Videos per browsing page",
						"top_page_limit" => "Popular videos page (limit)",
						"new_page_limit" => "New videos page (limit)",
						"comments_page" => "Comments per page",
						"thumb_video_w" => "Video thumbnail width",
						"thumb_video_h" => "Video thumbnail height",
						"thumb_article_w" => "Article thumbnail width",
						"thumb_article_h" => "Article thumbnail height",
						"thumb_avatar_w" => "User avatar width",
						"thumb_avatar_h" => "User avatar heigh"
					);
	$num_fields = array('new_videos', 'article_widget_limit', 'chart_days', 'top_videos', 'playingnow_limit', 'watch_related_limit', 'watch_toprated_limit', 'fav_limit', 'browse_page', 'comments_page', 'thumb_video_w', 'thumb_video_h', 'thumb_article_w', 'thumb_article_h', 'thumb_avatar_w', 'thumb_avatar_h', 'chart_days', 'show_stats', 'show_tags', 'tag_cloud_limit', 'search_suggest', 'show_addthis_widget', 'browse_articles', 'rtl_support');
	foreach($_POST as $k => $v)
	{
		if($_POST[$k] == '' && in_array($k, $req_fields))
		{
			$info_msg .= "'".$req_fields[$k] . "' field cannot be left blank!";
		}
		if(in_array($k, $num_fields))
		{
			$v = (int) $v;
			$v = abs($v);
			$inputs[$k] = $v;
		}
		else if ( ! is_array($v))
			$inputs[$k] = $v;
	}
	
	//  Template has changed? Clear the Smarty Cache & Compile directories
		if ($inputs['template_f'] != $config['template_f'])
		{
				@include_once('../Smarty/Smarty.class.php');
				$smarty = new Smarty;
				// clear out all cache files
				$smarty->clear_all_cache();

				//  empty compile directory
				$dir = @opendir($smarty->compile_dir);
				if ($dir)
				{
						while (false !== ($file = readdir($dir)))
						{
								if(strlen($file) > 2)
								{
										$tmp_parts = explode('.', $file);
										$ext = array_pop($tmp_parts);
										$ext = strtolower($ext);
										
										if ($ext == 'php' && strpos($file, '%') !== false)
										{
												unlink($smarty->compile_dir .'/'. $file);
										}
								}
						}
						closedir($dir);
				}
				
				//  empty cache directory
				$dir = @opendir($smarty->cache_dir);
				if ($dir)
				{
						while (false !== ($file = readdir($dir)))
						{
								if(strlen($file) > 2)
								{
										$tmp_parts = explode('.', $file);
										$ext = array_pop($tmp_parts);
										$ext = strtolower($ext);
										
										if ($ext == 'php' && strpos($file, '%') !== false)
										{
												unlink($smarty->cache_dir .'/'. $file);
										}
								}
						}
						closedir($dir);
				}
		}

	// Save config	
	if($info_msg == '')
	{
		foreach ($inputs as $config_name => $config_value)
		{
			if ($config_name != 'submit' && $config_name != 'allow_user_uploadvideo_unit')
			{	
				update_config($config_name, $config_value, true);
			}
		}
	}
}

$selected_tab_view = '';
$page_tab_views = array('tabname1', 't1', 't2', 't3', 'general', 'customize', 'store');

if ($_POST['settings_selected_tab'] != '' || $_GET['view'] != '')
{
	$selected_tab_view = ($_POST['settings_selected_tab'] != '') ? $_POST['settings_selected_tab'] : $_GET['view'];
	if ( ! in_array($selected_tab_view, $page_tab_views)) 
	{
		$selected_tab_view = '';
	}
}

?>
<div id="adminPrimary">
		<div class="content">

<form name="sitesettings" method="post" action="settings_theme.php">
<?php echo csrfguard_form('_admin_settings'); ?>
				<div id="settings-jump"></div>
				<nav id="import-nav" class="tabbable" role="navigation">
				<h2 class="h2-import pull-left"><?php echo $_page_title; ?></h2>
						<ul class="nav nav-tabs pull-right">
						<li class="<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == '' || $selected_tab_view == 't1' || $selected_tab_view == 'general') ? 'active' : '';?>"><a href="#tabname1" data-toggle="tab" class="tab-pane">General Settings</a></li>
						<li class="<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? 'active' : '';?>"><a href="#t2" data-toggle="tab" class="tab-pane<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? ' active' : '';?>">Customize Theme</a></li>
						<li class="<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? 'active' : '';?>"><a data-toggle="tab" href="#t3" class="tab-pane<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? ' active' : '';?>">Theme Store</a></li>
						</ul>
				</nav>
		<div style="clear:both"></div>
<?php if ('' != $info_msg) : ?>
		<br /> 
		<?php echo pm_alert_error($info_msg); ?>
<?php elseif ($_POST['submit'] == "Save" && $info_msg == '') : ?>
		<br /> 
		<?php echo pm_alert_success('The new settings have been saved and applied.'); ?>
<?php endif; ?>
<div class="">
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td>
		</td>
	</tr>
		<td valign="top">
	<div class="tab-content">
	<div class="tab-pane fade<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == ''  || $selected_tab_view == 'general') ? ' in active' : '';?>" id="tabname1">
		<h2 class="sub-head-settings">Header</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">

		<?php if ($config['template_f'] == 'default' || $config['template_f'] == 'Echo' || $config['template_f'] == 'echo') : ?>
			<tr>
					<td width="20%" valign="top">Right-To-Left layout</td>
					<td>
						<label><input name="rtl_support" type="radio" value="1" <?php echo ($config['rtl_support']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
						<label><input name="rtl_support" type="radio" value="0" <?php echo ($config['rtl_support']==0) ? 'checked="checked"' : "";?> /> Disabled</label>
						<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Useful for right-to-left layout orientation. <strong>Note</strong>: Not all themese support this feature."><i class="icon-info-sign"></i></a>
					</td>
			</tr>
		<?php endif; ?>
			<tr>
				<td width="20%">Site theme</td>
				<td>
				<select name="template_f">
				<?php echo dropdown_templates($config['template_f']); ?>
				</select> 
				<?php if ($config['template_f'] == 'default') { ?>
							 <a href="customize.php" class="btn btn-small" target="_blank"><i class="icon-share"></i> Customize</a>
				<?php } ?>
				</td>
			</tr>
			<tr>
				<td width="20%" valign="top">Site title</td>
				<td>
				<input name="homepage_title" type="text"  size="45" value="<?php echo htmlspecialchars(stripslashes($config['homepage_title'])); ?>" />
				</td>
			</tr>
			<tr>
					<td width="20%" valign="top">Logo image</td>
					<td>
						<div id="settings-logo-container">
							<?php if ($config['custom_logo_url'] != ''): ?>
					<img src="<?php echo $config['custom_logo_url'];?>" border="0" />
				<?php endif; ?>
						</div>
			<button class="btn btn-medium btn-danger <?php if ($config['custom_logo_url'] == '') echo 'hide';?>" id="btn-remove-logo">Remove logo</button>
						<span class="btn fileinput-button">
							<span>Upload logo</span>
				<input type="file" name="file" id="upload-logo-btn" />
			</span>
						<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="We recommend using a transparent PNG image with a suggested width of <strong>233 pixels</strong> and maximum height of <strong>80 pixels</strong>. Large images will be automatically resized to fit within the header."><i class="icon-info-sign"></i></a>
					</td>
			</tr>
			<tr>
					<td>Meta keywords</td>
					<td>
							<input name="homepage_keywords" type="text" size="45" value="<?php echo htmlspecialchars(stripslashes($config['homepage_keywords'])); ?>" />
					</td>
			</tr>
			<tr>
					<td valign="top">Meta description</td>
					<td>
							<textarea name="homepage_description" rows="2" cols="55"><?php echo stripslashes($config['homepage_description']); ?></textarea>
					</td>
			</tr>
		<tr>
		<td>Live search recommendations</td>
		<td>
		<label><input name="search_suggest" type="radio" value="1" <?php echo ($config['search_suggest']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
		<label><input name="search_suggest" type="radio" value="0" <?php echo ($config['search_suggest']==0) ? 'checked="checked"' : "";?> /> Disabled</label>
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If <em>enabled</em>, users will see a search suggestions list as they type the search query."><i class="icon-info-sign"></i></a>
				</td>
		</tr>
		</table>
		
		<h2 class="sub-head-settings">Homepage</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
		<tr>
				<td width="20%">Featured videos limit</td>
				<td>
						<input name="homepage_featured_limit" type="text" size="8" class="span1" value="<?php echo $config['homepage_featured_limit']; ?>" /> videos
				</td>
			</tr>
			<tr>
				<td width="20%">Popular videos widget: sort by</td>
				<td>
			<select name="top_videos_sort">
			 <option value="views" <?php if($config['top_videos_sort'] == "views") echo ' selected="selected" ';?>>Most viewed</option>
			 <option value="rating"<?php if($config['top_videos_sort'] == "rating") echo ' selected="selected" ';?>>Most liked</option>
			 <option value="chart" <?php if($config['top_videos_sort'] == "chart") echo ' selected="selected" ';?>>Most viewed (last <?php echo $config['chart_days'];?> days)</option>
			</select>
		</tr>
		<tr>
				<td>Popular videos widget: limit</td>
				<td>
				<input name="top_videos" type="text" size="8" class="span1" value="<?php echo $config['top_videos']; ?>" /> videos
						<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many videos you want to list in the <em>Popular Videos</em> widget from your homepage."><i class="icon-info-sign"></i></a>
						</td>
				</tr>
				<tr>
				<td>'Being watched' limit</td>
				<td><input name="playingnow_limit" type="text" size="8" class="span1" value="<?php echo $config['playingnow_limit']; ?>" /> videos
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many videos you want to list in the <em>'Being watched now'</em> widget from your homepage (under the homepage 'Featured' video)."><i class="icon-info-sign"></i></a>
				</td>
				</tr>
				<tr>
				<td>New videos limit</td>
				<td><input name="new_videos" type="text" size="8" class="span1" value="<?php echo $config['new_videos']; ?>" /> videos
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many videos you want to list in the <em>New Videos</em> widget from your homepage."><i class="icon-info-sign"></i></a>
				</td>
				</tr>
				<tr>
				<td>Articles widget: limit</td>
				<td><input name="article_widget_limit" type="text" size="8" class="span1" value="<?php echo $config['article_widget_limit']; ?>" /> articles
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set how many articles you want to show in the <em>Latest Articles</em> widget from your homepage."><i class="icon-info-sign"></i></a>
				</td>
				</tr>
		<tr>
		<td>Show statistics</td>
			<td>
			<label><input name="show_stats" type="radio" value="1" <?php echo ($config['show_stats']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="show_stats" type="radio" value="0" <?php echo ($config['show_stats']==0) ? 'checked="checked"' : "";?> /> No</label>
						<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If enabled, a widget containing details such as <em>member count</em>, <em>video count</em>, etc. will appear on your homepage."><i class="icon-info-sign"></i></a>
						</td>
		</tr>
		<tr>
			<td>Show tag cloud</td>
			<td>
			<label><input name="show_tags" type="radio" value="1" <?php echo ($config['show_tags']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="show_tags" type="radio" value="0" <?php echo ($config['show_tags']==0) ? 'checked="checked"' : "";?> /> No</label>
						<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If enabled, a widget listing the most common tags will appear on your homepage. This helps visitors find popular content on your site."><i class="icon-info-sign"></i></a>
						</td>
			</tr>
		 <tr>
			<td>Tag cloud limit</td>
			<td><input name="tag_cloud_limit" type="text" size="8" class="span1" value="<?php echo $config['tag_cloud_limit']; ?>" /> tags</td>
			</tr>
		<tr>
			<td>Order tag cloud</td>
			<td>
			<label><input name="shuffle_tags" type="radio" value="0" <?php echo ($config['shuffle_tags']==0) ? 'checked="checked"' : "";?> /> Descending</label> 
			<label><input name="shuffle_tags" type="radio" value="1" <?php echo ($config['shuffle_tags']==1) ? 'checked="checked"' : "";?> /> Shuffle</label>
						</td>
		</tr>
		</table>

		<h2 class="sub-head-settings">Video &amp; Content Pages</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
		<tr>
			<td>"Related" videos limit</td>
			<td><input name="watch_related_limit" type="text" size="8" class="span1" value="<?php echo $config['watch_related_limit']; ?>" /> videos
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This value must be greater than 0 (zero)."><i class="icon-info-sign"></i></a></td>
		</tr>
		<tr>
			<td>"Popular" videos limit</td>
			<td><input name="watch_toprated_limit" type="text" size="8" class="span1" value="<?php echo $config['watch_toprated_limit']; ?>" /> videos
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This value must be greater than 0 (zero)."><i class="icon-info-sign"></i></a></td>
		</tr>
		<tr>
				<td width="20%">Show a floating social sharing widget (share buttons)</td>
				<td>
						<label><input name="show_addthis_widget" type="radio" value="1" <?php echo ($config['show_addthis_widget']==1) ? 'checked="checked"' : "";?> /> Yes</label>  
						<label><input name="show_addthis_widget" type="radio" value="0" <?php echo ($config['show_addthis_widget']==0) ? 'checked="checked"' : "";?> /> No</label>
						<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="When enabled, a floating widget of sharing buttons (Facebook, Twitter, Google, etc.) appears next to your content pages (video and article pages)."><i class="icon-info-sign"></i></a>
				</td>
		</tr>
		</table>
		
		<h2 class="sub-head-settings">Listings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
			<tr>
					<td>Articles per browsing page</td>
					<td><input name="browse_articles" type="text" size="8" class="span1" value="<?php echo $config['browse_articles']; ?>" /> articles</td>
			</tr>
			<tr>
				<td width="20%">Videos per browsing page</td>
				<td><input name="browse_page" type="text" size="8" class="span1" value="<?php echo $config['browse_page']; ?>" /> videos
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to show on each category or search results page."><i class="icon-info-sign"></i></a></td>
			</tr>
	 <tr>
		<td>"<a href="<?php echo _URL; ?>/newvideos.php">New videos</a>" page</td>
		<td><input name="new_page_limit" type="text" size="8" class="span1" value="<?php echo $config['new_page_limit']; ?>" /> videos
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to list on the 'New Videos' page."><i class="icon-info-sign"></i></a></td>
	 </tr>
	 <tr>
		<td>"<a href="<?php echo _URL; ?>/topvideos.php?do=recent">Popular videos</a>" page</td>
		<td><input name="top_page_limit" type="text" size="8" class="span1" value="<?php echo $config['top_page_limit']; ?>" /> videos
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit how many videos to list on the 'Popular Videos' page."><i class="icon-info-sign"></i></a></td>
	 </tr>
			<tr>
				<td>Refresh "<a href="<?php echo _URL; ?>/topvideos.php?do=recent">Popular videos</a>" page</a> every</td>
				<td>
		<input name="chart_days" type="text" size="8" class="span1" value="<?php echo $config['chart_days']; ?>" /> days
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Insert <strong>0 (zero)</strong> to prevent the list from being refreshed. This will result in having an 'All time' popular videos chart/list."><i class="icon-info-sign"></i></a>
				</td>
		 </tr>
	 <tr>
		<td>Comments per page</td>
		<td><input name="comments_page" type="text" size="8" class="span1" value="<?php echo $config['comments_page']; ?>" />
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit the number of comments for each article/video."><i class="icon-info-sign"></i></a></td>
	 </tr>
		</table>

		<h2 class="sub-head-settings">Thumbnails &amp; Avatars</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
			<tr>
				<td width="20%">Video Thumbnails</td>
				<td>
				<input name="thumb_video_w" type="text" size="8" class="span1" value="<?php echo $config['thumb_video_w']; ?>" style="width:30px;" /> x <input name="thumb_video_h" type="text" size="8" class="span1" value="<?php echo $config['thumb_video_h']; ?>" style="width:30px;" /> <small>px</small>
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Assign the maximum width and height for video thumbnails. Uploaded thumbnails will be resized to fit these specifications. <br><strong>Format</strong>: WIDTH x HEIGHT (in pixels)"><i class="icon-info-sign"></i></a>
				</td>
			</tr>
	 <tr>
		<td>Article Thumbnails</td>
				<td>
				<input name="thumb_article_w" type="text" size="8" class="span1" value="<?php echo $config['thumb_article_w']; ?>" style="width:30px;" /> x <input name="thumb_article_h" type="text" size="8" class="span1" value="<?php echo $config['thumb_article_h']; ?>" style="width:30px;" /> <small>px</small>
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Assign the maximum width and height for article thumbnails. Uploaded thumbnails will be resized to fit these specifications. <br><strong>Format</strong>: WIDTH x HEIGHT (in pixels)"><i class="icon-info-sign"></i></a>
				<?php if ( $config['mod_article'] != 1 ) : ?>
		<span class="label label-warning">The 'Article Module' is disabled. Visit the 'Settings' page to enable it.</span>
		<?php endif; ?>
				</td>
	 </tr>
	 <tr>
		<td>User Avatar</td>
				<td>
				<input name="thumb_avatar_w" type="text" size="8" class="span1" value="<?php echo $config['thumb_avatar_w']; ?>" style="width:30px;" /> x <input name="thumb_avatar_h" type="text" size="8" class="span1" value="<?php echo $config['thumb_avatar_h']; ?>" style="width:30px;" /> <small>px</small>
				</td>
	 </tr>
		</table>
	</div>
		
	<div class="tab-pane fade<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'customize') ? ' in active' : '';?>" id="t2">    
	<h2 class="sub-head-settings">Customize Theme</h2>
		<?php
	if ($config['template_f'] != 'default') 
	{
	?>
		<div class="alert alert-danger">Sorry, the <strong><?php echo ucfirst($config['template_f']); ?></strong> theme doesn't support live customizations.</div>
		<?php
	} else {
	?>
	<div class="alert alert-success">The <strong><?php echo ucfirst($config['template_f']); ?></strong> theme supports customizations.</div>
	<a href="customize.php" class="btn btn-medium btn-blue" target="_blank"><i class="icon-share icon-white"></i> Launch the customizer</a>
		<?php
	}
	?>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'store') ? ' in active' : '';?>" id="t3">
	<h2 class="sub-head-settings">Theme Store</h2>

		<div class="well well-small">Personalize your video site by using a premium theme from the PHPSUGAR's theme collection. Below is a list of the available themes compatible with PHP Melody v<?php echo _PM_VERSION; ?>.</div>
		<hr />
		<div class="pm-themes">
			<?php
 
		$data_serialized = cache_this('get_theme_store_data', 'get_theme_store_data');
		$data = unserialize($data_serialized);

		if (is_array($data) && count($data) > 0) : 

			if ($data['items_count'] > 0) : ?>

			<ul class="row-fluid pm-themes-list">
							<?php 
				foreach ($data['items'] as $k => $theme) : 
					
					$theme_mark_new = false;
					
					if (array_key_exists('pubDate', $theme) && $theme['pubDate'] != '')
					{
						$theme['release_date_timestamp'] = strtotime($theme['pubDate']);
						if ((time() - $theme['release_date_timestamp']) <= 2678400) // a month
						{
							$theme_mark_new = true;
						}
					}
					else
					{
						$theme['release_date_timestamp'] = 0;
					}
				
				?>
				<li class="theme-item">
					<h3><?php echo $theme['title'];?></h3>
									<?php if ($theme_mark_new) : ?>
						<div class="theme-label">NEW</div>
					<?php endif; ?>
									<a href="<?php echo $theme['preview_url'];?>" class="theme-preview" target="_blank" title="Preview <?php echo str_replace('"', '', $theme['title']);?> Theme">
					<img src="<?php echo make_url_https($theme['thumb_url']);?>" alt="Theme Image" border="0" class="theme-thumb" />
					</a>
									<a href="<?php echo $theme['preview_url'];?>" target="_blank" class="btn btn-small btn-link">Preview</a>
									<a href="<?php echo $theme['buy_url'];?>" target="_blank" class="btn btn-small btn-link">Order now</a>
							</li>
				<?php endforeach; ?>
					</ul>
			<?php else : 
				echo pm_alert_danger('No themes available at the moment. <strong><a href="http://www.phpsugar.com/phpmelody_templates.html?utm_source=install_footer" target="_blank">Click here</a></strong> to visit the Theme Store.');
			endif; ?>
		<?php else : 
			echo pm_alert_danger('Sorry, couldn\'t retrieve data from the Theme Store. <strong><a href="http://www.phpsugar.com/phpmelody_templates.html?utm_source=install_footer" target="_blank">Click here</a></strong> to visit the Theme Store.');
		endif;?>
		</div><!--.pm-themes-->


		</div>
		</div>
	</td>
		</tr>
</table>
</div>

<div class="clearfix"></div>

<div id="stack-controls" class="list-controls">
<input name="views_from" type="hidden" value="2"  />
<input type="hidden" name="settings_selected_tab" value="<?php echo ($selected_tab_view != '') ? $selected_tab_view:  't1';?>" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />
<input type="hidden" name="upload-type" value="logo" />

<div class="btn-toolbar">
		<div class="btn-group">
		<button type="submit" name="submit" value="Save" class="btn btn-small btn-success btn-strong">Save changes</button>
	</div>
</div>
</div><!-- #list-controls -->

</div>
</form>

		</div><!-- .content -->
</div><!-- .primary -->
<script type="text/javascript">
$(document).ready(function(){
	$('form[name="sitesettings"]').change(function(){
		phpmelody.prevent_leaving_without_saving = true;
	}).submit(function(){
		phpmelody.prevent_leaving_without_saving = false;
	});
});
</script>
<?php
include('footer.php');
