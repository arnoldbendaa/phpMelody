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

session_start();
require('../config.php');
include_once(ABSPATH . 'include/user_functions.php');
include_once(ABSPATH . 'include/islogged.php');
include(ABSPATH .''. _ADMIN_FOLDER .'/functions.php');

// Overwrite Maintenance Mode
$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
session_write_close();

// --- START local functions
function curl_file_get_contents($url)
{
	if (function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID']); 
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		$contents = curl_exec($ch);
		$errormsg = curl_error($ch);
		curl_close($ch);
		
		if($errormsg != '')
		{
			return $errormsg;
		}
		return $contents;
	}
	else if (ini_get('allow_url_fopen') == 1)
	{
		$opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
		$context = stream_context_create($opts);
		return file_get_contents($url, false, $context);
	}

	return false;
}
// --- END local functions


// Data mapping & defining default values
$default_tpl_properties = array(
	// input name attr	=> input value attr
	'ct_heading' 		=> '#444444',		//	h1,h2,h3,h4,h5,h6: color
	'ct_container' 		=> '#ffffff',		//	#wrapper: background-color
	'ct_container_txt' 	=> '#000000',		//	#wrapper: color 
	'ct_body' 			=> '#fbfbfb',		//	body: background-color
	'ct_header' 		=> '#ffffff',		//	.wide-header: background-color
	'ct_header_user_pane' => '#505860',     //  #user-pane .greet-links a: color
	'ct_widenav' 		=> 'transparent',	//	.wide-nav: background-color
	'ct_widenav_link' 	=> 'inherit',		//	.navbar ul.nav li a.wide-nav-link: color
	'ct_wrapper_link' 	=> '#6e757b',		//	#wrapper a: color
	'ct_video_link' 	=> '#505961',		//	a.pm-title-link: color
	'ct_sitetitle' 		=> '#3583CC',		//	h1.site-title a: color
	'ct_footer'			=> 'inherit',		//	footer: background-color
	'ct_footer_link' 	=> '#000000',		//	footer, footer a: color
	'ct_container_width'	=> '1000px'			//	#wrapper, .fixed960: width, max-width (px)
);

if ($_POST['reset'] == 'true') // AJAX 
{
	if ( ! $logged_in || ! is_admin())
	{
		exit();
	}
	update_config('default_tpl_customizations', base64_encode(serialize(array())), true); //must use base64_encode/decode because of "," in serialized string.
	//header('Location: '. _URL .'/'. _ADMIN_FOLDER .'/customize.php');
	exit();
}

if ($_POST['action'] == 'customize-save') // AJAX
{
	if ( ! $logged_in || ! is_admin())
	{
		$ajax_msg = ($logged_in) ? 'Access denied!' : 'Please log in.';
		exit(json_encode(array('success' => false, 'msg' => pm_alert_error($ajax_msg))));
	}

	$customs = array();
	
	foreach ($default_tpl_properties as $property => $default_value)
	{
		if ((strtolower($default_value) != strtolower($_POST[$property])) && $_POST[$property] != '')
		{
			$value = trim($_POST[$property]);
			$value = stripslashes($value); // must have for servers with magic_quotes on.
			
			switch ($property) 
			{
				case 'ct_body':
					
					$customs['body']['background-image'] = 'none';
					$customs['body']['background-color'] = $value;

				break;
				
				case 'ct_heading':
				
					$customs['h1']['color'] = $value;
					$customs['h2']['color'] = $value;
					$customs['h3']['color'] = $value;
					$customs['h4']['color'] = $value;
					$customs['h5']['color'] = $value;
					$customs['h6']['color'] = $value;

				break;
				
				case 'ct_container':
				
					$customs['#wrapper']['background-image'] = 'none';
					$customs['#wrapper']['background-color'] = $value;

					$customs['.text-exp .show-more']['background-image'] = array(0 => 'linear-gradient(bottom, '. $value .' 0%, '. $value .' 50%, rgba(244,245,247,0)100%)',
																				 1 => '-o-linear-gradient(bottom, '. $value .' 0%, '. $value .' 50%, rgba(244,245,247,0)100%)',
																				 2 => '-moz-linear-gradient(bottom, '. $value .' 0%, '. $value .' 50%, rgba(244,245,247,0)100%)'
																				);
				break;
				
				case 'ct_container_txt':
					
					$customs['#wrapper']['color'] = $value;
				
				break;
				
				case 'ct_header':
					 
					$customs['.wide-header']['background-image'] = 'none';
					$customs['.wide-header']['background-color'] = $value;

				break;

				case 'ct_header_user_pane':
					 
					$customs['#user-pane']['color'] = $value;
					$customs['#user-pane .greet-links a']['color'] = $value;
					$customs['#user-pane .greet-links a:visited']['color'] = $value;

				break;
				
				case 'ct_widenav':
					 
					$customs['.wide-nav']['background-image'] = 'none';
					$customs['.wide-nav']['background-color'] = $value;
					$customs['.wide-nav']['box-shadow'] = 'none';
					$customs['.wide-nav']['border-top-color'] = $value;
					$customs['.wide-nav']['border-bottom-color'] = $value;

				break;
				
				case 'ct_widenav_link':
					
					$customs['.navbar ul.nav li a.wide-nav-link']['text-shadow'] = 'none';
					$customs['.navbar ul.nav li a.wide-nav-link']['color'] = $value;

				break;
				
				case 'ct_wrapper_link':
					
					$customs['#wrapper a']['text-shadow'] = 'none';
					$customs['#wrapper a']['color'] = $value;

				break;
				
				case 'ct_video_link':
					 
					$customs['#wrapper a.pm-title-link']['text-shadow'] = 'none';
					$customs['#wrapper a.pm-title-link']['color'] = $value;

				break;
				
				case 'ct_sitetitle':
					 
					$customs['h1.site-title a']['text-shadow'] = 'none';
					$customs['h1.site-title a']['color'] =  $value;

				break;
				
				case 'ct_footer':
					
					$customs['footer']['text-shadow'] = 'none';
					$customs['footer']['background-color'] = $value;

				break;
				
				case 'ct_footer_link':
	
					$customs['footer']['text-shadow'] = 'none';
					$customs['footer']['color'] = $value;
					$customs['footer a']['text-shadow'] = 'none';
					$customs['footer a']['color'] = $value;
					
				break;
				
				case 'ct_container_width':
					$value = str_replace('px', '', $value);
					$customs['#wrapper']['width'] = $value .'px';
					$customs['#wrapper']['max-width'] = $value .'px';
					$customs['.fixed960']['max-width'] = $value .'px';
					$customs['.video-wrapper-wide']['width'] = $value - 40 .'px';
					$customs['.pm-video-head-wide']['width'] = $value - 40 .'px';
					$customs['#video-wrapper.video-wrapper-wide object']['width'] = $value - 40 .'px';
					$customs['#video-wrapper.video-wrapper-wide embed']['width'] = $value - 40 .'px';
					$customs['#video-wrapper.video-wrapper-wide iframe']['width'] = $value - 40 .'px';
				break;
			}
		}
	}
	
	update_config('default_tpl_customizations', base64_encode(serialize($customs)), true); //must use base64_encode/decode because of "," in serialized string.
	
	echo json_encode(array('success' => true,
							'msg' => pm_alert_success('<strong>Saved!</strong> Return to the <a href="settings_theme.php">Admin Area</a> or <a href="'._URL.'/index.'._FEXT.'">see your website</a>.')
						  ));

	exit();
}

if ( ! $logged_in || ! is_admin())
{
	header('Location: '. _URL .'/'. _ADMIN_FOLDER .'/login.php');
	exit();
}

$tpl_customizations = array();
if ($config['default_tpl_customizations'] != '')
{
	$tpl_customizations = unserialize(base64_decode($config['default_tpl_customizations']));
	 
}

$tpl_properties = array();

foreach ($default_tpl_properties as $name => $value)
{
	switch ($name)
	{
		case 'ct_heading':
			$tpl_properties[$name] = ($tpl_customizations['h1']['color'] != '') ? $tpl_customizations['h1']['color'] : $value; 
		break;
		
		case 'ct_container':
			$tpl_properties[$name] = ($tpl_customizations['#wrapper']['background-color'] != '') ? $tpl_customizations['#wrapper']['background-color'] : $value;
		break;
		
		case 'ct_container_txt':
			$tpl_properties[$name] = ($tpl_customizations['#wrapper']['color'] != '') ? $tpl_customizations['#wrapper']['color'] : $value;;
		break;
		
		case 'ct_body':
			$tpl_properties[$name] = ($tpl_customizations['body']['background-color'] != '') ? $tpl_customizations['body']['background-color'] : $value;
		break;
		
		case 'ct_header':
			$tpl_properties[$name] = ($tpl_customizations['.wide-header']['background-color'] != '') ? $tpl_customizations['.wide-header']['background-color'] : $value;
		break;

		case 'ct_header_user_pane':
			$tpl_properties[$name] = ($tpl_customizations['#user-pane']['color'] != '') ? $tpl_customizations['#user-pane']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['#user-pane .greet-links a']['color'] != '') ? $tpl_customizations['#user-pane .greet-links a']['color'] : $value;
			$tpl_properties[$name] = ($tpl_customizations['#user-pane .greet-links a:visited']['color'] != '') ? $tpl_customizations['#user-pane .greet-links a:visited']['color'] : $value;
		break;
		
		case 'ct_widenav':
			$tpl_properties[$name] = ($tpl_customizations['.wide-nav']['background-color'] != '') ? $tpl_customizations['.wide-nav']['background-color'] : $value; 
		break;
		
		case 'ct_widenav_link':
			$tpl_properties[$name] = ($tpl_customizations['.navbar ul.nav li a.wide-nav-link']['color'] != '') ? $tpl_customizations['.navbar ul.nav li a.wide-nav-link']['color'] : $value; 
		break;
		
		case 'ct_wrapper_link':
			$tpl_properties[$name] = ($tpl_customizations['#wrapper a']['color'] != '') ? $tpl_customizations['#wrapper a']['color'] : $value; 
		break;
		
		case 'ct_video_link':
			$tpl_properties[$name] = ($tpl_customizations['a.pm-title-link']['color'] != '') ? $tpl_customizations['a.pm-title-link']['color'] : $value; 
		break;
		
		case 'ct_sitetitle':
			$tpl_properties[$name] = ($tpl_customizations['h1.site-title a']['color'] != '') ? $tpl_customizations['h1.site-title a']['color'] : $value; 
		break;
		
		case 'ct_footer':
			$tpl_properties[$name] = ($tpl_customizations['footer']['background-color'] != '') ? $tpl_customizations['footer']['background-color'] : $value; 
		break;
		
		case 'ct_footer_link':
			$tpl_properties[$name] = ($tpl_customizations['footer a']['color'] != '') ? $tpl_customizations['footer a']['color'] : $value; 
		break;
		
		case 'ct_container_width':
			$tpl_properties[$name] = ($tpl_customizations['#wrapper']['width'] != '') ? $tpl_customizations['#wrapper']['width'] : $value; 
		break;
	}
}

// get a random page for live preview 
if ($config['published_videos'] == 0)
{
	$preview_page = curl_file_get_contents(_URL);
}
else
{
	$rand_from = rand(0, $config['published_videos']);
	
	$sql = "SELECT uniq_id 
			FROM pm_videos 
			WHERE added <= '". time() ."'
			LIMIT $rand_from, 1";

	if ($result = mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		if (count($row) > 0)
		{
			$preview_page = curl_file_get_contents(_URL .'/watch.php?vid='. $row['uniq_id']);
			mysql_free_result($result);
		}
	}
	
	if ( ! $result || ! $row)
	{
		$preview_page = curl_file_get_contents(_URL);
	}
}
$preview_page = preg_replace('/<a(.*)href="([^"]*)"(.*)>/','<a$1href="javascript:alert(\'This link is not accessible during the customization process.\');"$3>',$preview_page);

echo $preview_page;

?>
<style>
body {
	margin:0;
	padding:0;
	margin-left: 250px;
}
.pm-ad-zone {
	display:none;	
}
#preroll_placeholder, #Playerholder, object, embed, iframe {
	z-index: 100;
}
#ct_wrapper_width {
	max-width: 100%;
}
.colorpicker {
	z-index: 6666 !important;
}
.ct_sidebar {
	position: fixed;
	overflow-y: auto;
	top:0;
	left:0;
	width: 250px;
	height: 100%;
	display: block;
	padding: 15px 20px;
	background-color: #f5f5f5;
	box-shadow: inset 0 0 4px #777;
	border-right: 1px solid #fff;
	z-index: 5000 !important;
}
.ct_title {
	font-size: 18px;
	font-weight: bold;
	line-height: 1.4em;
	text-transform:capitalize;
	padding: 5px 0;
	color: #555;
	text-shadow: 0 1px 0 #FFF;
	border-bottom: 3px solid #ddd;
	margin-bottom: 20px;
}
.ct_table {
	font-size: 11px;
	font-weight: bold;
}
.ct_table td {
	border-top: 0;
	border-bottom: 0;
	vertical-align: middle;
	text-shadow: 0 1px 0 #FFF;
	padding: 2px 0;
	margin:0;
}
.ct_table .input-append {
	position: relative;
}
.ct_table .reset-button {
	position: absolute;
	left:-23px;
	top:2px;
}
.ct_submit {
	display: block;
	width: 250px;
}
.ct_sidebar button.btn-small {
	font-size: 11px !important;
	font-weight: bold;
}
</style>
<!--
ct_body
ct_sitetitle
ct_header
ct_header_user_pane
ct_widenav
ct_widenav_link
ct_video_link
ct_container
ct_container_txt
ct_wrapper_link
ct_heading
ct_footer
ct_footer_link2
-->
<div class="ct_sidebar" id="sticky">
<div class="row-fluid">
	<div class="span12 ct_title"><?php echo $config['template_f']; ?> Theme Customization</div>
</div>
<div id="ajax-response-container" style="display:none"></div>
<form name="theme-customization" action="" method="post">
<table width="100%" cellpadding="0" cellspacing="0" class="table ct_table">
	<tr>
		<td width="50%" align="left" valign="middle">
			Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_body'];?>" data-color-format="hex" id="ct_body">
				<input id="ct_body2" name="ct_body" type="text" size="14" value="<?php echo $tpl_properties['ct_body'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_body'];?>"></i></span>
                <a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_body"><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td width="1%" align="center" valign="top">
			<input type="hidden" name="ct_body_base_value" value="<?php echo $tpl_properties['ct_body'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Site Name
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_sitetitle'];?>" data-color-format="hex" id="ct_sitetitle">
				<input id="ct_sitetitle2" name="ct_sitetitle" type="text" size="14" value="<?php echo $tpl_properties['ct_sitetitle'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_sitetitle'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_sitetitle"><i class="icon-remove-sign opac6"></i></a>
            </div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_sitetitle_base_value" value="<?php echo $tpl_properties['ct_sitetitle'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Header Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_header'];?>" data-color-format="hex" id="ct_header">
				<input id="ct_header2" name="ct_header" type="text" size="14" value="<?php echo $tpl_properties['ct_header'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_header'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_header" ><i class="icon-remove-sign opac6"></i></a>
            </div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_header_base_value" value="<?php echo $tpl_properties['ct_header'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Header Text &amp; Links
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_header_user_pane'];?>" data-color-format="hex" id="ct_header_user_pane">
				<input id="ct_header_user_pane2" name="ct_header_user_pane" type="text" size="14" value="<?php echo $tpl_properties['ct_header_user_pane'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_header_user_pane'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_header_user_pane" ><i class="icon-remove-sign opac6"></i></a>
            </div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_header_user_pane_base_value" value="<?php echo $tpl_properties['ct_header_user_pane'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Menu Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_widenav'];?>" data-color-format="hex" id="ct_widenav">
				<input id="ct_widenav2" name="ct_widenav" type="text" size="14" value="<?php echo $tpl_properties['ct_widenav'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_widenav'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_widenav" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_widenav_base_value" value="<?php echo $tpl_properties['ct_widenav'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Menu Links
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_widenav_link'];?>" data-color-format="hex" id="ct_widenav_link">
				<input id="ct_widenav_link2" name="ct_widenav_link" type="text" size="14" value="<?php echo $tpl_properties['ct_widenav_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_widenav_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_widenav_link" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_widenav_link_base_value" value="<?php echo $tpl_properties['ct_widenav_link'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Video &amp; Article Links
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_video_link'];?>" data-color-format="hex" id="ct_video_link">
				<input id="ct_video_link2" name="ct_video_link" type="text" size="14" value="<?php echo $tpl_properties['ct_video_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_video_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_video_link" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_video_link_base_value" value="<?php echo $tpl_properties['ct_video_link'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Headings
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_heading'];?>" data-color-format="hex" id="ct_heading">
				<input id="ct_heading2" name="ct_heading" type="text" size="14" value="<?php echo $tpl_properties['ct_heading'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_heading'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_heading"><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_heading_base_value" value="<?php echo $tpl_properties['ct_heading'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Container Width
		</td>
		<td align="right">
        	<div class="input-append pull-right">
			<input id="ct_container_width" name="ct_container_width" type="text" size="14" value="<?php echo $tpl_properties['ct_container_width']; ?>" style="width: 55px;"/><span class="add-on">px</span>
            </div>
		</td>
		<td align="center" valign="top">
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Container Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_container'];?>" data-color-format="hex" id="ct_container">
				<input id="ct_container2" name="ct_container" type="text" size="14" value="<?php echo $tpl_properties['ct_container'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_container'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_container" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_container_base_value" value="<?php echo $tpl_properties['ct_container'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Container Text
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_container_txt'];?>" data-color-format="hex" id="ct_container_txt">
				<input id="ct_container_txt2" name="ct_container_txt" type="text" size="14" value="<?php echo $tpl_properties['ct_container_txt'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_container_txt'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_container_txt" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_container_txt_base_value" value="<?php echo $tpl_properties['ct_container_txt'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Container Links (All)
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_wrapper_link'];?>" data-color-format="hex" id="ct_wrapper_link">
				<input id="ct_wrapper_link2" name="ct_wrapper_link" type="text" size="14" value="<?php echo $tpl_properties['ct_wrapper_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_wrapper_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_wrapper_link" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_wrapper_link_base_value" value="<?php echo $tpl_properties['ct_wrapper_link'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Footer Background
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_footer'];?>" data-color-format="hex" id="ct_footer">
				<input id="ct_footer2" name="ct_footer" type="text" size="14" value="<?php echo $tpl_properties['ct_footer'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_footer'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_footer" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_footer_base_value" value="<?php echo $tpl_properties['ct_footer'];?>" />
		</td>
	</tr>
	<tr>
		<td align="left" valign="middle">
			Footer Text
		</td>
		<td align="right">
			<div class="input-append color pull-right" data-color="<?php echo $tpl_properties['ct_footer_link'];?>" data-color-format="hex" id="ct_footer_link">
				<input id="ct_footer_link2" name="ct_footer_link" type="text" size="14" value="<?php echo $tpl_properties['ct_footer_link'];?>" style="width: 55px;"/><span class="add-on"><i style="background-color: <?php echo $tpl_properties['ct_footer_link'];?>"></i></span>
				<a href="#reset" alt="Reset" title="Reset" class="reset-button btn btn-link btn-mini" parent-input="ct_footer_link" ><i class="icon-remove-sign opac6"></i></a>
			</div>
		</td>
		<td align="center" valign="top">
			<input type="hidden" name="ct_footer_link_base_value" value="<?php echo $tpl_properties['ct_footer_link'];?>" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<ul class="pager ct_submit">
				<li class="previous">
					<button id="button-cancel" class="btn btn-small btn-normal border-radius3">
						&larr; Cancel
					</button>
				</li>
				<li class="next">
					<button id="button-save" class="btn btn-small btn-blue border-radius3">
						Save &amp; Apply
					</button>
					<input type="hidden" name="action" value="customize-save" />
				</li>
			</ul>
			<div align="center"><a href="customize.php?reset=true" id="reset-to-default">Reset all to default</a></div>
		</td>
	</tr>
</table>
</form>
</div><!-- .ct_sidebar -->

<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.nouislider.css" />
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-colorpicker.css" />
<script src="js/bootstrap-colorpicker.min.js" type="text/javascript"></script>
<script src="js/jquery.nouislider.min.js" type="text/javascript"></script>
<script type="application/javascript">
$("#ct_heading").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("h1").css("color", hex);
    $("h2").css("color", hex);
    $("h3").css("color", hex);
    $("h4").css("color", hex);
    $("h5").css("color", hex);
    $("h6").css("color", hex);
    $("#ct_heading2").val(hex);
	
	customize_show_reset_button("ct_heading");
});
$("#ct_container").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("#wrapper").css({
        "background-image": "none",
        "background-color": hex
    });
    $(".text-exp .show-more").css({
        "background-image": "linear-gradient(bottom, " + hex + " 0%, " + hex + " 50%, rgba(244,245,247,0)100%)"
    });
    $(".text-exp .show-more").css({
        "background-image": "-o-linear-gradient(bottom, " + hex + " 0%, " + hex + " 50%, rgba(244,245,247,0)100%)"
    });
    $(".text-exp .show-more").css({
        "background-image": "-moz-linear-gradient(bottom, " + hex + " 0%, " + hex + " 50%, rgba(244,245,247,0)100%)"
    });
    $("#ct_container2").val(hex);
	
	customize_show_reset_button("ct_container");
});
$("#ct_container_txt").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("#wrapper").css("color", hex);
    $("#ct_container_txt2").val(hex);
	
	customize_show_reset_button("ct_container_txt");
});
$("#ct_body").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("body").css({
        "background-image": "none",
        "background-color": hex
    });
    $("#ct_body2").val(hex);
	
	customize_show_reset_button("ct_body");
});
$("#ct_header").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".wide-header").css({
        "background-image": "none",
        "background-color": hex
    });
    $("#ct_header2").val(hex);
		
	customize_show_reset_button("ct_header");
});

$("#ct_header_user_pane").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".greet-links").css({
		"color": hex
    });
    $(".greet-links a").css({
		"color": hex
    });
    $("#ct_header_user_pane2").val(hex);
		
	customize_show_reset_button("ct_header_user_pane");
});


$("#ct_widenav").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".wide-nav").css({
        "background-image": "none",
        "background-color": hex,
        "box-shadow": "none",
		"border-top": "2px solid" + hex + "",
		"border-bottom": "1px solid" + hex + ""
    });
    $(".navbar ul.nav li a.wide-nav-link").css({
        "text-shadow": "none",
    });
    $("#ct_widenav2").val(hex);
	
	customize_show_reset_button("ct_widenav");
});
$("#ct_widenav_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $(".navbar ul.nav li a.wide-nav-link").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_widenav_link2").val(hex);
	
	customize_show_reset_button("ct_widenav_link");
});
$("#ct_wrapper_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("#wrapper a").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_wrapper_link2").val(hex);
	
	customize_show_reset_button("ct_wrapper_link");
});
$("#ct_video_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("a.pm-title-link").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_video_link2").val(hex);
	
	customize_show_reset_button("ct_video_link");
});
$("#ct_sitetitle").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("h1.site-title a").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_sitetitle2").val(hex);
	
	customize_show_reset_button("ct_sitetitle");
});
$("#ct_footer").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("footer").css({
        "text-shadow": "none",
        "background-color": hex
    });
    $("#ct_footer2").val(hex);
	
	customize_show_reset_button("ct_footer");
});
$("#ct_footer_link").colorpicker().on("changeColor", function(ev){
    hex = ev.color.toHex();
    $("footer").css({
        "text-shadow": "none",
        color: hex
    });
    $("footer a").css({
        "text-shadow": "none",
        color: hex
    });
    $("#ct_footer_link2").val(hex);
	
	customize_show_reset_button("ct_footer_link");
});

$('#ct_container_width').change(function() {

	var pixels = $(this).val();
	$("#wrapper").css({
		width: pixels + "px",
		"max-width":pixels + "px"
	});
	$(".fixed960").css("max-width", "" + pixels + "px");
	$("#ct_container_width").val(pixels);
});

function customize_show_reset_button(for_input_name) {
	var old_value = $('input[name="'+ for_input_name +'_base_value"]').val();
	var new_value = $('input[name="'+ for_input_name +'"]').val();
	
	if (new_value != old_value) {
		$('a[parent-input="'+ for_input_name +'"]').fadeIn();
	}
}

function customize_update_colorpicker(for_input_name, new_hex) {
	
	switch (for_input_name) {
		case "ct_body":
			$("#ct_body").colorpicker("setValue", new_hex); 
		break;
		case "ct_heading":
			$("#ct_heading").colorpicker("setValue", new_hex); 
		break;
		case "ct_container":
			$("#ct_container").colorpicker("setValue", new_hex); 
		break;
		case "ct_container_txt":
			$("#ct_container_txt").colorpicker("setValue", new_hex); 
		break;
		case "ct_header":
			$("#ct_header").colorpicker("setValue", new_hex); 
		break;
		case "ct_header_user_pane":
			$("#ct_header_user_pane").colorpicker("setValue", new_hex); 
		break;
		case "ct_widenav":

			if (new_hex == 'transparent') {
				$(".wide-nav").css({
			        "background-image": "#f0f0f0",
			        "background-color": new_hex,
			        "box-shadow": "#f1f1f1",
					"border-top": "1px solid #d2d2d2",
					"border-bottom": "1px solid #d2d2d2"
			    });
			    $("#ct_widenav2").val(new_hex);
				
				customize_show_reset_button("ct_widenav");
			} else {
				$("#ct_widenav").colorpicker("setValue", new_hex);
			} 
			
			
		break;
		case "ct_widenav_link":
			if (new_hex == 'inherit') {
				 $(".navbar ul.nav li a.wide-nav-link").css({
			        "text-shadow": "none",
			        color: new_hex
			    });
			    $("#ct_widenav_link2").val(new_hex);
				
				customize_show_reset_button("ct_widenav_link");
			} else {
				$("#ct_widenav_link").colorpicker("setValue", new_hex);
			}
		break;
		case "ct_wrapper_link":
			$("#ct_wrapper_link").colorpicker("setValue", new_hex); 
		break;
		case "ct_video_link":
			$("#ct_video_link").colorpicker("setValue", new_hex); 
		break;
		case "ct_sitetitle":
			$("#ct_sitetitle").colorpicker("setValue", new_hex); 
		break;
		case "ct_footer":
			if (new_hex == 'inherit') {
			    $("footer").css({
			        "background-color": new_hex
			    });
			    $("#ct_footer2").val(new_hex);

				customize_show_reset_button("ct_footer");
			} else {
				$("#ct_footer").colorpicker("setValue", new_hex);
			} 
		break;
		case "ct_footer_link":
			$("#ct_footer_link").colorpicker("setValue", new_hex); 
		break;
	}
	//
}
$(document).ready(function(){
	
	// hide all reset buttons
	$(".reset-button").hide();
	
	// bind to change event to inputs 
	$('input[name^="ct_"]').change(function(){
		customize_show_reset_button($(this).attr("name"));
		customize_update_colorpicker($(this).attr("name"), $(this).val());
	});

	// bind reset action
	$('a[class^="reset-button"]').click(function(){
		var name = $(this).attr("parent-input");
		var old_value = $('input[name="'+ name +'_base_value"]').val();
		
		customize_update_colorpicker(name, old_value);
		
		$(this).hide();
		
		return false;
	});
	
	// prevent form submission
	$("form").submit(function(event){
		 event.preventDefault();
	});
	
	$("#button-cancel").click(function(){
		window.location = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/settings_theme.php';?>";
	});
	
	$("#button-save").click(function(){
		
		$("#ajax-response-container").html("");
		$(this).attr("disabled", "disabled").addClass("disabled");

		var_form_data = $('form[name="theme-customization"]').serialize();
		
		$.ajax({
			url: "<?php echo _URL.'/'. _ADMIN_FOLDER .'/customize.php';?>",
			data: var_form_data,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#ajax-response-container").html(data["msg"]).show();
				
				$("#button-save").removeAttr("disabled").removeClass("disabled");
			}
		});
	});
	
	$('#reset-to-default').click(function(){
		
		if (confirm('Are you sure you want to reset all values to default?')) { 
			$.ajax({
				url: '<?php echo _URL .'/'. _ADMIN_FOLDER .'/customize.php'?>',
				data: {
					reset: 'true'
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					window.location = "<?php echo _URL .'/'. _ADMIN_FOLDER .'/customize.php';?>";
				}
			});
		}
		return false;
	});
});

</script>