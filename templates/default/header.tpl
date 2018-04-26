<!DOCTYPE html>
<!--[if IE 7 | IE 8]>
<html class="ie" dir="{if $smarty.const._IS_RTL == '1'}rtl{else}ltr{/if}">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="{if $smarty.const._IS_RTL == '1'}rtl{else}ltr{/if}">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1024,maximum-scale=1.0">
<title>{$meta_title}</title>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=edge,chrome=1">  
{if $no_index == '1' || $smarty.const._DISABLE_INDEXING == '1'}
<meta name="robots" content="noindex,nofollow">
<meta name="googlebot" content="noindex,nofollow">
{/if}
<meta name="title" content="{$meta_title}" />
<meta name="keywords" content="{$meta_keywords}" />
<meta name="description" content="{$meta_description}" />
<link rel="shortcut icon" href="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/favicon.ico">
{if $tpl_name == "video-category"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?c={$cat_id}" />
{elseif $tpl_name == "video-top"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?feed=topvideos" />
{elseif $tpl_name == "article-category"}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php?c={$cat_id}&feed=articles" />
{else}
<link rel="alternate" type="application/rss+xml" title="{$meta_title}" href="{$smarty.const._URL}/rss.php" />
{/if}
{if $comment_system_facebook && $fb_app_id != ''}
<meta property="fb:app_id" content="{$fb_app_id}" />
{/if}
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap.min.css">
{if $smarty.const._IS_RTL == '1'}
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap.min.rtl.css">
{/if}
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/bootstrap-responsive.min.css">
<!--[if lt IE 9]>
<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/new-style.css">
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/custom.css">
{if $tpl_name == 'video-edit'}
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/uniform.default.min.css">
{/if}
<link rel="stylesheet" type="text/css" media="screen" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&subset=all" rel="stylesheet" type="text/css">
<!--[if IE]>
{literal}
<link rel="stylesheet" type="text/css" media="screen" href="{/literal}{$smarty.const._URL}{literal}/templates/{/literal}{$template_dir}{literal}/css/new-style-ie.css">
{/literal}
<link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet" type="text/css">
<link href="//fonts.googleapis.com/css?family=Open+Sans:700italic" rel="stylesheet" type="text/css">
<![endif]-->
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
{if $tpl_name == 'video-watch' && $playlist}
<link rel="canonical" href="{$video_data.video_href}"/>
{/if}
<script type="text/javascript">
var MELODYURL = "{$smarty.const._URL}";
var MELODYURL2 = "{$smarty.const._URL2}";
var TemplateP = "{$smarty.const._URL}/templates/{$template_dir}";
var _LOGGEDIN_ = {if $logged_in} true {else} false {/if};
 
{if $tpl_name == 'index' || $tpl_name == 'video-watch'}
{literal}
var pm_video_data = {
{/literal}	
	uniq_id: "{$video_data.uniq_id}",
	url: "{$video_data.video_href}",
	duration: {$video_data.yt_length|default:0},
	duration_str: "{$video_data.duration}",
	category: "{$video_data.category}".split(','),
	category_str: "{$video_data.category}",
	featured: {$video_data.featured|default:0},
	restricted: {$video_data.restricted|default:0},
	allow_comments: {$video_data.allow_comments|default:0},
	allow_embedding: {$video_data.allow_embedding|default:0},
	is_stream: {if $video_data.is_stream}true{else}false{/if},
	views: {$video_data.site_views|default:0},
	likes: {$video_data.likes|default:0},
	dislikes: {$video_data.dislikes|default:0},
	publish_date_str: "{$video_data.html5_datetime}",
	publish_date_timestamp: {$video_data.added_timestamp|default:0},
	embed_url: "{$video_data.embed_href}",
	thumb_url: "{$video_data.thumb_img_url}",
	preview_image_url: "{$video_data.preview_image}",
	title: '{$video_data.video_title|escape:'quotes'}',
	autoplay_next: {if $video_data.autoplay_next}true{else}false{/if},
	autoplay_next_url: "{$video_data.autoplay_next_url}"
{literal}
}
{/literal}
{/if}
</script>
{literal}
<script type="text/javascript">
 var pm_lang = {
	lights_off: "{/literal}{$lang.lights_off}{literal}",
	lights_on: "{/literal}{$lang.lights_on}{literal}",
	validate_name: "{/literal}{$lang.validate_name}{literal}",
	validate_username: "{/literal}{$lang.validate_username}{literal}",
	validate_pass: "{/literal}{$lang.validate_pass}{literal}",
	validate_captcha: "{/literal}{$lang.validate_captcha}{literal}",
	validate_email: "{/literal}{$lang.validate_email}{literal}",
	validate_agree: "{/literal}{$lang.validate_agree}{literal}",
	validate_name_long: "{/literal}{$lang.validate_name_long}{literal}",
	validate_username_long: "{/literal}{$lang.validate_username_long}{literal}",
	validate_pass_long: "{/literal}{$lang.validate_pass_long}{literal}",
	validate_confirm_pass_long: "{/literal}{$lang.validate_confirm_pass_long}{literal}",
	choose_category: "{/literal}{$lang.choose_category}{literal}",
	validate_select_file: "{/literal}{$lang.upload_errmsg10}{literal}",
	validate_video_title: "{/literal}{$lang.validate_video_title}{literal}",
	please_wait: "{/literal}{$lang.please_wait}{literal}",
	// upload video page
	swfupload_status_uploaded: "{/literal}{$lang.swfupload_status_uploaded}{literal}",
	swfupload_status_pending: "{/literal}{$lang.swfupload_status_pending}{literal}",
	swfupload_status_queued: "{/literal}{$lang.swfupload_status_queued}{literal}",
	swfupload_status_uploading: "{/literal}{$lang.swfupload_status_uploading}{literal}",
	swfupload_file: "{/literal}{$lang.swfupload_file}{literal}",
	swfupload_btn_select: "{/literal}{$lang.swfupload_btn_select}{literal}",
	swfupload_btn_cancel: "{/literal}{$lang.swfupload_btn_cancel}{literal}",
	swfupload_status_error: "{/literal}{$lang.swfupload_status_error}{literal}",
	swfupload_error_oversize: "{/literal}{$lang.swfupload_error_oversize}{literal}",
	swfupload_friendly_maxsize: "{/literal}{$upload_limit}{literal}",
	upload_errmsg2: "{/literal}{$lang.upload_errmsg2}{literal}",
	// playlist
	playlist_delete_confirm: "{/literal}{$lang.playlist_delete_confirm}{literal}",
	playlist_delete_item_confirm: "{/literal}{$lang.playlist_delete_item_confirm}{literal}",
	show_more: "{/literal}{$lang.show_more}{literal}",
	show_less: "{/literal}{$lang.show_less}{literal}",
	delete_video_confirmation: "{/literal}{$lang.delete_video_confirmation}{literal}",
	browse_all: "{/literal}{$lang.browse_all}{literal}",
	upload_error_unknown: "{/literal}{$lang.error_unknown|default:'Unknown error occured'}{literal}"
 }
</script>
{/literal}

<script type="text/javascript" src="{$smarty.const._URL}/js/swfobject.js"></script>
{if $facebook_image_src != ''}
	<link rel="image_src" href="{$facebook_image_src}" />
	<meta property="og:url"  content="{if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}" />
	{if $tpl_name == 'article-read'}
	<meta property="og:type" content="article" />
	{/if}
	<meta property="og:title" content="{$meta_title}" />
	<meta property="og:description" content="{$meta_description}" />
	<meta property="og:image" content="{$facebook_image_src}" />
	<meta property="og:image:width" content="480" />
	<meta property="og:image:height" content="360" />
	{if $video_data.source_id == $_sources.localhost.source_id}
		<link rel="video_src" href="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}"/>
		<meta property="og:video" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}" />
		<meta property="og:video:url" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}" />
		<meta property="og:video:secure_url" content="{$smarty.const._URL}/uploads/videos/{$video_data.url_flv_raw}" />
		<meta property="og:video:type" content="video/mp4" />
	{/if}
{/if}
<style type="text/css">{$theme_customizations}</style>
{if isset($mm_header_inject)}{$mm_header_inject}{/if}
</head>
{if $tpl_name == "video-category"}
<body class="video-category catid-{$cat_id} page-{$gv_pagenumber}">
{elseif $tpl_name == "video-watch"}
<body class="video-watch videoid-{$video_data.id} author-{$video_data.author_user_id} source-{$video_data.source_id}{if $video_data.featured == 1} featured{/if}{if $video_data.restricted == 1} restricted{/if}">
{elseif $tpl_name == "article-category"}
<body class="article-category catid-{$cat_id}">
{elseif $tpl_name == "article-read"}
<body class="article-read articleid-{$article.id} author-{$article.author} {if $article.featured == 1} featured{/if}{if $article.restricted == 1} restricted{/if}">
{elseif $tpl_name == "page"}
<body class="page pageid-{$page.id} author-{$page.author}">
{else}
<body>
{/if}
{if $maintenance_mode}
	<div class="alert alert-danger" align="center"><strong>Currently running in maintenance mode.</strong></div>
{/if}
{if ($tpl_name == 'article-read' || $tpl_name == 'video-watch') && $comment_system_facebook}
<!-- Facebook Javascript SDK -->
<div id="fb-root"></div>
{literal}
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
{/literal}
{/if}
{if isset($mm_body_top_inject)}{$mm_body_top_inject}{/if}

{if $smarty.const.USER_DEVICE == 'mobile'}
{literal}
<script type="text/javascript"> 
window.mmswitch_options = {
		"message":"{/literal}{$lang.mmswitch_message|default:'Would you like to see the mobile optimized version of this page?'}{literal}",
		"confirm":"{/literal}{$lang._yes|default:'Yes'}{literal}",
		"dismiss":"{/literal}{$lang._no|default:'No'}{literal}",
		"link":"{/literal}{$_footer_switch_ui_link}{literal}",
		"theme":"mobileswitch"};
</script>
{/literal}
<script type="text/javascript" src="{$smarty.const._URL_MOBI}/js/mobileswitch.js"></script>
{/if}

<header class="wide-header" id="overview">
<div class="row-fluid fixed960">
	<div class="span3">
	  {if $_custom_logo_url != ''}
		<div class="pm-logo-image">
			<a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home"><img src="{$_custom_logo_url}" alt="{$smarty.const._SITENAME|escape}" title="{$smarty.const._SITENAME|escape}" border="0" /></a>
		</div>
	  {else}
		<h1 class="site-title"><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" rel="home">{$smarty.const._SITENAME}</a></h1>
	  {/if}
   </div>
   
   <div class="span6 wide-header-pad">
	{if $p == "article"}
	<form action="{$smarty.const._URL}/article.php" method="get" id="search" name="search" onSubmit="return validateSearch('true');">
	<div class="controls">
	  <div class="input-append">
		<input class="span10 pm-search-field" id="appendedInputButton" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..." x-webkit-speech speech onwebkitspeechchange="this.form.submit();"><button class="btn" type="submit"><i class="icon-search"></i></button>
	  </div>
	</div>
	</form>
	{else}
	<form action="{$smarty.const._URL}/search.php" method="get" id="search" name="search" onSubmit="return validateSearch('true');">
	<div class="controls">
	  <div class="input-append">
		<input class="span10 pm-search-field" id="appendedInputButton" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..." x-webkit-speech="x-webkit-speech" onwebkitspeechchange="this.form.submit();" {if $smarty.const._SEARCHSUGGEST == 1}onblur="fill();" autocomplete="off"{/if}>
		<input id="pm-video-id" size="16" name="video-id" type="hidden">
		<button class="btn" type="submit"><i class="icon-search"></i></button>
	  </div>
	  <div class="suggestionsBox" id="suggestions" style="display: none;">
		  <ul class="suggestionList input-xlarge" id="autoSuggestionsList"></ul>
	  </div>
	</div>
	</form>
	{/if}
   </div>

	<div class="span3 hidden-phone">
	<div id="user-pane">
		<div class="user-data">
		{if $logged_in != '1'}
			<span class="avatar-img avatar-generic">
			<a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-login-form" rel="tooltip" title="{$lang.login}"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/pm-avatar.png" width="40" height="40" alt=""></a>
			</span>
			<span class="greet-links">
				<div class="ellipsis"><strong>{$lang._welcome}</strong></div>
				<span class=""><a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-login-form">{$lang.login}</a>
				{if $allow_registration == '1'} / 
					{if $allow_facebook_login || $allow_twitter_login}
					<a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-register-form">{$lang.register}</a>
					{else}
					<a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}">{$lang.register}</a>
					{/if}
				{/if}
				</span>
			</span>
			</div>
		{else}
			<span class="avatar-img">
			<a href="#" id="notification_counter" title="{$lang.notifications}">
				{if $smarty.const._MOD_SOCIAL && $logged_in && $notification_count > 0}
					<span class="notifications">{$notification_count}</span>
				{else}
				{/if}
				<img src="{$s_avatar_url}" width="40" height="40" alt="">
			</a>
			</span>
			
			<span class="greet-links">
			<div class="ellipsis"><strong><a href="{$current_user_data.profile_url}">{$s_name}</a></strong></div>
			{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}<a href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a>{/if}{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1' && $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'} / {/if}{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'} <a href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload}</a>{/if}
			</span>
			</div>
			
			<div class="user-menu dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#"><i class="icon-chevron-down"></i></a>
				<ul class="dropdown-menu pull-right pm-ul-user-menu" role="menu" aria-labelledby="dLabel">
				{if $is_admin == 'yes' || $is_moderator == 'yes' || $is_editor == 'yes'}
				<li><a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/index.php">{$lang.admin_area}</a></li>
				{/if}
				<li><a tabindex="-1" href="{$current_user_data.profile_url}">{if $smarty.const._MOD_SOCIAL} {$lang.my_channel} {else} {$lang.my_profile} {/if}</a></li>
				<li><a tabindex="-1" href="{$smarty.const._URL}/edit_profile.{$smarty.const._FEXT}">{$lang.edit_profile}</a></li>
				{if $smarty.const._ALLOW_USER_SUGGESTVIDEO == '1'}
				<li><a tabindex="-1" href="{$smarty.const._URL}/suggest.{$smarty.const._FEXT}">{$lang.suggest}</a></li>
				{/if}
				{if $smarty.const._ALLOW_USER_UPLOADVIDEO == '1'}
				<li><a tabindex="-1" href="{$smarty.const._URL}/upload.{$smarty.const._FEXT}">{$lang.upload_video}</a></li>
				{/if}
				<li><a tabindex="-1" href="{$smarty.const._URL}/playlists.{$smarty.const._FEXT}">{$lang.my_playlists}</a></li>
				<li><a tabindex="-1" href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}">{$lang.members_list}</a></li>
				{if isset($mm_menu_logged_inject)}{$mm_menu_logged_inject}{/if}
				<li class="divider"></li>
				<li><a tabindex="-1" href="{$smarty.const._URL}/login.{$smarty.const._FEXT}?do=logout">{$lang.logout}</a></li>
				</ul>
			</div>
		{/if}
	
		{if ! $logged_in}
		<div class="modal modal-narrow hide" id="header-login-form" role="dialog" aria-labelledby="header-login-form-label"> <!-- login modal -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="header-login-form-label">{$lang.login|default:"Login"}</h3>
			</div>
			<div class="modal-body">
				<p></p>
				{if $allow_facebook_login || $allow_twitter_login}
				<div class="pm-social-accounts">
					<p>{$lang.login_with_social}</p>
					{if $allow_facebook_login}
					<a href="{$smarty.const._URL}/login.php?do=facebook" class="btn btn-facebook" rel="nofollow"><i class="fa fa-facebook-square"></i>Facebook</a>
					{/if}
					{if $allow_twitter_login}
					<a href="{$smarty.const._URL}/login.php?do=twitter" class="btn btn-twitter" rel="nofollow"><i class="fa fa-twitter"></i> Twitter</a>
					{/if}
				</div>
				<div class="clearfix"></div>
				<hr />
				{/if}
				{include file="user-auth-login-form.tpl"}
			</div>
		</div>

		<div class="modal modal-narrow hide" id="header-register-form" role="dialog" aria-labelledby="header-register-form-label"> <!-- register modal -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="header-register-form-label">{$lang.create_account}</h3>
			</div>
			<div class="modal-body">
				<p></p>
				<div class="pm-social-accounts">
					<p>{$lang.register_with_social}</p>
					{if $allow_facebook_login}
					<a href="{$smarty.const._URL}/login.php?do=facebook" class="btn btn-facebook" rel="nofollow"><i class="fa fa-facebook-square"></i>Facebook</a>
					{/if}
					{if $allow_twitter_login}
					<a href="{$smarty.const._URL}/login.php?do=twitter" class="btn btn-twitter" rel="nofollow"><i class="fa fa-twitter"></i> Twitter</a>
					{/if}
				</div>
				<div class="clearfix"></div>
				<hr />
				<a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}" class="btn btn-register-email">{$lang.register_with_email}</a>
			</div>
		</div>
		{/if}
	{if $smarty.const._MOD_SOCIAL && $logged_in}<!--//$notification_count > 0}-->
		<div class="hide" id="notification_temporary_display_container"></div>
	{/if}
		</div><!--.user-data-->
	</div><!--user-pane-->
</div>
</header>
<nav class="wide-nav">
	<div class="row-fluid fixed960">
		<span class="span12">
		<div class="navbar">
			  <div class="navbar-inner">
				<div class="container">
				  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </a>
				  <div class="nav-collapse">
					<ul class="nav">
					  <li><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}" class="wide-nav-link">{$lang.homepage}</a></li>
					  <li class="dropdown">
						{if $smarty.const._SEOMOD}
							<a href="{if $smarty.const.USER_DEVICE != 'mobile'}{$smarty.const._URL}/browse.{$smarty.const._FEXT}{else}#{/if}" class="dropdown-toggle wide-nav-link disabled" data-target="#" role="button" data-toggle="dropdown">{$lang.category} <b class="caret"></b></a>
						{else}
							<a href="{if $smarty.const.USER_DEVICE != 'mobile'}{$smarty.const._URL}/category.php{else}#{/if}" class="dropdown-toggle wide-nav-link disabled" data-target="#" role="button" data-toggle="dropdown">{$lang.category} <b class="caret"></b></a>
						{/if}
						<ul class="dropdown-menu">
						{dropdown_menu_video_categories max_levels=3}
						</ul>
					  </li>
					  
					  {if $smarty.const._MOD_ARTICLE == 1}
					  <li class="dropdown">
						<a href="#" class="dropdown-toggle wide-nav-link" data-toggle="dropdown">{$lang.articles} <b class="caret"></b></a>
						<ul class="dropdown-menu">
						{dropdown_menu_article_categories max_levels=3}
						</ul>
					  </li>
					  {/if}
					  <li><a href="{$smarty.const._URL}/topvideos.{$smarty.const._FEXT}" class="wide-nav-link">{$lang.top_videos}</a></li>
					  <li><a href="{$smarty.const._URL}/newvideos.{$smarty.const._FEXT}" class="wide-nav-link">{$lang.new_videos}</a></li>
					  <li><a href="{$smarty.const._URL}/randomizer.php" rel="nofollow" class="wide-nav-link">{$lang.random_video}</a></li>
					  {if isset($mm_menu_always_inject1)}{$mm_menu_always_inject1}{/if}		
					  <li><a href="{$smarty.const._URL}/contact_us.{$smarty.const._FEXT}" class="wide-nav-link">{$lang.contact_us}</a></li>
					  {if isset($mm_menu_always_inject2)}{$mm_menu_always_inject2}{/if}		
					  {if $logged_in != 1 && isset($mm_menu_notlogged_inject)}{$mm_menu_notlogged_inject}{/if}
					</ul>
					<ul class="nav pull-right pm-ul-pages">
					{if is_array($header_page_links) && !empty($header_page_links) }
					  <li class="dropdown">
						<a href="#" class="dropdown-toggle wide-nav-link" data-toggle="dropdown">{$lang.pages} <b class="caret"></b></a>
						<ul class="dropdown-menu">
							  {foreach from=$header_page_links key=k item=page_data}
								<li><a href="{$page_data.page_url}">{$page_data.title}</a></li>
							  {/foreach}
						</ul>
					  </li>
					{/if}
					
					{if $logged_in != '1'}
						{if $allow_registration == '1'}
							{if $allow_facebook_login || $allow_twitter_login}
							<li><a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-register-form" class="btn-register border-radius2">{$lang.register}</a></li>
							{else}
							<li><a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}" class="btn-register border-radius2">{$lang.register}</a></li>
							{/if}
						{/if}
					{/if}
					</ul>

				  </div><!-- /.nav-collapse -->
				</div>
			  </div><!-- /navbar-inner -->
			</div><!-- /navbar -->
	   </span>
	</div>
</nav>
<a id="top"></a>
{if $ad_1 != ''}
<div class="pm-ad-zone" align="center">{$ad_1}</div>
{/if}