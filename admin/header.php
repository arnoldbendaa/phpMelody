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

if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
{
	header("Location: "._URL. "/". _ADMIN_FOLDER ."/login.php");
	exit();
}

$mod_can = array();
$allowed_access = array('1');// dashboard

if (is_moderator())
{
	$mod_can = mod_can();
	
	if ($mod_can['manage_videos']) 
		$allowed_access[] = '2';
	
	if ($mod_can['manage_comments'])
	{
		$allowed_access[] = '4';
		$allowed_access[] = '5';
	}
	
	if ($mod_can['manage_users'])
		$allowed_access[] = '6';
	
	if ($mod_can['manage_articles'])  
		$allowed_access[] = 'mod_article';
}

if (is_editor() && _MOD_ARTICLE)
{
	$allowed_access[] = 'mod_article';
}

define('VS_UNCHECKED', 0);
define('VS_OK', 1);
define('VS_BROKEN', 2);
define('VS_RESTRICTED', 3);
define('VS_UNCHECKED_IMG', "vs_unchecked");
define('VS_OK_IMG', "vs_ok");
define('VS_BROKEN_IMG', "vs_broken");
define('VS_RESTRICTED_IMG', "vs_restricted");
define('VS_NOTAVAILABLE_IMG', "vs_na");

$upload_max_filesize = get_true_max_filesize();

if ($showm == '2')
{
	if (empty($_COOKIE['aa_videos_per_page']))
	{
		setcookie('aa_videos_per_page', 25, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_videos_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 25 : $results;
		setcookie('aa_videos_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_videos_per_page'] = $results;
	}

	if (in_array($_POST['data_source'], array('youtube', 'youtube-channel', 'dailymotion', 'vimeo')))
	{
		setcookie('aa_import_from', $_POST['data_source'], time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
}

if ($showm == '5')
{
	if (empty($_COOKIE['aa_comments_per_page']))
	{
		setcookie('aa_comments_per_page', 25, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_comments_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 25 : $results;
		setcookie('aa_comments_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_comments_per_page'] = $results;
	}
}


if ($showm == '6')
{
	if (empty($_COOKIE['aa_users_per_page']))
	{
		setcookie('aa_users_per_page', 25, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_users_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 25 : $results;
		setcookie('aa_users_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_users_per_page'] = $results;
	}
}

if ($showm == 'mod_pages')
{
	if (empty($_COOKIE['aa_pages_per_page']))
	{
		setcookie('aa_pages_per_page', 25, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_pages_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 25 : $results;
		setcookie('aa_pages_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_pages_per_page'] = $results;
	}
}
if ($showm == 'mod_article')
{
	if (empty($_COOKIE['aa_articles_per_page']))
	{
		setcookie('aa_articles_per_page', 25, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_articles_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 25 : $results;
		setcookie('aa_articles_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_articles_per_page'] = $results;
	}
}
if ($showm == 'cron')
{
	if (empty($_COOKIE['aa_cron_jobs_per_page']))
	{
		setcookie('aa_cron_jobs_per_page', 25, time()+(COOKIE_TIME * 100), COOKIE_PATH);
	}
	
	if ( ! empty($_GET['results']) && $_GET['results'] != $_COOKIE['aa_cron_jobs_per_page'])
	{
		$results = (int) $_GET['results'];
		$results = ($results <= 0) ? 25 : $results;
		setcookie('aa_cron_jobs_per_page', $results, time()+(COOKIE_TIME * 100), COOKIE_PATH);
		$_COOKIE['aa_cron_jobs_per_page'] = $results;
	}
}

?>
<?php
// Count important data
$vapprv = count_entries('pm_temp', '', '');
$crps = count_entries('pm_reports', 'r_type', '1');
$tab_video_total = $vapprv + $crps;
$capprv = count_entries('pm_comments', 'approved', '0');
$flagged_comments = count_entries('pm_comments', '1', '1\' AND report_count > \'0');
$pending_comments = count_entries('pm_comments', '1', '1\' AND approved = \'0');
$tab_comments = $capprv + $flagged_comments;
$tab_internallog = (int) $config['unread_system_messages'];

$sitemap_options = @unserialize(stripslashes($config['video_sitemap_options']));
if (is_array($sitemap_options))
{
	$time_now = time();
	
	if ($time_now > ($sitemap_options['sitemap_last_build'] + (86400 * 14)) && $sitemap_options['sitemap_last_build'] > 0 && $config['published_videos'] > $sitemap_options['total_videos'])
	{
		$tab_regular_sitemap = 1; // This means it is too old.
	}
	
	if ($time_now > ($sitemap_options['video-sitemap_last_build'] + (86400 * 14)) && $sitemap_options['video-sitemap_last_build'] > 0 && $config['published_videos'] > $sitemap_options['total_videos'])
	{
		$tab_video_sitemap = 1; // This means it is too old.
	}
}
?>
<!DOCTYPE html>
<!--[if IE 7 | IE 8 | IE 9]>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">-->

<meta name="viewport" content="width=1024,maximum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">
<title><?php echo ($_page_title != '') ? $_page_title .' - '. $config['homepage_title'] : $config['homepage_title'] .' - Admin Area'; ?></title>

<link rel="shortcut icon" type="image/ico" href="img/favicon.ico" />
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-responsive.min.css" />

<link rel="stylesheet" type="text/css" media="screen" href="css/admin-wrap.css" />
<?php if($load_ibutton == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.ibutton.css" />
<?php endif; ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/admin.css" />

<!--[if IE]><link rel="stylesheet" type="text/css" href="css/admin-ie.css"/><![endif]-->
<?php if($load_chzn_drop == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/chosen.css" />
<?php endif; ?>
<?php if($load_colorpicker == 1): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-colorpicker.css" />
<?php endif; ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<?php if($showm == 2 || $showm == 'cron'): ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/video-src.css" />
<?php endif; ?>

<link rel="stylesheet" type="text/css" media="screen" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css" />
<link href='//fonts.googleapis.com/css?family=Open+Sans:300,600,400,400italic,700,700italic|Cuprum:400,700|Roboto:400,300,500,700' rel='stylesheet' type='text/css'>

<?php $modframework->trigger_hook('admin_header'); ?>

<?php if ($load_datepicker) : ?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datepicker.min.css" />
<?php endif; ?>

<?php if ($load_flot) : ?>
<script type="text/javascript" src="js/jquery.flot.min.js"></script>
<script type="text/javascript" src="js/jquery.flot.resize.min.js"></script>
<?php endif; ?>

<?php if ($load_scrollpane == 1) : ?>
<script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="js/jquery.jscrollpane.min.js"></script>
<?php endif; ?>

<?php if ($load_dotdotdot == 1) : ?>
<script type="text/javascript" src="js/jquery.dotdotdot.min.js"></script>
<?php endif; ?>

<style>
<?php 
if (in_array($_POST['admin_color_scheme'], array('default', 'cherry', 'coffee', 'bluesky', 'sunset', 'blacknwhite')))
	$config['admin_color_scheme'] = $_POST['admin_color_scheme'];
?>
<?php if($config['admin_color_scheme'] == 'cherry') : ?>
/* Cherry */
.wide-header{background-color:#363b3f;border-color:#363b3f}#adminSecondary,#adminSecondary ul{background-color:#363b3f}.wide-header h1 a{color:#FFF}#admin-pane{color:#FFF;background-color:transparent}#admin-pane:hover{background-color:#11171c}#upload-pane{background-color:#e76049}#upload-pane a{color:#FFF}#upload-pane a:active{background-color:#e76049}#upload-pane:hover{text-shadow:none;background-color:#e76049}ul#sideNav li.active,ul#sideNav li:hover{color:#FFF;background-color:#cc4d4d}ul#sideNav ul li:hover,ul#sideNav ul.pm-sub-menu li.active,ul#sideNav ul.pm-sub-menu li.active:hover{border-left:3px solid #e76049;background-color:#26292c;color:#e76049}ul#sideNav li.pm-menu a:hover,ul#sideNav li.pm-menu.active>a,ul#sideNav li.pm-menu:hover>a{color:#FFF}ul#sideNav .pm-sub-menu li{background:none repeat scroll 0 0 #26292c;border-left:3px solid #26292c}ul#sideNav ul.pm-sub-menu li a{color:#FFF}ul#sideNav ul.pm-sub-menu.pm-sub-menu-side{background-color:transparent;border-top:4px solid #000;border-bottom:4px solid #000}ul.pm-sub-menu-side li{background-color:#000!important;border-color:#000!important}.pm-sub-menu-side:after{border-right-color:#000}ul#sideNav ul.pm-sub-menu li.active a,ul#sideNav ul.pm-sub-menu li.active:hover a,ul#sideNav ul.pm-sub-menu li:hover a{color:#FFF}ul#sideNav .pm-menu-count{border-color:#e76049;background-color:#e76049;background-image:none}
<?php endif; ?>
<?php if($config['admin_color_scheme'] == 'coffee') : ?>
/* Coffee */
#adminSecondary,#adminSecondary ul,.wide-header{background-color:#59524c;border-color:#59524c}.wide-header h1 a{color:#FFF}#admin-pane{color:#FFF;background-color:transparent}#admin-pane:hover{background-color:#46403c}#upload-pane{background-color:#7daa03;color:#FFF}#upload-pane a{color:#FFF}#upload-pane a:active{background-color:#7daa03}#upload-pane:hover{text-shadow:none;background-color:#7daa03}ul#sideNav li.pm-menu a{color:#ccc}ul#sideNav li.active,ul#sideNav li:hover{color:#FFF;background-color:#c7a589}ul#sideNav .pm-sub-menu li{background:none repeat scroll 0 0 #46403c;border-left:3px solid #46403c}ul#sideNav ul li:hover,ul#sideNav ul.pm-sub-menu li.active,ul#sideNav ul.pm-sub-menu li.active:hover{border-left:3px solid #46403c;background-color:#46403c}ul#sideNav li.pm-menu a:hover,ul#sideNav li.pm-menu.active>a,ul#sideNav li.pm-menu:hover>a{color:#FFF}ul#sideNav ul.pm-sub-menu li a{color:#ccc}ul#sideNav ul.pm-sub-menu li a:hover{color:#FFF}ul#sideNav ul.pm-sub-menu.pm-sub-menu-side,ul.pm-sub-menu-side li{background-color:#46403c!important;border-color:#46403c!important;border-top-color:#46403c;border-bottom-color:#46403c}.pm-sub-menu-side:after{border-right-color:#46403c}ul#sideNav ul.pm-sub-menu li.active a,ul#sideNav ul.pm-sub-menu li.active:hover a,ul#sideNav ul.pm-sub-menu li:hover a{color:#FFF}ul#sideNav .pm-menu-count,ul#sideNav .pm-submenu-count{border-color:#dd823b;background-color:#dd823b;background-image:none;color:#FFF}
<?php endif; ?>
<?php if($config['admin_color_scheme'] == 'bluesky') : ?>
/* BlueSky */
#adminSecondary,#adminSecondary ul,.wide-header{background-color:#5e8ec9;border-color:#5e8ec9}.wide-header h1 a{color:#FFF}#admin-pane{color:#FFF;background-color:transparent}#admin-pane:hover{background-color:#223449}#upload-pane{background-color:#7daa03;color:#FFF}#upload-pane a{color:#FFF}#upload-pane a:active{background-color:#7daa03}#upload-pane:hover{text-shadow:none;background-color:#7daa03}ul#sideNav li.pm-menu a{color:#ddd}ul#sideNav li.active,ul#sideNav li:hover{color:#FFF;background-color:#466a96}ul#sideNav .pm-sub-menu li{background:none repeat scroll 0 0 #384049;border-left:3px solid #384049}ul#sideNav ul li:hover,ul#sideNav ul.pm-sub-menu li.active,ul#sideNav ul.pm-sub-menu li.active:hover{border-left:3px solid #384049;background-color:#384049}ul#sideNav li.pm-menu a:hover,ul#sideNav li.pm-menu.active>a,ul#sideNav li.pm-menu:hover>a{color:#FFF}ul#sideNav ul.pm-sub-menu li a{color:#a1b7d2}ul#sideNav ul.pm-sub-menu li a:hover{color:#FFF}ul#sideNav ul.pm-sub-menu.pm-sub-menu-side,ul.pm-sub-menu-side li{background-color:#223449!important;border-color:#223449!important;border-top-color:#223449;border-bottom-color:#223449}.pm-sub-menu-side:after{border-right-color:#223449}ul#sideNav ul.pm-sub-menu li.active a,ul#sideNav ul.pm-sub-menu li.active:hover a,ul#sideNav ul.pm-sub-menu li:hover a{color:#FFF}ul#sideNav .pm-menu-count,ul#sideNav .pm-submenu-count{border-color:#ffdf6b;background-color:#ffdf6b;background-image:none;color:#000}
<?php endif; ?>
<?php if($config['admin_color_scheme'] == 'sunset') : ?>
/* Sunset */
#adminSecondary,#adminSecondary ul,.wide-header{background-color:#cb484c;border-color:#cb484c}.wide-header h1 a{color:#FFF}#admin-pane{color:#FFF;background-color:transparent}#admin-pane:hover{background-color:#dd823b}#upload-pane{background-color:#111;color:#FFF}#upload-pane a{color:#FFF}#upload-pane a:active{background-color:#439c24}#upload-pane:hover{text-shadow:none;background-color:#439c24}ul#sideNav li.pm-menu a{color:#ddd}ul#sideNav li.active,ul#sideNav li:hover{color:#FFF;background-color:#dd823b}ul#sideNav .pm-sub-menu li{background:none repeat scroll 0 0 #ba3539;border-left:3px solid #ba3539}ul#sideNav ul li:hover,ul#sideNav ul.pm-sub-menu li.active,ul#sideNav ul.pm-sub-menu li.active:hover{border-left:3px solid #46403c;background-color:#46403c}ul#sideNav li.pm-menu a:hover,ul#sideNav li.pm-menu.active>a,ul#sideNav li.pm-menu:hover>a{color:#FFF}ul#sideNav ul.pm-sub-menu li a{color:#ccc}ul#sideNav ul.pm-sub-menu li a:hover{color:#FFF}ul#sideNav ul.pm-sub-menu.pm-sub-menu-side,ul.pm-sub-menu-side li{background-color:#ba3539!important;border-color:#ba3539!important;border-top-color:#ba3539;border-bottom-color:#ba3539}.pm-sub-menu-side:after{border-right-color:#ba3539}ul#sideNav ul.pm-sub-menu li.active a,ul#sideNav ul.pm-sub-menu li.active:hover a,ul#sideNav ul.pm-sub-menu li:hover a{color:#FFF}ul#sideNav .pm-menu-count,ul#sideNav .pm-submenu-count{border-color:#4f1c1a;background-color:#4f1c1a;background-image:none;color:#FFF}
<?php endif; ?>
<?php if($config['admin_color_scheme'] == 'blacknwhite') : ?>
/* Black and White */
#adminSecondary,#adminSecondary ul,.wide-header{background-color:#161d20;border-color:#161d20}#adminSecondary,#adminSecondary ul{background-color:#1f282d}.wide-header h1 a{color:#2eb398}#admin-pane{color:#FFF;background-color:transparent}#admin-pane:hover{background-color:#2f373c}#upload-pane{background-color:#e76049;color:#FFF}#upload-pane a{color:#FFF}#upload-pane a:active{background-color:#e76049}#upload-pane:hover{text-shadow:none;background-color:#e76049}ul#sideNav li.pm-menu a{color:#ddd}ul#sideNav li.active,ul#sideNav li:hover{color:#FFF;background-color:#2f373c}ul#sideNav ul li:hover,ul#sideNav ul.pm-sub-menu li.active,ul#sideNav ul.pm-sub-menu li.active:hover{border-left:3px solid #000;background-color:#000}ul#sideNav li.pm-menu a:hover,ul#sideNav li.pm-menu.active>a,ul#sideNav li.pm-menu:hover>a{color:#FFF}ul#sideNav .pm-sub-menu li{background:none repeat scroll 0 0 #111;border-left:3px solid #111}ul#sideNav ul.pm-sub-menu li a:hover{color:#FFF}ul#sideNav ul.pm-sub-menu.pm-sub-menu-side,ul.pm-sub-menu-side li{background-color:#000!important;border-color:#000!important;border-top-color:#000;border-bottom-color:#000}.pm-sub-menu-side:after{border-right-color:#000}ul#sideNav ul.pm-sub-menu li.active a,ul#sideNav ul.pm-sub-menu li.active:hover a,ul#sideNav ul.pm-sub-menu li:hover a{color:#FFF}ul#sideNav .pm-menu-count,ul#sideNav .pm-submenu-count{border-color:#e76049;background-color:#e76049;background-image:none;color:#FFF}
<?php endif; ?>
</style>
<script type="text/javascript">
/*
 * Global js vars
 */
var pm_prevent_leaving_without_saving = false;
var pm_doing_ajax = false;
var pm_URL = '<?php echo _URL; ?>';
var pm_URL_ADMIN = '<?php echo _URL .'/'. _ADMIN_FOLDER; ?>';

var pm_prettyPop_fp_bgcolor = '<?php echo '0x' . _BGCOLOR;?>';
var pm_prettyPop_fp_timecolor = '<?php echo '0x' . _TIMECOLOR;?>';
var pm_prettyPop_fp_swf_loc = '<?php echo _URL .'/players/flowplayer2/flowplayer.swf';  ?>';

var MELODYURL = '<?php echo _URL; ?>';
var MELODYURL2 = '<?php echo _URL2; ?>';
 
// @since v2.7
var phpmelody = { 
	url: '<?php echo _URL; ?>',
	admin_url: '<?php echo _URL .'/'. _ADMIN_FOLDER; ?>',
	admin_ajax_url: '<?php echo _URL .'/'. _ADMIN_FOLDER .'/admin-ajax.php'; ?>',
	prevent_leaving_without_saving: false,
	doing_ajax: false,
	max_file_size_bytes: <?php echo $upload_max_filesize; ?>, 
	max_file_size_readable: '<?php echo readable_filesize(get_true_max_filesize()); ?>'
};
</script>

</head>
<body>
<div id="loading">Loading ...</div>
<!-- Masthead
================================================== -->
<header class="wide-header" id="overview">
<div class="row-fluid">
	<div class="span9">
	  <h1><a href="<?php echo _URL;?>" rel="tooltip" data-placement="right" title="Switch to front-end"><?php echo ($config['homepage_title'] == '') ? 'PHP Melody' : htmlspecialchars($config['homepage_title']); ?></a></h1>
   </div>
	<div class="span3">
		<div id="admin-pane" class="pull-right">
			<div class="user-data">
			<span class="user-avatar">
			<?php if (version_compare($official_version, $config['version']) == 1) : ?>
			<span class="user-notification">1</span>
			<?php endif; ?>
			
			<?php if(($tab_internallog > 0) && is_admin()) {?><span class="user-notification"><?php echo pm_number_format($tab_internallog); ?></span><?php }elseif(($tab_video_total > 0) && is_moderator() && $mod_can['manage_videos']) { ?><span class="user-notification"><?php echo $tab_video_total; ?></span><?php } ?>
			
			<span class="user-avatar-img"><img src="<?php echo _AVATARS_DIR . $userdata['avatar']; ?>" height="27" width="27" border="0" class="img-rounded" alt="" /></span>
			</span>
			<span class="greet-links">
			<div class="ellipsis"><strong><?php echo ucwords($userdata['name']);?></strong></div>
			</span>
			</div>
			<div class="user-menu">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-chevron-down icon-white opac7"></i></a>
				<ul class="dropdown-menu pull-right pm-ul-user-menu" role="menu" aria-labelledby="dLabel" data-hover="dropdown" data-delay="100" data-close-others="false">
					<?php if (is_admin()) : ?>
					<?php if (version_compare($official_version, $config['version']) == 1) : ?>
					<li><a tabindex="-1" href="#" style="color: #C30;">Update this installation</a></li>
					<?php endif; ?>
					<?php if($tab_internallog > 0) : ?>
					<li><a tabindex="-1" href="readlog.php">Recorded issues</a><span class="user-notification" style="left: 88%; top: 8px;"><?php echo $tab_internallog; ?></span></li>
					<?php endif; ?>
					<li><a tabindex="-1" href="settings.php">Settings</a></li>
					<li><a tabindex="-1" href="settings_theme.php">Layout settings</a></li>
					<li><a tabindex="-1" href="password.php">Change password</a></li>
					<li class="divider"></li>
				<?php endif; ?>
				<li><a tabindex="-1" href="<?php echo _URL; ?>/login.php?do=logout">Logout</a></li>
				</ul>
			</div>
		</div>

<?php if ((is_moderator() && $mod_can['manage_videos']) || is_admin()) : ?>
		<div id="upload-pane" class="pull-right">
		<a href="#addVideo" data-toggle="modal">ADD VIDEO</a>
		</div>
<?php endif; ?>
	</div>
</div>
</header>
<a id="top"></a>
<?php
include_once('sideNav.php');

if ( ! is_admin() && is_array($allowed_access) && ! in_array($showm, $allowed_access)) 
{
	restricted_access(true);
}
$official_version = cache_this('read_version', 'pm_version'); 
?>





<?php if(file_exists("db_update.php") && $hide_update_notification != 1) : ?>
	<?php if ((version_compare($official_version, $config['version'], '=='))) : ?>
	<div class="dbupdate-bar animated flash"><strong><strong>Important:</strong> Delete <code><?php echo _ADMIN_FOLDER;?>/db_update.php</code> right now.</strong></div>
	<?php else : ?>
	<div class="dbupdate-bar animated flash"><strong>PHP Melody Update: <a href="db_update.php">Finalize the update process now</a>. Do not skip this final step.</strong></div>
	<?php endif; ?>
<?php elseif (version_compare($official_version, $config['version']) == 1 && $hide_update_notification != 1) : ?>
<div class="new-release-bar">A newer version of <strong>PHP Melody</strong> is available! <a href="https://www.phpsugar.com/customer/" target="_blank">Click here to download the v<?php echo $official_version; ?> update</a>. </div>
<?php else : ?>
<div class="clearheaderfix"></div>
<?php endif; ?>
