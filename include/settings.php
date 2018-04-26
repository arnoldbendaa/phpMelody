<?php
include_once(ABSPATH .'include/cache.class.php');
$_pm_cache = new PhpmelodyCache();

require_once(ABSPATH .'include/functions.php');

if ( ! extension_loaded('mysql'))
{
	include_once(ABSPATH .'include/mysql2i.class.php');
}

// globals
$conn_id = db_connect();

if ( ! $conn_id)
{
	exit('<h1>Error establishing a database connection</h1>');
}

$config = get_config();

date_default_timezone_set( ($config['timezone'] != '') ? $config['timezone'] : 'UTC' );

$time_now = time();
$date_now = getdate();
$time_now_minute = mktime($date_now['hours'], $date_now['minutes'], ceil($date_now['seconds']/10)*10, $date_now['mon'], $date_now['mday'], $date_now['year']);
//$time_now_minute = $time_now;

$_video_categories = null;
$_article_categories = null;
$_countries_list = array();
$_sources = fetch_video_sources(); 

if ($config['mod_article'] == '1')
{
	require_once(ABSPATH .'include/article_functions.php');
}

if ($config['mod_social'] == '1')
{
	require_once(ABSPATH .'include/social_settings.php');
	require_once(ABSPATH .'include/social_functions.php');
}

//	Configs
$template_f = $config['template_f']; // Your current template this value should reflect the folder's name
define('_UPFOLDER', 'uploads'); // NO NEED TO EDIT THIS [!!!] The upload folder name for THUMBS & user AVATARS.
define('_EMAIL', $config['contact_mail']); // Your personal e-mail address (Contact form messages will be delivered to this email).
define('_THUMB_FROM', (int) $config['thumb_from']); // FETCH THUMBS FROM YOUTUBE OR LOCALHOST ? (1 = Youtube.com // 2 = Your server)
define('_BROWSER_PAGE', (int) $config['browse_page']); // Number of results per category page
define('_ISNEW_DAYS', (int) $config['isnew_days']); // How many days should a video stay marked as 'NEW'.
define('_ISPOPULAR', (int) $config['ispopular']); // Define the minimum number of views a video needs to become tagged as POPULAR
define('_STOPBADCOMMENTS', (int) $config['stopbadcomments']); // Don't post comments that contain bad words: bad_words.txt
define('_HTMLCOUNTER', stripslashes($config['counterhtml']));
define('_FAV_LIMIT', (int) $config['fav_limit']); // Favorite videos limit / user

define('_PM_VERSION', $config['version']); // PHP MELODY VERSION
define('_TPLFOLDER', $config['template_f']); // CURRENT YOUTUBE URL FOR THUMBS
define('_SEOMOD', (int) $config['seomod']);	// SHOW SEO FRIENDLY URLS OR NOT
define('_MOD_ARTICLE', (int) $config['mod_article']);
define('_ALLOW_USER_UPLOADVIDEO', (int) $config['allow_user_uploadvideo']);
define('_ALLOW_USER_SUGGESTVIDEO', (int) $config['allow_user_suggestvideo']);
define('_MOD_SOCIAL', (int) $config['mod_social']);
define('_DISABLE_INDEXING', $config['disable_indexing']); // Discourage Search Engines From Indexing The Entire Site
define('_IS_RTL', $config['rtl_support']);
define('_EU_WARNING', $config['eu_cookie_warning']);
define('_EU_WARNING_POSITION', $config['eu_cookie_warning_position']);

define('DAY_IN_SECONDS', 86400);
define('WEEK_IN_SECONDS', 604800);
define('MONTH_IN_SECONDS', 2592000);

// ad types
define('_AD_TYPE_CLASSIC', 1);
define('_AD_TYPE_VIDEO', 2);
define('_AD_TYPE_PREROLL', 3);

// playlist types
define('PLAYLIST_TYPE_CUSTOM', 0); // a.k.a User Defined
define('PLAYLIST_TYPE_WATCH_LATER', 1);
define('PLAYLIST_TYPE_FAVORITES', 2);
define('PLAYLIST_TYPE_LIKED', 3);
define('PLAYLIST_TYPE_HISTORY', 4);

// playlist visibility
define('PLAYLIST_PUBLIC', 1);
define('PLAYLIST_PRIVATE', 0);

if ( ! defined('_SITENAME')) // to avoid any possible issues with future versions
{
	define('_SITENAME', $config['homepage_title']);
}

define('_ISSMTP', (int) $config['issmtp']);

define('_USE_HQ_VIDS', (int) $config['use_hq_vids']);

if(_SEOMOD == '1')
	define('_FEXT', 'html');
else
	define('_FEXT', 'php');

define('_TOPVIDS', (int) $config['top_videos']);
define('_NEWVIDS', (int) $config['new_videos']);

//	Item types
define('IS_VIDEO', 1);
define('IS_ARTICLE', 2);
define('IS_PAGE', 3);

//	Comments moderation levels
define('MODERATE_ALL',    2);
define('MODERATE_GUESTS', 1);
define('MODERATE_NONE',   0);

//	Account activation levels
define('AA_DISABLED', 0);
define('AA_USER',     1);
define('AA_ADMIN',    2);

//	Users power levels
define('U_ACTIVE',   0); //	active, registered user
define('U_ADMIN',    1); // master
define('U_INACTIVE', 2); //	inactive, registered user
define('U_MODERATOR', 3);
define('U_EDITOR', 4);

// URLs
define('_VIDEOS_DIR', _URL .'/' . _UPFOLDER .'/videos/');
define('_THUMBS_DIR',  _URL .'/' . _UPFOLDER .'/thumbs/');
define('_NOTHUMB',  _URL .'/templates/'. _TPLFOLDER .'/img/no-thumbnail.jpg');
define('_SUBTITLES_DIR', _URL .'/'. _UPFOLDER .'/subtitles/');
define('_ARTICLE_ATTACH_DIR', _URL .'/'. _UPFOLDER .'/articles/');
define('_AVATARS_DIR', _URL .'/'. _UPFOLDER .'/avatars/');
define('_COVERS_DIR', _URL .'/'. _UPFOLDER .'/covers/');

//	Paths
define('_VIDEOS_DIR_PATH', ABSPATH . _UPFOLDER .'/videos/');
define('_THUMBS_DIR_PATH', ABSPATH . _UPFOLDER .'/thumbs/');
define('_ARTICLE_ATTACH_DIR_PATH', ABSPATH . _UPFOLDER .'/articles/');
define('_SUBTITLES_DIR_PATH', ABSPATH . _UPFOLDER .'/subtitles/');
define('_AVATARS_DIR_PATH', ABSPATH . _UPFOLDER .'/avatars/');
define('_COVERS_DIR_PATH', ABSPATH . _UPFOLDER .'/covers/');

//	Thumbnail sizes (px)
define('THUMB_W_VIDEO', $config['thumb_video_w']);
define('THUMB_H_VIDEO', $config['thumb_video_h']);
define('THUMB_W_ARTICLE', $config['thumb_article_w']);
define('THUMB_H_ARTICLE', $config['thumb_article_h']);
define('THUMB_W_AVATAR', $config['thumb_avatar_w']);
define('THUMB_H_AVATAR', $config['thumb_avatar_h']);

$url2 = ((ssl_enabled()) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
$temp = str_replace(array('http://', 'https://'), '', _URL);
$temp = explode("/", $temp);
$count = count($temp);
for($i = 1; $i < $count; $i++)
{
	$url2 .= "/".$temp[$i];
}
$url2 = rtrim($url2, "/");

define('_URL2', $url2);
unset($temp, $count, $url2);

/*
 *  Video Player configs
 */
//	'Index' player width and height
if($config['player_w_index'] != '')
	define('_PLAYER_W_INDEX', $config['player_w_index']);
else
	define('_PLAYER_W_INDEX', 430);
if($config['player_h_index'] != '')
	define('_PLAYER_H_INDEX', $config['player_h_index']);
else
	define('_PLAYER_H_INDEX', 344);

//	'My Favorites' player width and height
if($config['player_w_favs'] != '')
	define('_PLAYER_W_FAVS', $config['player_w_favs']);
else
	define('_PLAYER_W_FAVS', 575);
if($config['player_h_favs'] != '')
	define('_PLAYER_H_FAVS', $config['player_h_favs']);
else
	define('_PLAYER_H_FAVS', 466);

//	Default player width and height
if($config['player_w'] != '')
	define('_PLAYER_W', $config['player_w']);
else
	define('_PLAYER_W', 496);
if($config['player_h'] != '')
	define('_PLAYER_H', $config['player_h']);
else
	define('_PLAYER_W', 401);

//	Embed player width and height
if($config['player_w_embed'] != '')
	define('_PLAYER_W_EMBED', $config['player_w_embed']);
else
	define('_PLAYER_W_EMBED', 425);
if($config['player_h_embed'] != '')
	define('_PLAYER_H_EMBED', $config['player_h_embed']);
else
	define('_PLAYER_W_EMBED', 344);

if($config['player_autoplay'] == 1)
	define('_AUTOPLAY', 'true');
else
	define('_AUTOPLAY', ($_COOKIE['pm_autoplay_next'] == 'on') ? 'true' : 'false');

if($config['featured_autoplay'] == 1)
	define('_AUTOPLAY_FEATURED', 'true');
else
	define('_AUTOPLAY_FEATURED', 'false');

if($config['player_autobuff'] == 1)
	define('_AUTOBUFF', 'true');
else
	define('_AUTOBUFF', 'false');

if($config['player_bgcolor'] != '')
	define('_BGCOLOR', $config['player_bgcolor']);
else
	define('_BGCOLOR', '253133');

if($config['player_timecolor'] != '')
	define('_TIMECOLOR', $config['player_timecolor']);
else
	define('_TIMECOLOR', 'FFCC00');

if($config['player_watermarkshow'] == "always" || $config['player_watermarkshow'] == "fullscreen")
	define('_WATERMARKSHOW', $config['player_watermarkshow']);
else
	define('_WATERMARKSHOW', 'fullscreen');

if($config['player_watermarklink'] != '')
	define('_WATERMARKLINK', $config['player_watermarklink']);
else
	define('_WATERMARKLINK', _URL."/");

if($config['jwplayerskin'] != '')
	define('_JWSKIN', $config['jwplayerskin']);
else
	define('_JWSKIN', "glow.zip");

define('_WATERMARKURL', make_url_https($config['player_watermarkurl']));

define('_SEARCHSUGGEST', $config['search_suggest']);

// Initialize SMARTY
require(ABSPATH . 'Smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->template_dir = 	ABSPATH . "templates/"._TPLFOLDER;	//NO trailing or preceding slash!
$smarty->compile_dir =		ABSPATH . "Smarty/templates_c"; 	//NO trailing or preceding slash!
$smarty->cache_dir =  		ABSPATH . "Smarty/cache"; 			//NO trailing or preceding slash!
$smarty->config_dir = 		ABSPATH . "Smarty/configs"; 		//NO trailing or preceding slash!

// Theme customizations & logo
apply_theme_customizations();
$smarty->assign('_custom_logo_url', $config['custom_logo_url']);
$smarty->assign('_footer_switch_ui_link', get_switch_ui_url());

// Cookie Settings
define('COOKIE_SUFX', md5(_URL));
define('COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', _URL.'/' ));
define('COOKIE_NAME', 'melody_'.COOKIE_SUFX);
define('COOKIE_KEY', 'melody_key_'.COOKIE_SUFX);
define('COOKIE_TIME', 864000);		//	10 days
define('COOKIE_AUTHOR', 'guest_name_'.COOKIE_SUFX);
define('COOKIE_VIDEOAD', 'melody_vad_'.COOKIE_SUFX);
define('COOKIE_LANG', 'melody_lang_'.COOKIE_SUFX);
define('COOKIE_PREROLLAD', 'melody_pad_'.COOKIE_SUFX);
define('PREROLL_AD_HASH', substr(COOKIE_SUFX, 0, 12));

$parsed_url = parse_url(_URL);
if (ssl_enabled())
{
	define('COOKIE_SECURE', true);
}
else
{
	define('COOKIE_SECURE', false);
}

if (version_compare(phpversion(), '5.2', '>='))
{
	define('COOKIE_DOMAIN', false);
	define('COOKIE_HTTPONLY', true);
}
else
{
	$cookie_domain = false;
	if ($parsed_url['host'] != 'localhost')
	{
		$pieces = explode('.', $parsed_url['host']);
		$pieces_count = count($pieces);

		$cookie_domain = '.'. $pieces[$pieces_count - 2] . '.'. $pieces[$pieces_count - 1];
		$cookie_domain .= '; HttpOnly';
		unset($pieces, $pieces_count);
	}
	define('COOKIE_DOMAIN', $cookie_domain);
	define('COOKIE_HTTPONLY', false);
	unset($cookie_domain);
}
unset($parsed_url);

//	Ads System
$config['show_ads'] = 1;
if($config['show_ads'] == 1)
{
	$ads = array();

	if ($result = mysql_query("SELECT * FROM pm_ads WHERE active = '1'"))
	{
		while($row = mysql_fetch_assoc($result))
		{
			$ads[$row['id']] = $row;
		}
		$total_ads = count($ads);
		if($total_ads != 0)
		{
			foreach($ads as $k => $v)
			{
				if($v['code'] != '')
				{
					if ($v['disable_stats'] == 0)
					{
						$v['code'] .= '<img src="'. _URL .'/ajax.php?p=stats&do=show&aid='. $v['id'] .'&at='. _AD_TYPE_CLASSIC .'" width="1" height="1" border="0" />';
					}
					
					$smarty->assign('ad_'.$v['id'], $v['code']);
				}
			}
		}
		mysql_free_result($result);
	}
}

$default_language = 'english';
$_language_email_dir = 'english';

$langs = array();

//	English
$langs[1]["title"]	= "English";
$langs[1]["ico"]	= _URL . "/lang/flags/us.png";
//$langs[1]["ico"]	= _URL . "/lang/flags/gb.png";
$langs[1]["file"]	= 'english.php';
$langs[1]["email_dir"] = 'english';

//	Albanian
$langs[2]["title"]	= "Albanian";
$langs[2]["ico"]	= _URL . "/lang/flags/al.png";
$langs[2]["file"]	= 'albanian.php';
$langs[2]["email_dir"] = 'albanian';

//	Arabic
$langs[3]["title"]	= "Arabic";
$langs[3]["ico"]	= _URL . "/lang/flags/sa.png";
$langs[3]["file"]	= 'arabic.php';
$langs[3]["email_dir"] = 'arabic';

//	Brazilian
$langs[4]["title"] = "Brazilian";
$langs[4]["ico"]	= _URL . "/lang/flags/pt-br.png";
$langs[4]["file"]	= 'brazilian.php';
$langs[4]["email_dir"] = 'brazilian';

//	Bulgarian
$langs[5]["title"]	= "Bulgarian";
$langs[5]["ico"]	= _URL . "/lang/flags/bg.png";
$langs[5]["file"]	= 'bulgarian.php';
$langs[5]["email_dir"] = 'bulgarian';

//	Croatian
$langs[6]["title"]	= "Croatian";
$langs[6]["ico"]	= _URL . "/lang/flags/hr.png";
$langs[6]["file"]	= 'croatian.php';
$langs[6]["email_dir"] = 'croatian';

//	Danish
$langs[7]["title"]	= "Danish";
$langs[7]["ico"]	= _URL . "/lang/flags/dk.png";
$langs[7]["file"]	= 'danish.php';
$langs[7]["email_dir"] = 'danish';

//	German
$langs[8]["title"]	= "Deutsch";
$langs[8]["ico"]	= _URL . "/lang/flags/de.png";
$langs[8]["file"]	= 'german.php';
$langs[8]["email_dir"] = 'german';

//	French
$langs[9]["title"]	= "Fran&#231;ais";
$langs[9]["ico"]	= _URL . "/lang/flags/fr.png";
$langs[9]["file"]	= 'french.php';
$langs[9]["email_dir"] = 'french';

//	Georgian
$langs[10]["title"]	= "Georgian";
$langs[10]["ico"]	= _URL . "/lang/flags/ge.png";
$langs[10]["file"]	= 'georgian.php';
$langs[10]["email_dir"] = 'georgian';

//	Italian
$langs[11]["title"]	= "Italian";
$langs[11]["ico"]	= _URL . "/lang/flags/it.png";
$langs[11]["file"]	= 'italiano.php';
$langs[11]["email_dir"] = 'italiano';

//	Lithuanian
$langs[12]["title"]	= "Lithuanian";
$langs[12]["ico"]	= _URL . "/lang/flags/lt.png";
$langs[12]["file"]	= 'lithuanian.php';
$langs[12]["email_dir"] = 'lithuanian';

//	Dutch
$langs[13]["title"]	= "Nederlands";
$langs[13]["ico"]	= _URL . "/lang/flags/nl.png";
$langs[13]["file"]	= 'dutch.php';
$langs[13]["email_dir"] = 'dutch';

//	Polish
$langs[14]["title"] = "Polish";
$langs[14]["ico"]	= _URL . "/lang/flags/pl.png";
$langs[14]["file"]	= 'polish.php';
$langs[14]["email_dir"] = 'polish';

//	Portuguese
$langs[15]["title"]	= "Portuguese";
$langs[15]["ico"]	= _URL . "/lang/flags/pt.png";
$langs[15]["file"]	= 'portuguese.php';
$langs[15]["email_dir"] = 'portuguese';

//	Romanian
$langs[16]["title"]	= "Rom&#226;n&#259;";
$langs[16]["ico"]	= _URL . "/lang/flags/ro.png";
$langs[16]["file"]	= 'romanian.php';
$langs[16]["email_dir"] = 'romanian';

//	Russian
$langs[17]["title"]	= "Russian";
$langs[17]["ico"]	= _URL . "/lang/flags/ru.png";
$langs[17]["file"]	= 'russian.php';
$langs[17]["email_dir"] = 'russian';

//	Serbian
$langs[18]["title"]	= "Serbian";
$langs[18]["ico"]	= _URL . "/lang/flags/sr.png";
$langs[18]["file"]	= 'serbian.php';
$langs[18]["email_dir"] = 'serbian';

//	Slovak
$langs[19]["title"] = "Slovak";
$langs[19]["ico"]	= _URL . "/lang/flags/sk.png";
$langs[19]["file"]	= 'slovak.php';
$langs[19]["email_dir"] = 'slovak';

//	Spanish
$langs[20]["title"]	= "Spanish";
$langs[20]["ico"]	= _URL . "/lang/flags/es.png";
$langs[20]["file"]	= 'spanish.php';
$langs[20]["email_dir"] = 'spanish';

//	Swedish
$langs[21]["title"]	= "Swedish";
$langs[21]["ico"]	= _URL . "/lang/flags/se.png";
$langs[21]["file"]	= 'swedish.php';
$langs[21]["email_dir"] = 'swedish';

//	Turkce
$langs[22]["title"]	= "T&uuml;rk&ccedil;e";
$langs[22]["ico"]	= _URL . "/lang/flags/tr.png";
$langs[22]["file"]	= 'turkce.php';
$langs[22]["email_dir"] = 'turkce';

//	Hebrew
$langs[23]["title"]	= "Hebrew";
$langs[23]["ico"]	= _URL . "/lang/flags/il.png";
$langs[23]["file"]	= 'hebrew.php';
$langs[23]["email_dir"] = 'hebrew';

//	Thai
$langs[24]["title"]	= "Thai";
$langs[24]["ico"]	= _URL . "/lang/flags/th.png";
$langs[24]["file"]	= 'thai.php';
$langs[24]["email_dir"] = 'thai';

//	Farsi/Persian
$langs[25]["title"]	= "Farsi";
$langs[25]["ico"]	= _URL . "/lang/flags/ir.png";
$langs[25]["file"]	= 'farsi.php';
$langs[25]["email_dir"] = 'farsi';

//	Amharic/Semitic-Ethiopia
$langs[26]["title"]	= "Amharic";
$langs[26]["ico"]	= _URL . "/lang/flags/am.png";
$langs[26]["file"]	= 'amharic.php';
$langs[26]["email_dir"] = 'amharic';

//	Greek
$langs[27]["title"]	= "Greek";
$langs[27]["ico"]	= _URL . "/lang/flags/gr.png";
$langs[27]["file"]	= 'greek.php';
$langs[27]["email_dir"] = 'greek';

$lang_id = 1; 	//	english by default

if($config['default_lang'] != 0 && @array_key_exists($config['default_lang'], $langs))
{
	$lang_id = $config['default_lang'];
	$_language_email_dir = $langs[ $lang_id ]["email_dir"];
}

if(isset($_COOKIE[COOKIE_LANG]))
{
	if(@array_key_exists($_COOKIE[COOKIE_LANG], $langs))
	{
		$lang_id = (int) $_COOKIE[COOKIE_LANG];
	}
}

if(@file_exists( ABSPATH . "lang/" . $langs[ $lang_id ]["file"]) === FALSE)
{
	$error = "Error: Language file not found.";
	if($lang_id > 1)
	{
		$lang_id = 1;
		if(@file_exists( ABSPATH . "lang/" . $langs[ $lang_id ]["file"]) === FALSE)
		{
			echo $error;
			exit();
		}
		else
		{
			@include_once(ABSPATH . "lang/" . $langs[ $lang_id ]["file"]);
		}
	}
}
else
{
	@include_once(ABSPATH . "lang/" . $langs[ $lang_id ]["file"]);
}

@asort($langs);

$smarty->assign('lang', $lang);
$smarty->assign('langs_array', $langs);
$smarty->assign('current_lang_id', $lang_id);

if ((int) $config['maintenance_mode'] == 1 && ! defined('IGNORE_MAINTENANCE_MODE'))
{
	if ( ! function_exists('is_user_logged_in'))
	{
		include(ABSPATH .'include/user_functions.php');
	}
	
	require_once(ABSPATH.'include/mmodframework.class.php');
	$modframework = new modframework();
	if(isset($config['mm_framework']) && $config['mm_framework'] != 0) $modframework->initframework();
	
	include(ABSPATH .'include/islogged.php');

	$x = explode('/', $_SERVER['SCRIPT_NAME']);
	$script_name = array_pop($x);
	$dir_name = array_pop($x);

	if ($dir_name != _ADMIN_FOLDER && $userdata['power'] != U_ADMIN && $userdata['power'] != U_MODERATOR)
	{
		$smarty->assign('maintenance_display_message', ($config['maintenance_display_message'] != '') ? $config['maintenance_display_message'] : $lang['default_maintenance_message']);
		
		$smarty->assign('meta_title', htmlspecialchars( ('' != $config['homepage_title']) ? $config['homepage_title'] : sprintf($lang['homepage_title'], _SITENAME)) );
		$smarty->display('maintenance.tpl');

		if ($conn_id)
		{
			mysql_close($conn_id);
		}

		exit();
	}
	
	$smarty->assign('maintenance_mode', true);
	// continue
}

$smarty->register_function('smarty_fewchars', 'smarty_fewchars');
$smarty->register_function('echo_securimage_sid', 'smarty_echo_securimage_sid');
$smarty->register_function('get_advanced_video_list', 'smarty_get_advanced_video_list', false);
$smarty->register_function('dropdown_menu_video_categories', 'smarty_html_list_categories');
$smarty->register_function('pm_number_format', 'smarty_pm_number_format');
$smarty->assign('allow_registration', $config['allow_registration']);
$smarty->assign('comment_system_native', (int) $config['comment_system_native']);
$smarty->assign('comment_system_facebook', (int) $config['comment_system_facebook']);
$smarty->assign('comment_system_disqus', (int) $config['comment_system_disqus']);
$smarty->assign('allow_emojis', ($config['comment_system'] != 'on' || $config['comment_system_native'] == 0) ? 0 : (int) $config['allow_emojis']);
$smarty->assign('disqus_shortname', $config['disqus_shortname']);
$smarty->assign('fb_comment_numposts', ($config['comments_page'] > 100) ? 100 : $config['comments_page']);
$smarty->assign('fb_comment_sorting', $config['fb_comment_sorting']);
$smarty->assign('fb_app_id', $config['fb_app_id']);
$smarty->assign('_sources', $_sources);
$smarty->register_function('make_url_https', 'smarty_make_url_https');
$smarty->register_function('make_url_relative', 'smarty_make_url_relative');
$smarty->register_function('filter_text_https_friendly', 'smarty_filter_text_https_friendly');

// @since v2.7 -- made categories array globally available
include_once(ABSPATH .'include/user_functions.php');
$smarty->assign('_video_categories', load_categories(array('with_image' => true)));
$smarty->assign('_article_categories', (_MOD_ARTICLE) ? load_categories(array('db_table' => 'art_categories')) : null);

$_comment_primary = $config['comment_system_primary'];
if (isset($_COOKIE['pm_comment_view']) && in_array($_COOKIE['pm_comment_view'], array('native', 'facebook', 'disqus')))
{
	if ( ! $config['comment_system_'. $_COOKIE['pm_comment_view']])
	{
		$smarty->assign('comment_system_primary', $config['comment_system_primary']);
	}
	else
	{
		$_comment_primary = $_COOKIE['pm_comment_view'];
		$smarty->assign('comment_system_primary', $_COOKIE['pm_comment_view']);
	}
}
else
{
	$smarty->assign('comment_system_primary', $config['comment_system_primary']);
}

if (_MOD_ARTICLE)
{
	$smarty->register_function('dropdown_menu_article_categories', 'smarty_art_html_list_categories', false);
}
else
{
	function smarty_art_html_list_categories($params, &$smarty)
	{
		return '';
	}
	$smarty->register_function('dropdown_menu_article_categories', 'smarty_art_html_list_categories', false);
}

include(ABSPATH .'include/page_functions.php');

$smarty->register_function('get_video_meta_list', 'smarty_get_video_meta_list');
$smarty->register_function('get_video_meta', 'smarty_get_video_meta');
$smarty->register_function('get_article_meta_list', 'smarty_get_article_meta_list');
$smarty->register_function('get_article_meta', 'smarty_get_article_meta');
$smarty->register_function('get_page_meta_list', 'smarty_get_page_meta_list');
$smarty->register_function('get_page_meta', 'smarty_get_page_meta');

$header_page_links = '';
$footer_page_links = '';
$links_to_pages = ''; // @Legacy code for older Themes and Mobile Melody; since v2.3

if ($config['total_pages'] > 0)
{
	$header_page_links = generate_page_links('header');
	$footer_page_links = generate_page_links();
	$links_to_pages = $footer_page_links; // @Legacy code for older Themes and Mobile Melody; since v2.3

	$smarty->assign('header_page_links', $header_page_links);
	$smarty->assign('footer_page_links', $footer_page_links);
	$smarty->assign('links_to_pages', $links_to_pages); // @Legacy code for older Themes and Mobile Melody; since v2.3
}

// JW Player Keys
$smarty->assign('jwplayerkey', $config['jwplayerkey']);
$smarty->assign('jwplayer7key', $config['jwplayer7key']);

$smarty->assign('allow_facebook_login', $config['oauth_facebook']);
$smarty->assign('allow_twitter_login', $config['oauth_twitter']);

session_save_footprint();

$x = explode('/', $_SERVER['SCRIPT_NAME']);
$script_name = array_pop($x);

$smarty->assign('_script_name', $script_name);

unset($dir_name, $script_name, $x);
require_once(ABSPATH.'include/mmodframework.class.php');
$modframework = new modframework();
if(isset($config['mm_framework']) && $config['mm_framework'] != 0) $modframework->initframework();
