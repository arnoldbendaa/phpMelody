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
// | Copyright: (c) 2004-2016 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '8';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_colorpicker = 1;
$load_scrolltofixed = 1;
$_page_title = 'Settings';
include('header.php');

//$config	= get_config();

$inputs = array();
$info_msg = '';
$video_sources = a_fetch_video_sources();

if (_MOD_SOCIAL && ! array_key_exists('activity_options', $config))
{
	add_config('activity_options', serialize($default_activity_options));
}

if ($_POST['submit'] == "Save" && ( ! csrfguard_check_referer('_admin_settings')))
{
	$info_msg .= pm_alert_warning('Invalid token or session expired. Please load this page from the menu and try again.');
}
else if ($_POST['submit'] == "Save")
{
	$req_fields = array("contact_mail" => "Contact mail",
						"isnew_days" => "Mark video as 'new' for",
						"ispopular" => "Mark video as 'popular' for",
						"comments_page" => "Comments per page"
					);
	$num_fields = array('isnew_days', 'ispopular', 'comments_page', 'account_activation', 'issmtp', 'player_autoplay', 'player_autobuff', 'default_lang', 
						'player_w', 'player_h', 'player_w_index', 'player_h_index', 'player_w_favs', 'player_h_favs', 'player_w_embed', 'player_h_embed', 
						'mod_article', 'bin_rating_allow_anon_voting', 'maintenance_mode', 'featured_autoplay', 'keyboard_shortcuts', 'disable_indexing', 
						'allow_playlists', 'playlists_limit', 'playlists_items_limit', 'comment_system_native', 'comment_system_facebook', 'comment_system_disqus',
						'stopbadcomments', 'guests_can_comment', 'comm_moderation_level', 'use_hq_vids', 'mod_social', 'allow_user_uploadvideo', 'allow_user_suggestvideo',
						'auto_approve_suggested_videos', 'register_time_to_submit', 'allow_embedding', 'eu_cookie_warning', 'auto_approve_suggested_videos_verified',
						'allow_user_edit_video', 'allow_user_delete_video', 'allow_emojis', 'oauth_facebook', 'oauth_twitter', 'csrfguard'
					);
	
	// set unchecked checkboxes
	$_POST['comment_system_native'] = ( ! array_key_exists('comment_system_native', $_POST)) ? 0 : $_POST['comment_system_native'];
	$_POST['comment_system_facebook'] = ( ! array_key_exists('comment_system_native', $_POST)) ? 0 : $_POST['comment_system_facebook'];
	$_POST['comment_system_disqus'] = ( ! array_key_exists('comment_system_native', $_POST)) ? 0 : $_POST['comment_system_disqus'];
	
	foreach($_POST as $k => $v)
	{
		if($_POST[$k] == '' && in_array($k, $req_fields))
		{
			$info_msg .= pm_alert_warning("'".$req_fields[$k] . "' field cannot be left blank!");
		}
		if(in_array($k, $num_fields))
		{
			$v = (int) $v;
			$v = abs($v);
			$inputs[$k] = $v;
		}
		else if ( ! is_array($v))
		{
			$inputs[$k] = stripslashes($v);
		}
	}
	
	if ( ! $inputs['comment_system_'. $inputs['comment_system_primary']])
	{
		if ($inputs['comment_system_native'])
		{
			$inputs['comment_system_primary'] = 'native';
		}
		else if ($inputs['comment_system_facebook']) 
		{
			$inputs['comment_system_primary'] = 'facebook';
		}
		else if ($inputs['comment_system_disqus']) 
		{
			$inputs['comment_system_primary'] = 'disqus';
		}
	}
	
	
	if (($inputs['comment_system_native'] + $inputs['comment_system_facebook'] + $inputs['comment_system_disqus']) == 0)
	{
		$inputs['comment_system'] = 'off';
	}
	
	$inputs['mail_pass'] = str_replace('&quot;', '"', $inputs['mail_pass']);

	if($inputs['videoads_delay'] == '')
		$inputs['videoads_delay'] = 0;
	switch($inputs['videoads_delay_timespan'])
	{
		case 'minutes':
			$inputs['videoads_delay'] = $inputs['videoads_delay'] * 60;
		break;
		case 'hours':
			$inputs['videoads_delay'] = $inputs['videoads_delay'] * 60 * 60;
		break;
	}

	//preroll_ads_delay
	if($inputs['preroll_ads_delay'] == '')
		$inputs['preroll_ads_delay'] = 0;
	switch($inputs['preroll_ads_delay_timespan'])
	{
		case 'minutes':
			$inputs['preroll_ads_delay'] = $inputs['preroll_ads_delay'] * 60;
		break;
		case 'hours':
			$inputs['preroll_ads_delay'] = $inputs['preroll_ads_delay'] * 60 * 60;
		break;
	}

	//	Template has changed? Clear the Smarty Cache & Compile directories
	if ($inputs['jwplayerskin'] != $config['jwplayerskin'])
	{
		//	empty compile directory
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

		//	empty cache directory
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

	// moderator permissions
	$perms = '';
	// mod_can_manage_users
	$perms .= 'manage_users:';
	$perms .= ($_POST['mod_can_manage_users'] == "1") ? '1' : '0';
	$perms .= ';';
	// mod_can_manage_comments
	$perms .= 'manage_comments:';
	$perms .= ($_POST['mod_can_manage_comments'] == "1") ? '1' : '0';
	$perms .= ';';
	// mod_can_manage_videos
	$perms .= 'manage_videos:';
	$perms .= ($_POST['mod_can_manage_videos'] == "1") ? '1' : '0';
	$perms .= ';';
	$perms .= 'manage_articles:';
	$perms .= ($_POST['mod_can_manage_articles'] == "1") ? '1' : '0';
	$perms .= ';';

	if($info_msg == '')
	{
		update_config('moderator_can', $perms, true);

		if ($inputs['allow_user_uploadvideo_unit'] == 'GB')
		{
			$inputs['allow_user_uploadvideo_bytes'] = (int)$inputs['allow_user_uploadvideo_bytes'] .'G';
		}
		else if ($inputs['allow_user_uploadvideo_unit'] == 'MB')
		{
			$inputs['allow_user_uploadvideo_bytes'] = (int)$inputs['allow_user_uploadvideo_bytes'] .'M';
		}
		else if ($inputs['allow_user_uploadvideo_unit'] == 'KB')
		{
			$inputs['allow_user_uploadvideo_bytes'] = (int)$inputs['allow_user_uploadvideo_bytes'] .'K';
		}
		$inputs['allow_user_uploadvideo_bytes'] = return_bytes($inputs['allow_user_uploadvideo_bytes']);

		$upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
		$post_max_size = return_bytes(ini_get('post_max_size'));

		if (_MOD_SOCIAL)
		{
			$loggables = activity_load_options();

			foreach ($loggables as $activity => $v)
			{
				if (array_key_exists('loggable_activity_'.$activity, $inputs))
				{
					$loggables[$activity] = 1;
					unset($inputs['loggable_activity_'. $activity]);
				}
				else
				{
					$loggables[$activity] = 0;
				}
			}
			update_config('activity_options', serialize($loggables), true);

			unset($loggables);
		}
		$inputs['player_timecolor'] = str_replace('#', '', $inputs['player_timecolor']);
		$inputs['player_bgcolor'] = str_replace('#', '', $inputs['player_bgcolor']);

		foreach ($inputs as $config_name => $config_value)
		{
			if ($config_name != 'submit' && $config_name != 'allow_user_uploadvideo_unit')
			{
				update_config($config_name, $config_value, true);
			}
		}

		if((int) readable_filesize($config['allow_user_uploadvideo_bytes']) != $inputs['allow_user_uploadvideo_bytes']) {


			if ($inputs['allow_user_uploadvideo_bytes'] > $upload_max_filesize || $inputs['allow_user_uploadvideo_bytes'] > $post_max_size)
			{
				//$info_msg = 'It appears that your <strong>Max. upload size</strong> (Under "User Settings") is greater than your <a href="sys_phpinfo.php">PHP configuration</a> allows.<strong>Contact your hosting provider and ask them to increase "<em>upload_max_filesize</em>" and "<em>post_max_size</em>" to match your requirements.</strong>';

				// change back to old value
				$inputs['allow_user_uploadvideo_bytes'] = $config['allow_user_uploadvideo_bytes'];
			}
		}

		$player_config = "{embedded: true,
							showOnLoadBegin: true,
							useHwScaling: false,
							menuItems: [false, false, true, true, true, false, false],
							timeDisplayFontColor: '0x". $inputs['player_timecolor'] ."',
							controlBarBackgroundColor: '0x". $inputs['player_bgcolor'] ."',
							progressBarColor2: '0x000000',
							progressBarColor1: '0xFFFFFF',
							controlsOverVideo: 'locked',
							controlBarGloss: 'high',
							initialScale: 'fit',
							hideControls: false,
							autoPlay: false,
							autoBuffering: true,
							watermarkLinkUrl: '". $inputs['player_watermarklink'] ."',
							showWatermark: '". $inputs['player_watermarkshow'] ."',
							watermarkUrl: '". make_url_https($inputs['player_watermarkurl'])  ."',
							playList: [ { overlayId: 'play',
									  name: 'ClickToPlay'
									 },
									 {  linkWindow: '_blank',
										linkUrl: '". _URL ."/watch.php?vid=___UNIQ___',
										url: '". _URL ."/videos.php?vid=___UNIQ___',
										name: ''
									 }]}";

		$player_config = rawurlencode($player_config);
		$player_config = _URL .'/players/flowplayer2/flowplayer.swf?config='. $player_config;

		@chmod(ABSPATH .''. _ADMIN_FOLDER .'/temp/embedparams.xml', 0755);
		if (is_writable(ABSPATH .''. _ADMIN_FOLDER .'/temp/embedparams.xml'))
		{
			$fp = fopen('./temp/embedparams.xml', 'w');
			fwrite($fp, $player_config, strlen($player_config));
			fclose($fp);
		}
		else
		{
			$info_msg .= pm_alert_warning('File "/'. _ADMIN_FOLDER .'/temp/embedparams.xml" is not writable. Please CHMOD this file to 0777 and retry.');
		}
		
		if ($config['video_player'] == 'jwplayer' || $_POST['video_player'] == 'jwplayer')
		{
			//@chmod(ABSPATH .'jwembed.xml', 0755);
			@chmod(ABSPATH .'players/jwplayer5/jwembed.xml', 0755); // @since v2.2
			if (file_exists(ABSPATH .'players/jwplayer5/jwembed.xml') && is_writable(ABSPATH .'players/jwplayer5/jwembed.xml'))
			{
				$write_this = '';
				$write_this .= "<config>\n";
				$write_this .= " <backcolor>". $inputs['player_bgcolor'] ."</backcolor>\n";
				$write_this .= " <frontcolor>". $inputs['player_timecolor'] ."</frontcolor>\n";
				$write_this .= " <screencolor>000000</screencolor>\n";
				$write_this .= " <controlbar>over</controlbar>\n";
				$write_this .= " <bufferlength>5</bufferlength>\n";
				$write_this .= " <autostart>false</autostart>\n";
				$write_this .= " <logo>". make_url_https($inputs['player_watermarkurl']) ."</logo>\n";
				$write_this .= " <link>". $inputs['player_watermarklink'] ."</link>\n";
				$write_this .= '</config>';

				$fp = fopen(ABSPATH .'players/jwplayer5/jwembed.xml', 'w');
				fwrite($fp, $write_this, strlen($write_this));
				fclose($fp);
			}
			else
			{
				$info_msg .= pm_alert_warning('File "/players/jwplayer5/jwembed.xml" is not writable. Please CHMOD this file to 0777 and retry.');
			}
		}
	}

	//	Update video sources too.
	foreach ($_POST['user_choice'] as $source_id => $user_choice)
	{
		if ($user_choice != $video_sources[$source_id]['user_choice'])
		{
			$sql = "UPDATE pm_sources
					SET user_choice = '". $user_choice ."'
					WHERE source_id = '". $source_id ."'";
			mysql_query($sql);
		}
	}

	// refresh display data
	$video_sources = a_fetch_video_sources();

	if ($inputs['spambot_prevention'] == 'recaptcha' && (empty($inputs['recaptcha_public_key']) || empty($inputs['recaptcha_private_key'])))
	{
		$info_msg .= pm_alert_warning('reCAPTCHA requires both a public and a private key. You can get them for free by signing up at <a href="http://www.google.com/recaptcha/intro/index.html" target="_blank">http://www.google.com/recaptcha/intro/index.html</a>.');
	}
	
	$allowed_zones = timezone_identifiers_list();
	if ($inputs['timezone'] != '' && ! in_array( $inputs['timezone'], $allowed_zones ))
	{
		$info_msg .= pm_alert_warning('The timezone you have entered is not valid. Please select a valid timezone.');
	}
	else
	{
		date_default_timezone_set($inputs['timezone']);
	}

	//	Update HTML COUNTER / Analytics
	if (!empty($_POST['htmlcode']))
	{
		$htmlcode = (get_magic_quotes_gpc()) ? stripslashes($_POST['htmlcode']) : $_POST['htmlcode'];
		
		$result = update_config('counterhtml', $htmlcode);// update_config does secure_sql()
		$current_counter = stripslashes($htmlcode);
		$config['counterhtml'] = $current_counter;
	} else {
		$result = update_config('counterhtml', $htmlcode);// update_config does secure_sql()
		$config['counterhtml'] = $htmlcode;
	}
	$config['mail_pass'] = stripslashes($config['mail_pass']);
}

$mod_can = mod_can();

$selected_tab_view = '';
$page_tab_views = array('tabname1', 't1', 't2', 't3', 't4', 't5', 't6', 't7', 't8', 't9', 't10',
						'general', 'modules', 'player', 'video', 'sources', 'video-ads', 'comment', 'email', 'user');
if ($_POST['settings_selected_tab'] != '' || $_GET['view'] != '')
{
	$selected_tab_view = ($_POST['settings_selected_tab'] != '') ? $_POST['settings_selected_tab'] : $_GET['view'];
	if ( ! in_array($selected_tab_view, $page_tab_views)) 
	{
		$selected_tab_view = '';
	}
}

$highlight_fields = array();	// @todo
if ($_GET['highlight'] != '')
{
	$highlight_fields = explode(',', $_GET['highlight']);
}

?>
<div id="adminPrimary">
    <div class="content">

<form name="sitesettings" method="post" action="settings.php">
<?php echo csrfguard_form('_admin_settings'); ?>
        <div id="settings-jump"></div>
        <nav id="import-nav" class="tabbable" role="navigation">
        <h2 class="h2-import pull-left">Settings</h2>
            <ul class="nav nav-tabs pull-right">
	            <li class="<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == '' || $selected_tab_view == 'general') ? 'active' : '';?>"><a href="#tabname1" data-toggle="tab" class="tab-pane">General Settings</a></li>
	            <li class="<?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? 'active' : '';?>"><a href="#t6" data-toggle="tab" class="tab-pane<?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? ' active' : '';?>">Modules</a></li>
	            <li class="<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? 'active' : '';?>"><a data-toggle="tab" href="#t2" class="tab-pane<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? ' active' : '';?>">Video Player</a></li>
	            <li class="<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? 'active' : '';?>"><a data-toggle="tab" href="#t3" class="tab-pane<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? ' active' : '';?>">Video Settings</a></li>
	            <li class="<?php echo ($selected_tab_view == 't8' || $selected_tab_view == 'sources') ? 'active' : '';?>"><a data-toggle="tab" href="#t8" class="tab-pane<?php echo ($selected_tab_view == 't8' || $selected_tab_view == 'sources') ? ' active' : '';?>">Video Sources</a></li>
	            <li class="<?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? 'active' : '';?>"><a data-toggle="tab" href="#t5" class="tab-pane<?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? ' active' : '';?>">Video Ads</a></li>
		    <li class="<?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? 'active' : '';?>"><a data-toggle="tab" href="#t10" class="tab-pane<?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? ' active' : '';?>">Comments</a></li>
	            <li class="<?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? 'active' : '';?>"><a data-toggle="tab" href="#t7" class="tab-pane<?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? ' active' : '';?>">E-mail</a></li>
	            <li class="<?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? 'active' : '';?>"><a data-toggle="tab" href="#t9" class="tab-pane<?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? ' active' : '';?>">Users</a></li>
            </ul>
        </nav>
		<div style="clear:both"></div>

<?php if ($info_msg != '') : ?>
	<br />
	<?php echo $info_msg; ?>
<?php endif; ?>

<?php if ($_POST['submit'] == "Save" && $info_msg == '') : ?>
	<br />
	<?php echo pm_alert_success('The new settings have been saved and applied.'); ?>
<?php endif; ?>

<?php if ($config['mod_article'] == '0' && $config['mod_social'] == '0' && $config['firstinstall'] > ($time_now - 259200)) : // display info message only in the first 3 days ?>
	<br />
	<?php echo pm_alert_info('The "Article Module" and "Social Module" are disabled by default. To enable any available modules, see <a href="settings.php?view=modules"><strong>Modules</strong>.</a>'); ?>
<?php endif; ?>

<div>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td>
		</td>
	</tr>
    <td valign="top">
	<div class="tab-content">
	<div class="tab-pane fade<?php echo ($selected_tab_view == 'tabname1' || $selected_tab_view == 't1' || $selected_tab_view == '' || $selected_tab_view == 'general') ? ' in active' : '';?>" id="tabname1">
  	<h2 class="sub-head-settings">General Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
		<tr>
			<td width="20%">Site title</td>
			<td>
				<input name="homepage_title" type="text"  size="45" value="<?php echo htmlspecialchars(stripslashes($config['homepage_title'])); ?>" />
			</td>
		</tr>
	  <tr>
        <td>Default language</td>
        <td>
			<select name="default_lang">
			<?php
			 foreach($langs as $lang_id => $lang_arr)
			 {
			 	if($lang_id == $config['default_lang'])
				{
					echo '<option value="'.$lang_id.'" selected="selected">'.$lang_arr['title'].'</option>';
				}
				else
				{
					echo '<option value="'.$lang_id.'">'.$lang_arr['title'].'</option>';
				}
			 }
			?>
			</select>
          </td>
        </tr>
	  <tr>
        <td>Default timezone</td>
        <td>
			<select name="timezone">
				<?php  echo pm_timezone_select($config['timezone']); ?>
			</select>
			<br />
			<small>Server time: <?php echo date('Y-m-d H:i:s'); ?></small>
          </td>
      </tr>
	  <tr>
        <td>Use SEO friendly URLs</td>
        <td>
		<label><input name="seomod" type="radio" value="1" <?php echo ($config['seomod']==1) ? 'checked="checked"' : "";?> /> Yes</label>
		<label><input name="seomod" type="radio" value="0" <?php echo ($config['seomod']==0) ? 'checked="checked"' : "";?> /> No</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Your server must support <strong>mod_rewrite</strong> commands. Once enabled, all the URLs will transform from a dynamic appearance to a static one. This may improve the search engine rankings. <br><br><strong>Warning:</strong> don't update this setting once your website has been indexed into the search engines."><i class="icon-info-sign"></i></a>
        </td>
      </tr>
	  <tr>
        <td>Discourage search engines from indexing this site</td>
        <td>
		<label><input name="disable_indexing" type="radio" value="1" <?php echo ($config['disable_indexing']==1) ? 'checked="checked"' : "";?> /> Yes</label>
		<label><input name="disable_indexing" type="radio" value="0" <?php echo ($config['disable_indexing']==0) ? 'checked="checked"' : "";?> /> No</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="It is up to search engines to honor this request."><i class="icon-info-sign"></i></a>
        </td>
      </tr>

      <tr>
        <td>Show video thumbnails from</td>
        <td>
		<label><input name="thumb_from" type="radio" value="1" <?php echo ($config['thumb_from']==1) ? 'checked="checked"' : "";?> /> Remote</label>
		<label><input name="thumb_from" type="radio" value="2" <?php echo ($config['thumb_from']==2) ? 'checked="checked"' : "";?> /> Local</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="'<strong>Local</strong>' = thumbnails from imported videos <strong>are saved</strong> locally.<br><br>'<strong>Remote</strong>' = thumbnails from imported videos <strong>are NOT saved</strong> locally.<br><br> To avoid missing thumbnails, once this option is set to '<strong>Remote</strong>', <strong>it should not be changed again</strong> because videos imported while the 'Remote' option was active will not have local thumbnails. <br><br><strong>Recommended:</strong> Use the 'Local' option."><i class="icon-info-sign"></i></a>
        </td>
    </tr>
	  <tr> 
        <td>Use thumbnail size</td>
        <td>
        	<select name="download_thumb_res">
				<option value="small" <?php echo ($config['download_thumb_res'] == 'small') ? 'selected="selected"' : ''; ?>>Small (120x90 pixels)</option>
				<option value="medium" <?php echo ($config['download_thumb_res'] == 'medium' || $config['download_thumb_res'] == '') ? 'selected="selected"' : ''; ?>>Medium (320x180 pixels)</option>
				<option value="large" <?php echo ($config['download_thumb_res'] == 'large') ? 'selected="selected"' : ''; ?>>Large (480x360 pixels)</option>
				<option value="extra-large" <?php echo ($config['download_thumb_res'] == 'extra-large') ? 'selected="selected"' : ''; ?>>Extra Large (640x480 pixels)</option>
				<option value="original" <?php echo ($config['download_thumb_res'] == 'original') ? 'selected="selected"' : ''; ?>>Maximum (1280x720 pixels)</option>
			</select>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Assign the preferred thumbnail resolution/size for imported videos (Youtube, Dailymotion and Vimeo).<br /><strong>Note:</strong> If the 'Extra Large' thumbnail is not available, PHP Melody will use the next best thumbnail available. <br><br><strong>Default:</strong> 'Medium'"><i class="icon-info-sign"></i></a> 
        </td>
      </tr>
	  <tr>
		  <td>Maintenance mode</td>
		  <td>
		  	<label><input name="maintenance_mode" type="radio" value="1" <?php echo ($config['maintenance_mode']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
		  	<label><input name="maintenance_mode" type="radio" value="0" <?php echo ($config['maintenance_mode']==0) ? 'checked="checked"' : "";?> /> Disabled</label> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Put your site in 'Maintenance mode' if you want to perform updates or layout changes. Users will see a short 'Maintenance mode' message but you can define a custom message below. Once your site is ready to be made available again, simple check the 'Disabled' box.<br><strong>Note</strong>: All administrator and moderators will be able to browse the site when it is in 'Maintenance mode' (as usual)."><i class="icon-info-sign"></i></a>
          </td>
	  </tr>
	  <tr>
		  <td>Maintenance mode message</td>
		  <td>
		  	<input type="text" name="maintenance_display_message" value="<?php echo $config['maintenance_display_message'];?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Define a custom message for your visitors while your site is in 'Maintenance mode'. If left empty a generic 'Maintenance mode' message will be provided instead."><i class="icon-info-sign"></i></a>
          </td>
	  </tr>

	  <tr>
		  <td>EU cookie notification</td>
		  <td>
		  	<label><input name="eu_cookie_warning" type="radio" value="1" <?php echo ($config['eu_cookie_warning']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
		  	<label><input name="eu_cookie_warning" type="radio" value="0" <?php echo ($config['eu_cookie_warning']==0) ? 'checked="checked"' : "";?> /> Disabled</label> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="All websites owned in the EU or targeted towards EU citizens, are now expected to comply with the 'EU Cookie' law."><i class="icon-info-sign"></i></a>
          </td>
	  </tr>

	<?php if ($config['eu_cookie_warning']==1) : ?>
	  <tr>
		  <td>EU cookie notification placement</td>
		  <td>
		  	<label><input name="eu_cookie_warning_position" type="radio" value="floating" <?php echo ($config['eu_cookie_warning_position']=='floating') ? 'checked="checked"' : "";?> /> Floating</label>
		  	<label><input name="eu_cookie_warning_position" type="radio" value="top" <?php echo ($config['eu_cookie_warning_position']=='top') ? 'checked="checked"' : "";?> /> Top</label> 
		  	<label><input name="eu_cookie_warning_position" type="radio" value="bottom" <?php echo ($config['eu_cookie_warning_position']=='bottom') ? 'checked="checked"' : "";?> /> Bottom</label>
          </td>
	  </tr>
	<?php endif; ?>
	  <tr>
		  <td>CSRF form protection</td>
		  <td>
			<label><input name="csrfguard" type="radio" value="1" <?php echo ($config['csrfguard']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
			<label><input name="csrfguard" type="radio" value="0" <?php echo ($config['csrfguard']==0) ? 'checked="checked"' : "";?> /> Disabled</label> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Protects forms and URLs from several types of malicious attacks including cross-site request forgery (CSRF)."><i class="icon-info-sign"></i></a>
		  </td>
	  </tr>	

    </table>
    
    <h2 class="sub-head-settings">Admin Area Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	  <tr>
		  <td width="20%" valign="top">Keyboard Shortcuts</td>
		  <td>
		  	<label><input name="keyboard_shortcuts" type="radio" value="1" <?php echo ($config['keyboard_shortcuts']==1) ? 'checked="checked"' : "";?> /> Enabled</label> 
		  	<label><input name="keyboard_shortcuts" type="radio" value="0" <?php echo ($config['keyboard_shortcuts']==0) ? 'checked="checked"' : "";?> /> Disabled</label> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Once enabled, press SHIFT+/ to see a list of the available keyboard shortcuts commands."><i class="icon-info-sign"></i></a>
          </td>
	  </tr>
	  <tr>
		  <td width="20%" valign="top">Color Scheme</td>
		  <td>
		  	<label><input name="admin_color_scheme" type="radio" value="default" <?php echo ($config['admin_color_scheme'] == 'default') ? 'checked="checked"' : "";?> /> <div class="pm-color-scheme-sprite default"></div> Default</label>
		  	<label><input name="admin_color_scheme" type="radio" value="cherry" <?php echo ($config['admin_color_scheme'] == 'cherry') ? 'checked="checked"' : "";?> /> <div class="pm-color-scheme-sprite cherry"></div> Cherry</label>
		  	<label><input name="admin_color_scheme" type="radio" value="coffee" <?php echo ($config['admin_color_scheme'] == 'coffee') ? 'checked="checked"' : "";?> /> <div class="pm-color-scheme-sprite coffee"></div> Coffee</label>
		  	<label><input name="admin_color_scheme" type="radio" value="bluesky" <?php echo ($config['admin_color_scheme'] == 'bluesky') ? 'checked="checked"' : "";?> /> <div class="pm-color-scheme-sprite bluesky"></div> Blue Sky</label>
		  	<label><input name="admin_color_scheme" type="radio" value="sunset" <?php echo ($config['admin_color_scheme'] == 'sunset') ? 'checked="checked"' : "";?> /> <div class="pm-color-scheme-sprite sunset"></div> Sunset</label>
		  	<label><input name="admin_color_scheme" type="radio" value="blacknwhite" <?php echo ($config['admin_color_scheme'] == 'blacknwhite') ? 'checked="checked"' : "";?> /> <div class="pm-color-scheme-sprite blacknwhite"></div> Black'n'White</label>
          </td>
	  </tr>

    </table>

    <h2 class="sub-head-settings">Analytics/Tracking Code</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
          <td width="20%" valign="top">HTML code</td>
          <td>
             <textarea name="htmlcode" rows="3" cols="55"><?php echo $config['counterhtml']; ?></textarea>
             <span class="helptext"><small>This tracking code is inserted in the footer</small></span>
          </td>
      </tr>
    </table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't2' || $selected_tab_view == 'player') ? ' in active' : '';?>" id="t2">
	<h2 class="sub-head-settings">Video Player Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	  <tr>
        <td>Default video player</td>
        <td>
		<label><input name="video_player" type="radio" value="embed" <?php echo ($config['video_player']=='embed') ? 'checked="checked"' : "";?> /> Original Player</label>
		<label><input name="video_player" type="radio" value="flvplayer" <?php echo ($config['video_player']=='flvplayer') ? 'checked="checked"' : "";?> /> FlowPlayer</label>
		<label><input name="video_player" type="radio" value="jwplayer" <?php echo ($config['video_player']=='jwplayer') ? 'checked="checked"' : "";?> /> JW Player 5</label>
		<label><input name="video_player" type="radio" value="jwplayer6" <?php echo ($config['video_player']=='jwplayer6') ? 'checked="checked"' : "";?> /> JW Player 6</label>
		<label><input name="video_player" type="radio" value="jwplayer7" <?php echo ($config['video_player']=='jwplayer7') ? 'checked="checked"' : "";?> /> JW Player 7</label>
		<label><input name="video_player" type="radio" value="videojs" <?php echo ($config['video_player']=='videojs') ? 'checked="checked"' : "";?> /> Video JS</label>
		</td>
      </tr>

	  <?php if($config['video_player']=='jwplayer7') : ?>
      <tr>
        <td width="20%">JW Player 7 license key</td>
        <td>
			<input id="jwplayer7key" name="jwplayer7key" type="text" size="8" value="<?php echo $config['jwplayer7key']; ?>" style="width: 150px;" />
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="JW Player 7 requires a license key. Create your own account and key on www.jwplayer.com."><i class="icon-info-sign"></i></a>
			<?php if(!$config['jwplayer7key']) : ?><span class="label label-warning">Required</span><?php endif; ?>
			<a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank">Get a key</a>
			</td>
      </tr>
	  <?php endif; ?>
	  <?php if($config['video_player']=='jwplayer6') : ?>
      <tr>
        <td width="20%">JW Player 6 license key</td>
        <td>
			<input id="jwplayerkey" name="jwplayerkey" type="text" size="8" value="<?php echo $config['jwplayerkey']; ?>" style="width: 150px;" />
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="If you purchased the Pro, Premium or Ads edition of JW Player 6, unlock its features of the by inserting your JW Player license key. Otherwise, leave this field blank."><i class="icon-info-sign"></i></a>
			</td>
      </tr>
	  <?php endif; ?>
	  <?php if($config['video_player']=='jwplayer') : ?>
      <tr>
        <td width="20%">JW Player 5 skin</td>
        <td>
			<select name="jwplayerskin">
			<option value="<?php echo $config['jwplayerskin']; ?>" selected="selected"><?php echo ucfirst(trim($config['jwplayerskin'], ".zip")); ?></option>
			<option></option>
			<?php echo dropdown_jwskins(); ?>
			</select>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="JW Player skins come with their own color scheme which cannot be edited below."><i class="icon-info-sign"></i></a>
			</td>
        </tr>
	  <?php endif; ?>
	  <tr>
        <td>Progress bar background color</td>
        <td><input id="bg_bar" name="player_bgcolor" type="text" size="14" value="#<?php echo $config['player_bgcolor'];?>" style="width: 50px;"/></td>
      </tr>
      <tr>
        <td>Video text color</td>
        <td><input id="play_timer" name="player_timecolor" type="text" size="8" value="#<?php echo $config['player_timecolor']; ?>" style="width: 50px;" /></td>
      </tr>
	  <tr>
        <td>Default player size</td>
        <td><input type="text" name="player_w" size="4" maxlength="4" class="span1"value="<?php echo $config['player_w'];?>" /> x <input type="text" name="player_h" size="4" maxlength="4" class="span1" value="<?php echo $config['player_h'];?>" /> px		</td>
      </tr>
	  <tr>
        <td>Hompage player size</td>
        <td><input type="text" name="player_w_index" size="4" maxlength="4" class="span1"value="<?php echo $config['player_w_index'];?>" /> x <input type="text" name="player_h_index" size="4" maxlength="4" class="span1"value="<?php echo $config['player_h_index'];?>" /> px		</td>
      </tr>
	  <tr>
        <td>Embed player size</td>
        <td>
		<input type="text" name="player_w_embed" size="4" maxlength="4" class="span1" value="<?php echo $config['player_w_embed'];?>" /> x <input type="text" name="player_h_embed" size="4" maxlength="4" class="span1" value="<?php echo $config['player_h_embed'];?>" /> px		</td>
      </tr>
	  <tr>
        <td>Play videos in</td>
        <td><label><input name="use_hq_vids" type="radio" value="1" <?php echo ($config['use_hq_vids']==1) ? 'checked="checked"' : "";?> /> High Quality</label>
		<label><input name="use_hq_vids" type="radio" value="0" <?php echo ($config['use_hq_vids']==0) ? 'checked="checked"' : "";?> /> Low Quality</label>
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This feature applies selectively depending on the video source."><i class="icon-info-sign"></i></a>
        </td>
      </tr>
      <tr>
        <td width="20%">Autoplay videos</td>
        <td>
		<label><input name="player_autoplay" type="radio" value="1" <?php echo ($config['player_autoplay']==1) ? 'checked="checked"' : "";?> /> On</label>
		<label><input name="player_autoplay" type="radio" value="0" <?php echo ($config['player_autoplay']==0) ? 'checked="checked"' : "";?> /> Off</label>
        </td>
        </tr>
        <tr>
        <td width="20%">Autoplay featured videos</td>
        <td>
		<label><input name="featured_autoplay" type="radio" value="1" <?php echo ($config['featured_autoplay']==1) ? 'checked="checked"' : "";?> /> On</label>
		<label><input name="featured_autoplay" type="radio" value="0" <?php echo ($config['featured_autoplay']==0) ? 'checked="checked"' : "";?> /> Off</label>
        <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This feature allows you to disable/enable autoplay for videos on your homepage separately"><i class="icon-info-sign"></i></a>

        </td>
        </tr>
      <tr>
        <td>Video pre-buffering</td>
        <td>
		<label><input name="player_autobuff" type="radio" value="1" <?php echo ($config['player_autobuff']==1) ? 'checked="checked"' : "";?> /> On</label>
		<label><input name="player_autobuff" type="radio" value="0" <?php echo ($config['player_autobuff']==0) ? 'checked="checked"' : "";?> /> Off</label>		</td>
        </tr>
      <tr>
        <td>Use watermark</td>
        <td>
		<label><input name="player_watermarkshow" type="radio" value="always" <?php echo ($config['player_watermarkshow']=="always") ? 'checked="checked"' : "";?> /> Always</label>
		<label><input name="player_watermarkshow" type="radio" value="fullscreen" <?php echo ($config['player_watermarkshow']=="fullscreen") ? 'checked="checked"' : "";?> />
		Only when fullscreen</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Watermarks can only be shown in Flowplayer and JW Player (<em>paid version</em>). Watermarks cannot be applied to external players."><i class="icon-info-sign"></i></a>		</td>
      </tr>
      <tr>
        <td>Watermark image URL</td>
        <td><input name="player_watermarkurl" type="text" value="<?php echo $config['player_watermarkurl']; ?>" placeholder="http://" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Insert the full URL to the image you want to use as a watermark (Image types supported: JPG, GIF, PNG). To disable the watermark please leave this field empty. <br> Note: this works for JW Player Commercial Edition or Flowplayer"><i class="icon-info-sign"></i></a></td>
      </tr>
      <tr>
        <td>Watermark link</td>
        <td><input name="player_watermarklink" type="text" value="<?php echo $config['player_watermarklink']; ?>" placeholder="http://" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Clicking the watermark can take the visitor to a desired location. Please enter that location (Complete URL)."><i class="icon-info-sign"></i></a></td>
      </tr>
    </table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't3' || $selected_tab_view == 'video') ? ' in active' : '';?>" id="t3">
	<h2 class="sub-head-settings" >Video Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
        <td width="20%">Allow video embedding (site-wide setting)</td>
        <td>
        	<label><input name="allow_embedding" type="radio" value="1" <?php echo ($config['allow_embedding'] == '1') ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="allow_embedding" type="radio" value="0" <?php echo ($config['allow_embedding'] == '0') ? 'checked="checked"' : "";?> /> No</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This setting allows you to control video embedding site-wide."><i class="icon-info-sign"></i></a>
		</td>
      </tr>
	  <tr>
        <td width="20%">Mark video as 'new' for the first</td>
        <td><input name="isnew_days" type="text" size="8" class="span1" value="<?php echo $config['isnew_days']; ?>" /> days</td>
        </tr>
      <tr>
        <td>Mark video as 'popular' after</td>
        <td><input name="ispopular" type="text" size="8" class="span1" value="<?php echo $config['ispopular']; ?>" /> views</td>
        </tr>
		<tr>
		<td>Mark video as 'featured' after
		<td><input name="auto_feature" type="text" size="8" class="span1" value="<?php echo ($config['auto_feature'] != '') ? $config['auto_feature'] : 0; ?>" /> views <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Automatically mark a video as 'Featured' when reaching this number of views. Set to 0 (zero) to disable this feature."><i class="icon-info-sign"></i></a></td>
	</tr>
    </table>

	<h2 class="sub-head-settings">Video Importing API Keys</h2>

	<?php if ( empty($config['youtube_api_key']) ) : ?>
		<div class="alert alert-help">
			<strong>Before importing videos from YouTube.com...</strong> 
			<p>To import videos from YouTube.com, an API key is required. <strong><a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank">Watch the video</a></strong> and see how to create your API key.</p>
			<p>Enable YouTube importing by adding your API key below.</p>
		</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
		<tr>
			<td width="20%">
				Youtube Public API Key
			</td>
			<td>
				<input type="text" name="youtube_api_key" class="input-xlarge" value="<?php echo $config['youtube_api_key'];?>" />
				<a href="http://help.phpmelody.com/how-to-create-a-youtube-api-key/" target="_blank" rel="popover" data-placement="right" data-trigger="hover" data-content="Click on this icon to learn how to create your own YouTube API key."><i class="icon-info-sign"></i></a> <a href="https://console.developers.google.com" target="_blank">Get key</a>
			</td>
		</tr>
		<tr>
			<td width="20%">
				Vimeo API Access Token
			</td>
			<td>
				<input type="text" name="vimeo_api_token" class="input-xlarge" value="<?php echo $config['vimeo_api_token'];?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="To use the Vimeo video importing features you need an API key from Vimeo.com. Please visit https://developer.vimeo.com/api for more details."><i class="icon-info-sign"></i></a> <a href="https://developer.vimeo.com/api" target="_blank">Get key</a>
			</td>
		</tr>
	</table>
    </div>
	<div class="tab-pane fade<?php echo ($selected_tab_view == 't10' || $selected_tab_view == 'comment') ? ' in active' : '';?>" id="t10">
	<h2 class="sub-head-settings" >Comment Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	 <tr>
		<td width="20%">Allow comments (site-wide setting)</td>
		<td>
		<label><input name="comment_system" type="radio" value="on" <?php echo ($config['comment_system'] == 'on') ? 'checked="checked"' : "";?> /> Yes</label>
		<label><input name="comment_system" type="radio" value="off" <?php echo ($config['comment_system'] == 'off') ? 'checked="checked"' : "";?> /> No</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This setting allows you to turn the commenting system ON or OFF site-wide."><i class="icon-info-sign"></i></a>
	 </tr>
	 <tr>
		<td width="20%">Enabled comment systems</td>
		<td>
		<label><input name="comment_system_native" type="checkbox" value="1" <?php echo ($config['comment_system_native']) ? 'checked="checked"' : "";?> /> PHP Melody</label>
		<label><input name="comment_system_facebook" type="checkbox" value="1" <?php echo ($config['comment_system_facebook']) ? 'checked="checked"' : "";?> /> Facebook</label>
		<label><input name="comment_system_disqus" type="checkbox" value="1" <?php echo ($config['comment_system_disqus']) ? 'checked="checked"' : "";?> /> Disqus</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Enable 3rd party comment services such as Disqus and Facebook Comments."><i class="icon-info-sign"></i></a>
	 </tr>
	 <tr>
		<td width="20%">Primary comment system</td>
		<td>
		<label><input name="comment_system_primary" type="radio" value="native" <?php echo ($config['comment_system_primary'] == 'native') ? 'checked="checked"' : "";?> /> PHP Melody</label>
		<label><input name="comment_system_primary" type="radio" value="facebook" <?php echo ($config['comment_system_primary'] == 'facebook') ? 'checked="checked"' : "";?> /> Facebook</label>
		<label><input name="comment_system_primary" type="radio" value="disqus" <?php echo ($config['comment_system_primary'] == 'disqus') ? 'checked="checked"' : "";?> /> Disqus</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="The primary commenting system will always appear first on your website."><i class="icon-info-sign"></i></a>
	 </tr>
	 <tr>
		<td width="20%">Allow emojis</td>
		<td>
		<label><input name="allow_emojis" type="radio" value="1" <?php echo ($config['allow_emojis'] == 1) ? 'checked="checked"' : "";?> /> Yes</label>
		<label><input name="allow_emojis" type="radio" value="0" <?php echo ($config['allow_emojis'] == 0) ? 'checked="checked"' : "";?> /> No</label>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This setting allows you to turn the Emoji support ON or OFF site-wide."><i class="icon-info-sign"></i></a>
	 </tr>
	 <tr>
		<td>Comments per page</td>
		<td><input name="comments_page" type="text" size="8" class="span1" value="<?php echo $config['comments_page']; ?>" />		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Limit the number of comments displayed per page."><i class="icon-info-sign"></i></a></td>
	 </tr>
	 <tr class="comment-options-tr">
		<td width="20%">Block bad comments</td>
		<td>
		<label><input name="stopbadcomments" type="radio" value="1" <?php echo ($config['stopbadcomments']==1) ? 'checked="checked"' : "";?> /> Yes</label>
		<label><input name="stopbadcomments" type="radio" value="0" <?php echo ($config['stopbadcomments']==0) ? 'checked="checked"' : "";?> /> No</label>		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Filter out the bad comments by editing the 'Blacklist' of unallowed words. Comments containing those words won't be added to the database."><i class="icon-info-sign"></i></a>		</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Allow comments from</td>
		<td>
		<label><input name="guests_can_comment" type="radio" value="1" <?php echo ($config['guests_can_comment']==1) ? 'checked="checked"' : "";?> /> Anyone</label>
		<label><input name="guests_can_comment" type="radio" value="0" <?php echo ($config['guests_can_comment']==0) ? 'checked="checked"' : "";?> /> Registered users only</label>	</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Comment moderation</td>
		<td>
		<label><input name="comm_moderation_level" type="radio" value="0" <?php echo ($config['comm_moderation_level']==0) ? 'checked="checked"' : "";?> /> Disabled</label>
		<label><input name="comm_moderation_level" type="radio" value="1" <?php echo ($config['comm_moderation_level']==1) ? 'checked="checked"' : "";?> /> Moderate guest comments only</label>
		<label><input name="comm_moderation_level" type="radio" value="2" <?php echo ($config['comm_moderation_level']==2) ? 'checked="checked"' : "";?> /> Moderate all comments</label>	</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Default sorting</td>
		<td>
		<label><input name="comment_default_sort" type="radio" value="added" <?php echo ($config['comment_default_sort']=='added') ? 'checked="checked"' : "";?> /> Most recent first</label>
		<label><input name="comment_default_sort" type="radio" value="score" <?php echo ($config['comment_default_sort']=='score') ? 'checked="checked"' : "";?> /> Most liked first</label>
		</td>
	 </tr>
	 <tr class="comment-options-tr">
		<td valign="top">Dislikes threshold</td>
		<td>
		<input type="text" name="comment_rating_hide_threshold" size="8" class="span1" value="<?php echo $config['comment_rating_hide_threshold']; ?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Minimum number of dislikes to mute a comment."><i class="icon-info-sign"></i></a>
		</td>
	 </tr> 
	</table>

	<h2 class="sub-head-settings">Disqus Comment Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	 <tr class="disqus-comment-options-tr">
		<td valign="top" width="20%">Disqus shortname</td>
		<td>
		<input type="text" name="disqus_shortname"  value="<?php echo $config['disqus_shortname']; ?>" />
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="This is required to enable the Disqus comments on your website.<br />To find out your shortname, log in your Disqus account and go to Settings / General / Site Identity."><i class="icon-info-sign"></i></a>
		</td>
	 </tr>
	</table>

	<h2 class="sub-head-settings">Facebook Comment Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	 <tr class="fb-comment-options-tr">
		<td valign="top" width="20%">Facebook comment sorting</td>
		<td>
		<label><input name="fb_comment_sorting" type="radio" value="social" <?php echo ($config['fb_comment_sorting'] == 'social') ? 'checked="checked"' : "";?> /> Social (default)</label><br />
		<label><input name="fb_comment_sorting" type="radio" value="time" <?php echo ($config['fb_comment_sorting'] == 'time') ? 'checked="checked"' : "";?> /> Oldest - Newest</label><br />
		<label><input name="fb_comment_sorting" type="radio" value="reverse_time" <?php echo ($config['fb_comment_sorting'] == 'reverse_time') ? 'checked="checked"' : "";?> /> Newest - Oldest</label>
		</td>
		</td>
	 </tr>
	 <tr class="fb-comment-options-tr">
		<td valign="top">Facebook APP ID</td>
		<td>
			<input type="text" name="fb_app_id" value="<?php echo $config['fb_app_id']; ?>" />
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Define your Facebook App ID to moderate comments right on your website."><i class="icon-info-sign"></i></a>
		</td>
		</td>
	 </tr>
	</table>
	</div>

   <div class="tab-pane fade<?php echo ($selected_tab_view == 't5' || $selected_tab_view == 'video-ads') ? ' in active' : '';?>" id="t5">
   <h2 class="sub-head-settings" >Video Ads Settings</h2>
   <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
      <tr>
        <td width="20%">Set <a href="videoads.php">pre-roll video ads</a> recurrence</td>
        <td><input name="videoads_delay" type="text" size="8" class="span1" value="<?php echo $config['videoads_delay']; ?>" />
		<select name="videoads_delay_timespan" class="input-small">
		 <option value="seconds" <?php if($config['videoads_delay'] > 0) echo 'selected="selected"'; ?>>Seconds</option>
		 <option value="minutes" <?php if($config['videoads_delay'] == 0) echo 'selected="selected"'; ?>>Minutes</option>
		 <option value="hours">Hours</option>
		</select>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Sets the delay between two video ads. If you set the delay to 2 minutes, your visitors will see a video ad every 2 minutes.  Insert <strong>0 (zero)</strong> to disable the limit and show the ads each time a video is played."><i class="icon-info-sign"></i></a></td>
      </tr>
	  <tr>
        <td width="20%">Set <a href="prerollstatic_ad_manager.php">pre-roll static ads</a> recurrence</td>
        <td><input name="preroll_ads_delay" type="text" size="8" class="span1" value="<?php echo $config['preroll_ads_delay']; ?>" />
		<select name="preroll_ads_delay_timespan" class="input-small">
		 <option value="seconds" <?php if($config['preroll_ads_delay'] > 0) echo 'selected="selected"'; ?>>Seconds</option>
		 <option value="minutes" <?php if($config['preroll_ads_delay'] == 0) echo 'selected="selected"'; ?>>Minutes</option>
		 <option value="hours">Hours</option>
		</select>
		<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Sets the delay between two pre-roll static ads. If you set the delay to 2 minutes, your visitors will see a pre-roll static ad every 2 minutes. Insert <strong>0 (zero)</strong> to disable the limit and show the ads each time a video is played."><i class="icon-info-sign"></i></a></td>
      </tr>
    </table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't6' || $selected_tab_view == 'modules') ? ' in active' : '';?>" id="t6">
	
	<h2 class="sub-head-settings" >Article Module Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
 		<tr>
			<td width="20%">Articles Module</td>
			<td>
			  	<label><input name="mod_article" type="radio" value="1" <?php echo ($config['mod_article']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
				<label><input name="mod_article" type="radio" value="0" <?php echo ($config['mod_article']==0) ? 'checked="checked"' : "";?> /> Disabled</label> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Enable this module if you intend to start a blog or an article area on <?php echo htmlspecialchars(_SITENAME); ?>."><i class="icon-info-sign"></i></a>
			</td>
		</tr>
    </table>

	<h2 class="sub-head-settings" >Social Module Settings</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
 		<tr>
			<td width="20%">Social Module</td>
			<td>
			  	<label><input name="mod_social" type="radio" value="1" <?php echo ($config['mod_social']==1) ? 'checked="checked"' : "";?> /> Enabled</label>
				<label><input name="mod_social" type="radio" value="0" <?php echo ($config['mod_social']==0) ? 'checked="checked"' : "";?> /> Disabled</label>
			</td>
		</tr>
		<?php //if ($config['mod_social']) :
		if ( ! function_exists('activity_load_options'))
		{
			include_once(ABSPATH .'include/social_settings.php');
			include_once(ABSPATH .'include/social_functions.php');
		}
		?>
		<tr class="mod-social-options-tr <?php echo ( ! $config['mod_social']) ? 'hide' : ''; ?>">
			<td>
				Following limit
			</td>
			<td>
				<input type="text" name="user_following_limit" size="8" class="span1" value="<?php echo $config['user_following_limit']; ?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Maximum number of users someone can follow."><i class="icon-info-sign"></i></a>
			</td>
		</tr>
		<tr class="mod-social-options-tr <?php echo ( ! $config['mod_social']) ? 'hide' : ''; ?>">
			<td>Log the following activities</td>
			<td>
				<?php


				$loggables = activity_load_options();
				foreach ($loggables as $activity => $value)
				{
					?>
					<label><input type="checkbox" name="loggable_activity_<?php echo $activity;?>" value="1" <?php echo ($value == 1) ? 'checked="checked"' : '';?> /> <?php echo $activity_labels[$activity];?></label>
					<br />

					<?php
				}
				?>
			</td>
		</tr>
		<?php //endif;?>
	</table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't7' || $selected_tab_view == 'email') ? ' in active' : '';?>" id="t7">
	<h2 class="sub-head-settings" >E-mail Settings</h2>
	<?php
	if( $config['mail_server'] == 'mail.domain.com' ) {
		echo pm_alert_danger( "<strong>" . _SITENAME . " cannot send any emails at this time because no email account appears to be set up.</strong>");
		echo pm_alert_info( "For optimal results, use your <em>local mail server</em> instead of 3rd party servers such as Gmail, Hotmail, etc.");
	}

	?>
	<div id="mail_preset_warn"></div>
	<table id="mail_settings" cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	 <tr>
		<td>Choose from existing presets</td>
		<td>
			<div class="qsFilter">
			<div class="btn-group input-prepend">
			<select id="mail_presets">
				<option id="none">- none -</option>
				<option id="gmail">Gmail</option>
				<option id="godaddy">GoDaddy</option>
				<option id="yahoo">Yahoo</option>
			</select>
			</div><!-- .btn-group -->
			</div><!-- .qsFilter -->
		</td>
	 </tr>
	 <tr>
		<td>Mail server</td>
		<td>
		<input name="mail_server" id="mail_server" type="text" size="25" value="<?php echo $config['mail_server']; ?>" /> Port <input name="mail_port" id="mail_port" type="text" size="5" class="span1" value="<?php echo $config['mail_port']; ?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="The mail server port is most likely to be 110 but it can also to be: 25, 26, 465 (GMAIL) and 587 (Yahoo). Please ask your host if you're not sure about this."><i class="icon-info-sign"></i></a>
		</td>
	 </tr>
	 <tr>
		<td>Account login</td>
		<td>
		<input name="mail_user" id="mail_user" type="text" size="25" value="<?php echo $config['mail_user']; ?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="The account login is most likely to be your email address. Please ask your host for details if you need to"><i class="icon-info-sign"></i></a>
		</td>
	 </tr>
	 <tr>
		<td>Account password</td>
		<td>
		<input name="mail_pass" id="mail_pass" type="password" size="25" value="<?php echo str_replace('"', '&quot;', $config['mail_pass']); ?>" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Please avoid using quotation marks ( ' or &quot; ) in your password."><i class="icon-warning-sign"></i></a>
		</td>
	 </tr>
	 <tr>
		<td>Use SMTP protocol for mail</td>
		<td>
		<label><input name="issmtp" type="radio" value="1" <?php echo ($config['issmtp']==1) ? 'checked="checked"' : "";?> /> Yes</label>
		<label><input name="issmtp" type="radio" id="nosmtp" value="0" <?php echo ($config['issmtp']==0) ? 'checked="checked"' : "";?> /> No</label>		</td>
	 </tr>
     <tr>
        <td width="20%">Contact e-mail</td>
        <td><input name="contact_mail" id="contact_mail" type="text" value="<?php echo $config['contact_mail']; ?>" size="30" /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Contact page submissions will be delivered to this address. We highly recommend this email is associated with the account above."><i class="icon-info-sign"></i></a></td>
     </tr>
	 <tr>
	 	<td>
        <button type="submit" name="test-email" value="Test this email account" class="btn btn-mini btn-blue" id="test-email" data-loading-text="Testing..." />Test this email account</button>
	 	</td>
        <td>
        <div class="hide" id="loader"><img src="img/ico-loading.gif" width="16" height="16" border="0" /> <em>Please wait...</em></div>
        </td>
	 </tr>
    </table>
	</div>

	<div class="tab-pane fade<?php echo ($selected_tab_view == 't8' || $selected_tab_view == 'sources') ? ' in active' : '';?>" id="t8">
    <h2 class="sub-head-settings" >Video Sources</h2>
    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
    <?php

    $video_sources = array_reverse($video_sources);
	$video_sources = array_sort($video_sources, 'source_name', SORT_ASC);
    foreach ($video_sources as $id => $source)
    {
        $disabled = 1;
        if (is_int($id))
        {
            if ($source['flv_player_support'] == 1 && $source['embed_player_support'] == 1)
            {
                $disabled = 0;
            }
        ?>
        <tr>
             <td width="20%">
                <?php
                if ($disabled)
                {
                    echo ucfirst($source['source_name']);
                }
                else
                {
                    echo '<strong>'. ucfirst($source['source_name']) .'</strong>';
                }
                ?>
            </td>
             <td width="80%">
              <label>
              	<input name="user_choice[<?php echo $source['source_id'];?>]" value="flvplayer" type="radio" <?php if($source['user_choice'] == 'flvplayer') echo 'checked="checked"'; if($disabled) echo 'disabled="true"'; ?> /> <span rel="tooltip" title="Choose this option if you want to use your existing default player (e.g. JW Player, Flowplayer, etc.).">Use my video player</span>
              </label>

              <label>
              <input name="user_choice[<?php echo $source['source_id'];?>]"  value="embed"  type="radio" <?php if($source['user_choice'] == 'embed') echo 'checked="checked"'; if($disabled) echo 'disabled="true"'; ?>  /> <span rel="tooltip" title="Choose this option if you want to use the <?php echo ucfirst($source['source_name']); ?> video player.">Use original player</span>
              </label>
			  
             </td>
            </tr>
        <?php
        }
    }
    ?>
    </table>
	</div>
	<div class="tab-pane fade<?php echo ($selected_tab_view == 't9' || $selected_tab_view == 'user') ? ' in active' : '';?>" id="t9">
		<h2 class="sub-head-settings" >General User Settings</h2>
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
		 <tr>
			<td width="20%">Allow users to upload videos</td>
			<td>
			<label><input name="allow_user_uploadvideo" type="radio" value="1" <?php echo ($config['allow_user_uploadvideo']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="allow_user_uploadvideo" type="radio" value="0" <?php echo ($config['allow_user_uploadvideo']==0) ? 'checked="checked"' : "";?> /> No</label>
			</td>
		 </tr>
		 <tr>
			<td width="20%">Max. video uploads/user/day</td>
			<td>
				<input name="user_upload_daily_limit" type="text" size="8" class="span1" value="<?php echo (int) $config['user_upload_daily_limit']; ?>" />
			</td>
		 </tr>
		 <tr>
			<td width="20%">Allow users to suggest videos</td>
			<td>
			<label><input name="allow_user_suggestvideo" type="radio" value="1" <?php echo ($config['allow_user_suggestvideo']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="allow_user_suggestvideo" type="radio" value="0" <?php echo ($config['allow_user_suggestvideo']==0) ? 'checked="checked"' : "";?> /> No</label>
			</td>
		 </tr>
		 <tr> 
			<td width="20%">Allow users to edit their videos</td>
			<td>
			<label><input name="allow_user_edit_video" type="radio" value="1" <?php echo ($config['allow_user_edit_video']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="allow_user_edit_video" type="radio" value="0" <?php echo ($config['allow_user_edit_video']==0) ? 'checked="checked"' : "";?> /> No</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="When set to 'Yes', users can edit the title, description, category, duration, tags, thumbnail and source URL or uploaded media file."><i class="icon-info-sign"></i></a>
			</td>
		 </tr>
		 <tr> 
			<td width="20%">Allow users to delete their videos</td>
			<td>
			<label><input name="allow_user_delete_video" type="radio" value="1" <?php echo ($config['allow_user_delete_video']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="allow_user_delete_video" type="radio" value="0" <?php echo ($config['allow_user_delete_video']==0) ? 'checked="checked"' : "";?> /> No</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Allow your users to delete their videos."><i class="icon-info-sign"></i></a>
			</td>
		 </tr>
		 <tr>
			<td width="20%">Auto-approve videos submissions from all users</td>
			<td>
			<label><input name="auto_approve_suggested_videos" type="radio" value="1" <?php echo ($config['auto_approve_suggested_videos']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="auto_approve_suggested_videos" type="radio" value="0" <?php echo ($config['auto_approve_suggested_videos']==0) ? 'checked="checked"' : "";?> /> No</label>
			</td>
		 </tr>
		 <tr> 
			<td width="20%">Auto-approve videos submissions from 'Verified' users</td>
			<td>
			<label><input name="auto_approve_suggested_videos_verified" type="radio" value="1" <?php echo ($config['auto_approve_suggested_videos_verified']==1) ? 'checked="checked"' : "";?> /> Yes</label>
			<label><input name="auto_approve_suggested_videos_verified" type="radio" value="0" <?php echo ($config['auto_approve_suggested_videos_verified']==0) ? 'checked="checked"' : "";?> /> No</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Submissions from users marked as having a 'Verified channel' will be auto-approved automatically. <br /> This applies even if you have a manual approval process for the rest of your user base. <br /><strong>Requires 'Social Module'</strong>"><i class="icon-info-sign"></i></a> 
			</td>
		 </tr>
		 <tr>
			<td width="20%">Max. upload size</td>
			<td>
				<input name="allow_user_uploadvideo_bytes" type="text" size="8" class="span1" value="<?php echo (float) readable_filesize($config['allow_user_uploadvideo_bytes']); ?>" />
				<?php
				$unit = readable_filesize($config['allow_user_uploadvideo_bytes']);
				$unit = explode(' ', $unit);
				$unit = trim($unit[1]);
				?>

				<select name="allow_user_uploadvideo_unit" class="smaller-select">
					<option value="GB" <?php if ($unit == 'GB') echo 'selected="selected"'; ?>>GB</option>
					<option value="MB" <?php if ($unit == 'MB') echo 'selected="selected"'; ?>>MB</option>
					<option value="KB" <?php if ($unit == 'KB') echo 'selected="selected"'; ?>>KB</option>
				</select>
		        <?php
				if((int) readable_filesize($config['allow_user_uploadvideo_bytes']) > (int)readable_filesize(get_true_max_filesize())) {
					echo '<span class="label label-warning">Your hosting provider has a limit of '.readable_filesize(get_true_max_filesize()).' per upload.</span>';			
				}
				?>
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set the maximum upload limit allowed (per video). Ask your hosting provider to increase the limit if it's too low."><i class="icon-info-sign"></i></a>
	        </td>
		 </tr>
		 <tr>
		 	<td>Like/Dislike rating</td>
			<td>
				<label><input name="bin_rating_allow_anon_voting" type="radio" value="1" <?php echo ($config['bin_rating_allow_anon_voting'] == 1) ? 'checked="checked"' : "";?> /> Anyone</label>
				<label><input name="bin_rating_allow_anon_voting" type="radio" value="0" <?php echo ($config['bin_rating_allow_anon_voting']==0) ? 'checked="checked"' : "";?> /> Registered users only</label>
			</td>
		 </tr>
		 <tr>
		 	<td>Allow users to create playlists</td>
			<td>
				<label><input name="allow_playlists" type="radio" value="1" <?php echo ($config['allow_playlists'] == 1) ? 'checked="checked"' : "";?> /> Yes</label>
				<label><input name="allow_playlists" type="radio" value="0" <?php echo ($config['allow_playlists']==0) ? 'checked="checked"' : "";?> /> No</label>
			</td>
		 </tr>
		 <tr>
		 	<td>Max. playlists/user</td>
			<td>
				<input name="playlists_limit" type="text" size="8" class="span1" value="<?php echo (int) $config['playlists_limit']; ?>" />
			</td>
		 </tr>
		 <tr>
		 	<td>Max. videos/playlist</td>
			<td>
				<input name="playlists_items_limit" type="text" size="8" class="span1" value="<?php echo (int) $config['playlists_items_limit']; ?>" />
			</td>
		 </tr>
		</table>
		
		<h2 class="sub-head-settings">Registration Settings</h2>
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
		 <tr>
			<td width="20%">Allow registration</td>
			<td>
			<label><input name="allow_registration" type="radio" value="1" <?php echo ($config['allow_registration']=='1') ? 'checked="checked"' : "";?> />
			Yes</label>
			<label><input name="allow_registration" type="radio" value="0" <?php echo ($config['allow_registration']=='0') ? 'checked="checked"' : "";?> /> No</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set to '<em>No</em>' to disable all public registrations. This will not disable the 'Login' procedure in the front-end. <br> Note: the default setting is '<strong>Yes</strong>'."><i class="icon-info-sign"></i></a>
	        </td>
		 </tr>
		 <tr>
			<td width="20%">Allow non-latin usernames</td>
			<td>
			<label><input name="allow_nonlatin_usernames" type="radio" value="1" <?php echo ($config['allow_nonlatin_usernames']=='1') ? 'checked="checked"' : "";?> />
			Yes</label>
			<label><input name="allow_nonlatin_usernames" type="radio" value="0" <?php echo ($config['allow_nonlatin_usernames']=='0') ? 'checked="checked"' : "";?> /> No</label>
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Set to '<em>Yes</em>' if you want to let your users register with usernames containing non-latin characters too.<br> Note: the default setting is '<strong>Yes</strong>'."><i class="icon-info-sign"></i></a>
	        </td>
		 </tr>
		 <tr>
			<td width="20%">Account activation</td>
			<td>
				<label><input name="account_activation" type="radio" value="0" <?php echo ($config['account_activation']==0) ? 'checked="checked"' : "";?> /> None</label>
				<label><input name="account_activation" type="radio" value="1" <?php echo ($config['account_activation']==1) ? 'checked="checked"' : "";?> /> User e-mail</label>
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Ask new users to verify their email by clicking a link provided upon registration. The account will remain inactive until they verify their identity."><i class="icon-info-sign"></i></a>
				<label><input name="account_activation" type="radio" value="2" <?php echo ($config['account_activation']==2) ? 'checked="checked"' : "";?> /> Admin/Moderator</label>
	        </td>
		 </tr>
		 <tr>
		 	<td width="20%">Form protection</td>
			<td>
				<label>
					<input name="spambot_prevention" type="radio" value="none" <?php echo ($config['spambot_prevention'] == 'none') ? 'checked="checked"' : "";?> /> None</lable>
				</label>
				<label>
					<input name="spambot_prevention" type="radio" value="securimage" <?php echo ($config['spambot_prevention'] == 'securimage') ? 'checked="checked"' : "";?> /> SecurImage</lable>
				</label>
				<label>
					<input name="spambot_prevention" type="radio" value="recaptcha" <?php echo ($config['spambot_prevention'] == 'recaptcha') ? 'checked="checked"' : "";?> id="" /> reCAPTCHA <span class="label label-success">Recommended</span></lable>
				</label>
			</td>
		 </tr>
	 <tr class="recaptcha_public_key_tr">
		<td width="20%">reCAPTCHA public key (Site key)</td>
		<td>
			<input name="recaptcha_public_key" type="text" class="input-xlarge" value="<?php echo $config['recaptcha_public_key'];?>" /> 
		</td>
	 </tr>
	 <tr class="recaptcha_private_key_tr">
		<td width="20%">reCAPTCHA private key (Secret key)</td>
		<td>
			<input name="recaptcha_private_key" type="text" class="input-xlarge" value="<?php echo $config['recaptcha_private_key'];?>" /> 
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="You need a reCAPTCHA/Google account to use reCAPTCHA on your site. Click '<strong>Get keys</strong>' to get started."><i class="icon-info-sign"></i></a>
			<a href="http://www.google.com/recaptcha/intro/index.html" target="_blank">Get keys</a>
		</td>
	 </tr>
		 <tr>
		 	<td width="20%">Prevent account creation in under</td>
			<td>
				<input name="register_time_to_submit" type="text" size="5" class="span1" value="<?php echo $config['register_time_to_submit'];?>" /> seconds
				<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Prevent SPAM bots from creating accounts on your website. Define how many seconds should pass before the user can submit the registration form. Default value is 3 seconds."><i class="icon-info-sign"></i></a>
			</td>
		 </tr>
		</table>
	
	<h2 class="sub-head-settings">Facebook Login</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	 <tr>
		<td width="20%">Facebook Login</td>
		<td>
			<label><input name="oauth_facebook" type="radio" value="1" <?php echo ($config['oauth_facebook'] == 1) ? 'checked="checked"' : "";?> /> Enabled</label>
			<label><input name="oauth_facebook" type="radio" value="0" <?php echo ($config['oauth_facebook'] == 0) ? 'checked="checked"' : "";?> /> Disabled</label>
		</td>
	 </tr>
	 <tr>
		<td width="20%">Facebook App ID</td>
		<td>
			<input name="oauth_fb_app_id" type="text" class="input-xlarge" value="<?php echo $config['oauth_fb_app_id'];?>" />
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="To enable this form of registration/login you need to create an App within Facebook. Click '<strong>Get help</strong>' to get started."><i class="icon-info-sign"></i></a>
			<a href="http://help.phpmelody.com/how-to-setup-facebook-login/" target="_blank">Get help</a>
		</td>
	 </tr>
	 <tr>
		<td width="20%">Facebook App Secret</td>
		<td>
			<input name="oauth_fb_app_secret" type="text" class="input-xlarge" value="<?php echo $config['oauth_fb_app_secret'];?>" />
		</td>		
	 </tr>
	 <tr>
		<td>
			<button type="submit" name="test-fb-app" value="Test this facebook app" class="btn btn-mini btn-blue" id="test-fb-app" data-loading-text="Testing..." />Check App Status</button>
		</td>
		<td>
			<div class="hide" id="fb-loader"><img src="img/ico-loading.gif" width="16" height="16" border="0" /> <em>Please wait...</em></div>
		</td>
	 </tr>
	</table>
	
	<h2 class="sub-head-settings">Twitter Login</h2>
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	 <tr>
		<td width="20%">Twitter Login</td>
		<td>
			<label><input name="oauth_twitter" type="radio" value="1" <?php echo ($config['oauth_twitter'] == 1) ? 'checked="checked"' : "";?> /> Enabled</label>
			<label><input name="oauth_twitter" type="radio" value="0" <?php echo ($config['oauth_twitter'] == 0) ? 'checked="checked"' : "";?> /> Disabled</label>
		</td>
	 </tr>
	 <tr>
		<td width="20%">Consumer Key</td>
		<td>
			<input name="oauth_twitter_consumer_key" type="text" class="input-xlarge" value="<?php echo $config['oauth_twitter_consumer_key'];?>" /> 
			<a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="To enable this form of registration/login you need to create an App within Twitter. Click '<strong>Get help</strong>' to get started."><i class="icon-info-sign"></i></a>
			<a href="http://help.phpmelody.com/how-to-setup-twitter-login/" target="_blank">Get help</a>
		</td>
	 </tr>
	 <tr>
		<td width="20%">Consumer Secret</td>
		<td>
			<input name="oauth_twitter_consumer_secret" type="text" class="input-xlarge" value="<?php echo $config['oauth_twitter_consumer_secret'];?>" /> 
		</td>
	 </tr>
	</table>


		<h2 class="sub-head-settings">Access Areas Available to Moderators</h2>
		<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
	     <tr>
	        <td width="20%">Videos</td>
	        <td>
	        	<label><input name="mod_can_manage_videos" type="checkbox" value="1" <?php if ($mod_can['manage_videos']) echo 'checked="checked"'; ?> /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Moderators will be able to <strong>add</strong>, <strong>embed</strong>, <strong>import</strong>, <strong>edit</strong>, <strong>delete</strong>, <strong>approve</strong> and <strong>manage reported videos</strong>"><i class="icon-info-sign"></i></a>
				</label>
			</td>
	     </tr>
		 <tr>
	        <td>Comments</td>
	        <td>
	        	<label><input name="mod_can_manage_comments" type="checkbox" value="1" <?php if ($mod_can['manage_comments']) echo 'checked="checked"'; ?> /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Moderators will be able to <strong>approve</strong>, <strong>edit</strong> and <strong>delete</strong> comments"><i class="icon-info-sign"></i></a>
				</label>
			</td>
	     </tr>
		 <tr>
	        <td>Manage users</td>
	        <td>
	        	<label><input name="mod_can_manage_users" type="checkbox" value="1" <?php if ($mod_can['manage_users']) echo 'checked="checked"'; ?> /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Moderators will be able to <strong>activate new accounts</strong>, <strong>ban</strong> and <strong>unban</strong> other users"><i class="icon-info-sign"></i></a>
				</label>
			</td>
	     </tr>
		 <tr>
	        <td>Manage articles</td>
	        <td>
	        	<label><input name="mod_can_manage_articles" type="checkbox" value="1" <?php if ($mod_can['manage_articles']) echo 'checked="checked"'; ?> /> <a href="#" rel="popover" data-placement="right" data-trigger="hover" data-content="Moderators will be able to <strong>add</strong>, <strong>edit</strong> and <strong>delete</strong> articles. <strong>Please note</strong> that there is a special user rank for managing only the articles: the <strong>Editor</strong> rank."><i class="icon-info-sign"></i></a>
				</label>
			</td>
	     </tr>
	    </table>
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
	
  $('input[name="mod_social"]').click(function(){
	if ($(this).val() == '0') {
		$('.mod-social-options-tr').hide();
	} else {
		$('.mod-social-options-tr').fadeIn();
	}
  });
  
  $('#mail_presets').change(function() {
	var $this = $(this).find('option:selected').attr('id');

	if($this == 'gmail') {
		$('#mail_settings').find('#mail_server').val('ssl://smtp.gmail.com');
		$('#mail_settings').find('#mail_port').val('465');
		$('#mail_settings').find('#mail_user').val('you@gmail.com');
		$('#mail_settings').find('#mail_pass').val('');
		$('#mail_settings').find('#contact_mail').val('you@gmail.com');
		$('#mail_preset_warn').html('<div class="alert"><small>GMAIL is an excellent choice if the following  conditions are met: <ol><li>Your site sends less than 500 emails per day</li><li>Your hosting provider allows outgoing SSL connections</li><li>Your GMAIL account is set to allow SMTP connections</li></ol></small></div>');
	}
	if($this == 'godaddy') {
		$('#mail_settings').find('#mail_server').val('relay-hosting.secureserver.net');
		$('#mail_settings').find('#mail_port').val('25');
		$('#mail_settings').find('#mail_user').val('username and password are not required');
		$('#mail_settings').find('#mail_pass').val('none');
		$('#mail_settings').find('#contact_mail').val('you@your-godaddy-account.com');
		$('#mail_settings').find('#nosmtp').attr('checked', 'checked');


		$('#mail_preset_warn').html('<div class="alert alert-danger"><small>Using <strong>GoDaddy</strong>\'s server to send emails is a bit problematic. For example they don\'t permit email delivery to @aol.com, @gmail.com, @hotmail.com, @msn.com, or @yahoo.com addresses. That makes their service almost unusable from PHP scripts. We recommend using a different provider if possible.</small></div>').css('display','block');
	} 	if($this == 'yahoo') {
		$('#mail_settings').find('#mail_server').val('smtp.mail.yahoo.com');
		$('#mail_settings').find('#mail_port').val('587');
		$('#mail_settings').find('#mail_user').val('you@yahoo.com');
		$('#mail_settings').find('#mail_pass').val('');
		$('#mail_settings').find('#contact_mail').val('you@yahoo.com');
		$('#mail_preset_warn').css('display','none');
	} 	if($this == 'none') {
		$('#mail_settings').find('#mail_server').val('mail.yourdomain.com');
		$('#mail_settings').find('#mail_port').val('25');
		$('#mail_settings').find('#mail_user').val('user+yourdomain.com');
		$('#mail_settings').find('#mail_pass').val('');
		$('#mail_settings').find('#contact_mail').val('user@yourdomain.com');
		$('#mail_preset_warn').css('display','none');
	}
  });
});
</script>
<?php
include('footer.php');
