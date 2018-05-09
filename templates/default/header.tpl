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
	<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/main.min.css">

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

<header class="header clearfix">
	<div class="container">
		<div class="navbar-header">
			<div class="logo">
				<a href="index.html">
					<img src="{$smarty.const._URL}/templates/{$template_dir}/img/logo-temp.png" alt="">
				</a>
			</div>
			<div id="open-menu">
				<div id="hamburger"><span></span><span></span><span></span>
				</div>
				<div id="cross"><span></span><span></span>
				</div>
			</div>
			<div class="wrap-menu-register">
				<div data-toggle="dropdown" class="btn-menu-register wrap-icon">
					<svg class="svg-icon" width="15px" height="15px">
						<use xlink:href="#user"></use>
					</svg>

				</div>
                {if $logged_in != '1'}
				<div class="menu-register">
					<a href="#" data-href="{$smarty.const._URL}/templates/{$template_dir}/helpers/signup.html" data-max-width="850" class="link wrap-icon js__btn-popup">
						<svg class="svg-icon" width="15px" height="15px">
							<use xlink:href="#user"></use>
						</svg>
						<span>Register</span>
					</a><span class="text">or </span>
					<a href="#" data-href="{$smarty.const._URL}/templates/{$template_dir}/helpers/login.html" data-max-width="460" class="link wrap-icon js__btn-popup">
						<svg class="svg-icon" width="15px" height="15px">
							<use xlink:href="#exit"></use>
						</svg>
						<span>Login  </span>
					</a>
				</div>
                {else}
					<span class="avatar-img">
						<a href="#" id="notification_counter" title="{$lang.notifications}">
							{if $smarty.const._MOD_SOCIAL && $logged_in && $notification_count > 0}
								<span class="notifications">{$notification_count}</span>
							{else}
							{/if}
							<img src="{$s_avatar_url}" width="25" height="25" alt="" style="display: inline;">
						</a>
					</span>
					<div class="user-menu dropdown" style="display:inline">
						<a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#">{$s_name}<i class="icon-chevron-down"></i></a>
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
			</div>
			<div class="menu-languages">
				<div data-toggle="dropdown" class="menu-languages-btn wrap-icon">
					<img src="{$smarty.const._URL}/templates/{$template_dir}/img/usa.png" alt=""><span>Eng</span>
					<svg class="svg-icon" width="9px" height="5px">
						<use xlink:href="#arr-down-wider"></use>
					</svg>

				</div>
				<ul class="menu-languages-drop">
					<li>
						<a href="#" class="wrap-icon">
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/deutch.png" alt=""><span>Deutch</span>
						</a>
					</li>
					<li class="active">
						<a href="#" class="wrap-icon">
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/russian.png" alt=""><span>Русский</span>
						</a>
					</li>
					<li>
						<a href="#" class="wrap-icon">
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/english.png" alt=""><span>English</span>
						</a>
					</li>
					<li>
						<a href="#" class="wrap-icon">
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/hindi.png" alt=""><span>Hindi</span>
						</a>
					</li>
					<li>
						<a href="#" class="wrap-icon">
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/frunc.png" alt=""><span>Français</span>
						</a>
					</li>
					<li>
						<a href="#" class="wrap-icon">
							<img src="{$smarty.const._URL}/templates/{$template_dir}/img/italia.png" alt=""><span>Italiano</span>
						</a>
					</li>
				</ul>
			</div>
            {if $logged_in == '1'}
				<a href="#" data-href="{$smarty.const._URL}/upload.php" data-max-width="850" class="btn-upload js__btn-popup" style="margin-top:0">
					<span class="wrap-svg" style="height:29px;">
						<svg class="svg-icon" width="10px" height="12px" style="height:25px;">
							<use xlink:href="#upload-arr"></use>
						</svg>
					</span>
					<span class="text">Upload</span>
				</a>
			{/if}
		</div>
		<div class="wrap-search claerfix">
			<form action="{$smarty.const._URL}/search.php" id="search" method="get"  name="search" onSubmit="return validateSearch('true');" class="form1">
				<div class="wrap-search-input">
					<div class="search">
						<div class="search-inner">
							<input class="span10 pm-search-field" id="appendedInputButton" size="16" name="keywords" type="text" placeholder="{$lang.submit_search}..."
								   x-webkit-speech speech onwebkitspeechchange="this.form.submit();">
							<button class="btn" type="submit"><i class="icon-search"></i></button>
						</div>
					</div>
					<button type="submit" class="button-search wrap-icon btn">
						<svg class="svg-icon" width="16px" height="16px">
							<use xlink:href="#searchIcon"></use>
						</svg>
					</button>
				</div>
				<div class="wrap-search-select">
					<div class="search-select">
						<div data-toggle="dropdown" class="search-select-btn wrap-icon" >
							<svg class="svg-icon" width="55.2px" height="33px" >
								<use xlink:href="#camera"></use>
							</svg>

							<svg class="svg-icon arr-up" width="5px" height="3px">
								<use xlink:href="#arr-up"></use>
							</svg>

							<svg class="svg-icon arr-down" width="5px" height="3px">
								<use xlink:href="#arr-down"></use>
							</svg>

						</div>
						<ul class="search-select-drop">
							<li>
								<a href="videos.html" class="wrap-icon">
									<svg class="svg-icon" width="485.2px" height="424.6px">
										<use xlink:href="#images"></use>
									</svg>
									<span>Photos</span>
								</a>
							</li>
							<li class="active">
								<a href="videos.html" class="wrap-icon">
									<svg class="svg-icon" width="55.2px" height="33px">
										<use xlink:href="#camera"></use>
									</svg>
									<span>Videos</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="wrap-navigation">
		<div id="nav-toggle" class="cross">
			<svg viewbox="0 0 800 600">
				<path id="nav-toggle-top" d="M300,220 C300,220 520,220 540,220 C740,220 640,540 520,420 C440,340 300,200 300,200"></path>
				<path id="nav-toggle-middle" d="M300,320 L540,320"></path>
				<path id="nav-toggle-bottom" d="M300,210 C300,210 520,210 540,210 C740,210 640,530 520,410 C440,330 300,190 300,190" transform="translate(480, 320) scale(1, -1) translate(-480, -318) "></path>
			</svg>
		</div>
		<div class="navigation clearfix">
			<ul>
				<li><a href="videos.html" data-toggle="dropdown" class="nav-item"><span>Videos</span></a>
					<div class="nav-drop">
						<ul style="margin:0;">
							<li>
								<a href="{$smarty.const._URL}"  class="wrap-icon">
									<svg class="svg-icon" width="40px" height="40.031px">
										<use xlink:href="#new"></use>
									</svg>
									<span>Newest</span>
								</a>
							</li>
							<li>
								<a href="#" class="wrap-icon">
									<svg class="svg-icon" width="40px" height="40px">
										<use xlink:href="#eye"></use>
									</svg>
									<span>Popular</span>
								</a>
							</li>
							<li>
								<a href="#" class="wrap-icon">
									<svg class="svg-icon" width="39.97px" height="37px">
										<use xlink:href="#top-rated"></use>
									</svg>
									<span>Top rated</span>
								</a>
							</li>
							<li class="active">
								<a href="#" class="wrap-icon">
									<svg class="svg-icon" width="35.97px" height="40.031px">
										<use xlink:href="#like"></use>
									</svg>
									<span>Most Liked</span>
								</a>
							</li>
							<li>
								<a href="#" class="wrap-icon">
									<svg class="svg-icon" width="40px" height="37px">
										<use xlink:href="#list"></use>
									</svg>
									<span>Playlists</span>
								</a>
							</li>
							<li>
								<a href="#" class="wrap-icon">
									<svg class="svg-icon" width="37px" height="39.969px">
										<use xlink:href="#tv"></use>
									</svg>
									<span>Channels</span>
								</a>
							</li>
						</ul>
					</div>
				</li>
				<li><a href="category.php" class="nav-item"><span>Categories</span></a>
				</li>
				<li><a href="albums.html" class="nav-item"><span>Providers</span></a>
				</li>
				<li><a href="models.html" class="nav-item"><span>Casinos</span></a>
				</li>
				<li><a href="channels.html" class="nav-item"><span>Community</span></a>
				</li>
				<li class="show-mob"><a href="#" class="nav-item"><span>Upload</span></a>
				</li>
				<li class="show-mob"><a href="#" data-href="{$smarty.const._URL}/templates/{$template_dir}/helpers/signup.html" data-max-width="850" class="nav-item js__btn-popup"><span>Register</span></a>
				</li>
				<li class="show-mob"><a href="#" data-href="{$smarty.const._URL}/templates/{$template_dir}/helpers/login.html" data-max-width="460" class="nav-item js__btn-popup"><span>Login</span></a>
				</li>
			</ul>
		</div>
	</div>

</header>

<a id="top"></a>
{if $ad_1 != ''}
<div class="pm-ad-zone" align="center">{$ad_1}</div>
{/if}

<svg xmlns="http://www.w3.org/2000/svg" style="border: 0 !important; clip: rect(0 0 0 0) !important; height: 1px !important; margin: -1px !important; overflow: hidden !important; padding: 0 !important; position: absolute !important; width: 1px !important;" class="root-svg-symbols-element">
	<symbol id="add-folder" viewBox="0 0 23.47 20.719">
		<path d="M21.284 20.732H2.211c-1.173 0-2.2-1.038-2.2-2.223V2.213c0-1.185 1.027-2.222 2.2-2.222H8.08c1.174 0 2.2 1.037 2.2 2.222v1.481c0 .445.294.741.734.741h10.27c1.173 0 2.2 1.037 2.2 2.223v11.851c0 1.185-1.027 2.223-2.2 2.223zm.734-14.074c0-.445-.294-.742-.734-.742h-10.27c-1.174 0-2.2-1.036-2.2-2.222V2.213c0-.445-.294-.741-.734-.741H2.211c-.439 0-.733.296-.733.741v16.296c0 .445.294.741.733.741h19.073c.44 0 .733-.296.733-.741h.001V6.658zm-7.336 6.962h-2.201v2.223c0 .444-.294.741-.734.741s-.733-.297-.733-.741V13.62H8.813c-.44 0-.733-.296-.733-.74 0-.445.293-.741.733-.741h2.201V9.917c0-.445.293-.741.733-.741.441 0 .734.296.734.741v2.222h2.201c.44 0 .733.296.733.741 0 .444-.293.74-.733.74z" class="cls-1"></path>
	</symbol>
	<symbol id="arr-down-wider" viewBox="0 0 9 5">
		<path d="M4.5 5a.638.638 0 0 1-.446-.18L.184 1.049a.606.606 0 0 1 0-.869.643.643 0 0 1 .892 0L4.5 3.517 7.924.18a.641.641 0 0 1 .891 0 .602.602 0 0 1 0 .869L4.945 4.82A.634.634 0 0 1 4.5 5z" class="cls-1"></path>
	</symbol>
	<symbol id="arr-down" viewBox="0 0 5 3">
		<path d="M2.5 3a.338.338 0 0 1-.247-.108L.103.629a.382.382 0 0 1 0-.521.338.338 0 0 1 .495 0L2.5 2.11 4.402.108a.338.338 0 0 1 .495 0 .382.382 0 0 1 0 .521L2.748 2.892A.342.342 0 0 1 2.5 3z" class="cls-1"></path>
	</symbol>
	<symbol id="arr-left" viewBox="0 0 15.03 27">
		<path d="M.54 14.837l11.315 11.609a1.808 1.808 0 0 0 2.605 0c.72-.738.72-1.936 0-2.674L4.449 13.5 14.46 3.227c.72-.738.72-1.935 0-2.673a1.809 1.809 0 0 0-2.606 0L.539 12.163A1.913 1.913 0 0 0 0 13.5c0 .484.181.967.54 1.337z" class="cls-1"></path>
	</symbol>
	<symbol id="arr-right-circle-empty" viewBox="0 0 17.97 17.969">
		<path d="M8.149 5.28l-.002.002a.833.833 0 0 0-.567-.224.837.837 0 1 0-.567 1.459l-.001.003L9.7 8.984l-2.688 2.464.001.002a.836.836 0 0 0-.274.618c0 .464.377.841.841.841.22 0 .417-.086.567-.223l.002.002 3.365-3.084a.844.844 0 0 0 0-1.241L8.149 5.28zM8.982.011A8.972 8.972 0 0 0 .01 8.984 8.973 8.973 0 1 0 8.982.011zm0 16.263c-4.019 0-7.29-3.271-7.29-7.29 0-4.02 3.271-7.291 7.29-7.291 4.02 0 7.291 3.271 7.291 7.291 0 4.019-3.271 7.29-7.291 7.29z" class="cls-1"></path>
	</symbol>
	<symbol id="arr-right" viewBox="0 0 15 27">
		<path d="M14.46 14.837L3.145 26.446a1.809 1.809 0 0 1-2.606 0 1.927 1.927 0 0 1 0-2.674L10.551 13.5.539 3.227a1.926 1.926 0 0 1 0-2.673 1.81 1.81 0 0 1 2.607 0l11.315 11.609c.359.369.539.853.539 1.337s-.181.967-.54 1.337z" class="cls-1"></path>
	</symbol>
	<symbol id="arr-up" viewBox="0 0 5 3">
		<path d="M2.5 0a.338.338 0 0 0-.247.108L.103 2.371a.382.382 0 0 0 0 .521.338.338 0 0 0 .495 0L2.5.89l1.902 2.002a.338.338 0 0 0 .495 0 .382.382 0 0 0 0-.521L2.748.108A.342.342 0 0 0 2.5 0z" class="cls-1"></path>
	</symbol>
	<symbol id="bell" viewBox="0 0 14 15">
		<path d="M13.035 12.356a33.231 33.231 0 0 1-6.028.546h-.011a33.237 33.237 0 0 1-5.931-.53l-.181-.034a1.172 1.172 0 0 1-.89-1.124c0-.3.118-.573.312-.78l.005-.006.086-.081.001-.001.091-.082.043-.037c.441-.371.906-.707.996-1.705.413-4.601.835-6.441 3.95-7.465.483-.158.649-.381.761-.58L6.29.384l.001-.001a.792.792 0 0 1 .411-.326.889.889 0 0 1 .294-.046.875.875 0 0 1 .293.046.795.795 0 0 1 .413.327c.016.03.032.062.05.093.113.199.279.422.761.58 3.115 1.024 3.537 2.864 3.95 7.465.09.998.555 1.334.996 1.705l.042.037c.031.027.06.052.088.079l.005.003c.034.031.066.063.098.094.189.206.305.477.305.774 0 .564-.413 1.033-.962 1.142zm-7.751 1.208l.034.003c.002-.002.002-.002.002 0 .555.027 1.114.041 1.676.041h.011c.554 0 1.105-.014 1.652-.04l.06-.003c.141.008.252.12.252.258 0 .041-.01.079-.027.113l-.027.043a2.283 2.283 0 0 1-1.921 1.02 2.272 2.272 0 0 1-1.917-1.014l-.001-.003-.031-.046a.25.25 0 0 1-.026-.113c0-.141.117-.256.263-.259z" class="cls-1"></path>
	</symbol>
	<symbol id="block" viewBox="0 0 20 20">
		<path d="M10 0C4.5 0 0 4.5 0 10s4.5 10 10 10 10-4.5 10-10S15.5 0 10 0zM2 10c0-4.4 3.6-8 8-8 1.8 0 3.5.6 4.9 1.7L3.7 14.9C2.6 13.5 2 11.8 2 10zm8 8c-1.8 0-3.5-.6-4.9-1.7L16.3 5.1C17.4 6.5 18 8.2 18 10c0 4.4-3.6 8-8 8z" class="cls-1"></path>
	</symbol>
	<symbol id="cake" viewBox="0 0 11 13">
		<path d="M10.385 13H.615A.624.624 0 0 1 0 12.369v-1.634c0-.348.276-.632.615-.632h9.77c.339 0 .615.284.615.632v1.634a.624.624 0 0 1-.615.631zm-.832-3.788H1.411a.624.624 0 0 1-.615-.631V7.356c0-.348.276-.632.615-.632h8.142c.339 0 .614.284.614.632v1.225a.623.623 0 0 1-.614.631zM8.358 5.908H2.641a.625.625 0 0 1-.615-.632v-.817c0-.348.276-.631.615-.631h2.316v-1.3a.56.56 0 0 1 .224-.45c-.296-.158-.501-.541-.501-1C4.68.49 5.267 0 5.489 0c.198 0 .808.485.808 1.078 0 .452-.198.83-.488.993a.561.561 0 0 1 .234.457v1.3h2.315c.339 0 .615.283.615.631v.817a.625.625 0 0 1-.615.632z" class="cls-1"></path>
	</symbol>
	<symbol id="camera" viewBox="0 0 55.2 33">
		<path class="st0" d="M55.2 32.4l-.5-30.8L43 13.2V5c0-2.8-2.2-5-5-5H5C2.2 0 0 2.2 0 5v23c0 2.8 2.2 5 5 5h33c2.8 0 5-2.2 5-5v-7.8l12.2 12.2z"></path>
	</symbol>
	<symbol id="check" viewBox="0 0 11 9">
		<path d="M10.828.967A7.872 7.872 0 0 0 9.403.055c-.227-.117-.53-.034-.694.188L4.66 5.572 2.288 2.487c-.164-.222-.467-.304-.694-.199a8.906 8.906 0 0 0-1.438.923c-.202.164-.214.456-.05.678 0 0 3.128 4.079 3.507 4.571a1.325 1.325 0 0 0 2.094 0c.391-.503 5.172-6.827 5.172-6.827.164-.222.151-.514-.051-.666z" class="cls-1"></path>
	</symbol>
	<symbol id="close" viewBox="0 0 10 10">
		<path d="M10 1L9 0 5 4 1 0 0 1l4 4-4 4 1 1 4-4 4 4 1-1-4-4 4-4z" class="cls-1"></path>
	</symbol>
	<symbol id="comments" viewBox="0 0 24.75 25">
		<path d="M12.365 0C5.529 0-.013 4.897-.013 10.937c0 3.452 1.815 6.527 4.642 8.53V25l6.338-3.2c.459.047.925.075 1.398.075 6.836 0 12.379-4.897 12.379-10.938C24.744 4.897 19.201 0 12.365 0zm0 20.312c-.708 0-1.4-.062-2.072-.175l.525.177-4.642 2.342V18.75l.574.194c-3.122-1.645-5.216-4.61-5.216-8.007 0-5.178 4.849-9.375 10.831-9.375 5.982 0 10.832 4.197 10.832 9.375 0 5.179-4.85 9.375-10.832 9.375z" class="cls-1"></path>
	</symbol>
	<symbol id="download" viewBox="0 0 20 20">
		<path d="M20 9.982c0 1.727-1.362 3.128-3.043 3.128h-3.752V8.838c0-.953-.72-1.741-1.647-1.741H8.431c-.927 0-1.681.788-1.681 1.741v4.272H4.11C1.84 13.11 0 11.218 0 8.886c0-1.752 1.038-3.254 2.516-3.894C2.57 2.226 4.768 0 7.473 0c1.721 0 3.236.901 4.126 2.269a3.823 3.823 0 0 1 1.64-.368c2.173 0 3.934 1.81 3.934 4.043 0 .314-.036.62-.102.913C18.699 6.919 20 8.294 20 9.982zM6.772 14.35c.187-.083.399-.134.628-.134h.359V8.838c0-.38.302-.704.672-.704h3.116c.37 0 .65.324.65.704v5.378h.403c.229 0 .441.051.628.134.282.124.505.343.634.628.215.472.122 1.019-.247 1.473l-2.41 2.959c-.305.375-.744.59-1.205.59-.461 0-.9-.215-1.205-.59l-2.41-2.958c-.369-.454-.462-1.005-.247-1.478.129-.284.352-.5.634-.624zm2.668 2.519v.8c0 .273.239.494.504.494a.508.508 0 0 0 .504-.494v-.758c.337-.17.51-.496.51-.87a.983.983 0 0 0-.969-.996.983.983 0 0 0-.969.996c0 .345.151.65.42.828z" class="cls-1"></path>
	</symbol>
	<symbol id="edit" viewBox="0 0 29 29.031">
		<path d="M28.203.779a2.727 2.727 0 0 1 0 3.851l-1.281 1.284-3.847-3.852L24.357.779a2.717 2.717 0 0 1 3.846 0zM8.979 16.185l-1.28 5.135 5.125-1.284L25.64 7.198l-3.846-3.852L8.979 16.185zm12.77-2.521V25.4H3.625V7.243h11.716l3.624-3.631H0v25.419h25.375v-19l-3.626 3.633z" class="cls-1"></path>
	</symbol>
	<symbol id="exit" viewBox="0 0 15 15">
		<path d="M12.678 15H6.273a.314.314 0 0 1-.315-.313v-.941c0-.173.141-.314.315-.314h6.405a.75.75 0 0 0 .75-.748V2.316a.75.75 0 0 0-.75-.748H6.273a.315.315 0 0 1-.315-.314v-.94c0-.174.141-.314.315-.314h6.405A2.321 2.321 0 0 1 15 2.316v10.368A2.321 2.321 0 0 1 12.678 15zM6.138 3.657l6.201 3.572a.313.313 0 0 1 0 .543l-6.201 3.571a.31.31 0 0 1-.314 0 .315.315 0 0 1-.157-.272V9.408H.315A.314.314 0 0 1 0 9.095v-3.19c0-.173.141-.314.315-.314h5.352V3.929a.315.315 0 0 1 .471-.272z" class="cls-1"></path>
	</symbol>
	<symbol id="eye" viewBox="0 0 40 40">
		<path d="M20 40C8.972 40 0 31.027 0 20 0 8.972 8.972 0 20 0c11.029 0 20 8.972 20 20 0 11.026-8.973 20-20 20zm0-28.53c-9.345 0-14.384 8.722-14.384 8.722S10.655 28.913 20 28.913c9.347 0 14.383-8.721 14.383-8.721S29.347 11.47 20 11.47zm0 16.156c-4.101 0-7.437-3.335-7.437-7.435 0-4.101 3.336-7.437 7.437-7.437 4.101 0 7.438 3.336 7.437 7.437 0 4.098-3.336 7.435-7.437 7.435zm0-12.453a5.025 5.025 0 0 0-5.021 5.019A5.026 5.026 0 0 0 20 25.211c2.77 0 5.02-2.252 5.02-5.019A5.026 5.026 0 0 0 20 15.173zm.002 8.637a3.621 3.621 0 1 1 0-7.241c.562 0 1.085.139 1.561.368a1.79 1.79 0 0 0 .288 3.558 1.79 1.79 0 0 0 1.663-1.139c.064.269.108.546.108.835a3.62 3.62 0 0 1-3.62 3.619z" class="cls-1"></path>
	</symbol>
	<symbol id="eye2" viewBox="0 0 22 16">
		<path d="M11 0C6 0 1.7 3.307 0 8c1.7 4.693 6 8 11 8s9.3-3.307 11-8c-1.7-4.693-6-8-11-8zm0 13.333c-2.8 0-5-2.347-5-5.333 0-2.987 2.2-5.333 5-5.333S16 5.013 16 8c0 2.986-2.2 5.333-5 5.333zm0-8.534C9.3 4.799 8 6.186 8 8c0 1.813 1.3 3.2 3 3.2s3-1.387 3-3.2c0-1.814-1.3-3.201-3-3.201z" class="cls-1"></path>
	</symbol>
	<symbol id="facebook-circle" viewBox="0 0 26 26">
		<path d="M12.997 0C5.82 0 0 5.823 0 13.004c0 7.182 5.82 13.005 12.997 13.005 7.178 0 12.998-5.823 12.998-13.005C25.995 5.821 20.175 0 12.997 0zm3.36 13.003l-2.13.001-.002 7.803h-2.922v-7.803h-1.95v-2.688l1.95-.002-.004-1.583c0-2.195.595-3.529 3.177-3.529h2.152v2.69h-1.346c-1.006 0-1.055.375-1.055 1.076l-.003 1.346h2.418l-.285 2.689z" class="cls-1"></path>
	</symbol>
	<symbol id="gear" viewBox="0 0 15 14.969">
		<path d="M14.941 6.751a.778.778 0 0 0-.656-.671c-.421-.047-1.28-.157-1.28-.157a.852.852 0 0 1-.656-.828c0-.156.046-.312.14-.437 0-.016.781-1.032.781-1.032a.728.728 0 0 0-.016-.921 6.214 6.214 0 0 0-1.015-1.016.728.728 0 0 0-.921-.015l-1.016.781a.964.964 0 0 1-.468.14.836.836 0 0 1-.812-.687L8.865.658a.78.78 0 0 0-.671-.657 7.34 7.34 0 0 0-1.437 0 .78.78 0 0 0-.672.657l-.156 1.25a.824.824 0 0 1-.812.687.9.9 0 0 1-.5-.156l-.984-.765a.73.73 0 0 0-.922.015 6.253 6.253 0 0 0-1.015 1.016.71.71 0 0 0-.015.921l.749.985c.109.14.172.312.172.484a.84.84 0 0 1-.719.828L.665 6.08a.763.763 0 0 0-.656.671 7.35 7.35 0 0 0 0 1.438.78.78 0 0 0 .656.672l1.218.156a.813.813 0 0 1 .719.813.812.812 0 0 1-.172.5l-.749.983a.73.73 0 0 0 .015.923c.297.375.64.718 1.015 1.015.141.11.297.172.469.172a.697.697 0 0 0 .453-.156l.984-.766a.908.908 0 0 1 .5-.156c.406 0 .749.297.812.687 0 .016.156 1.25.156 1.25a.78.78 0 0 0 .672.657c.234.015.484.031.718.031.235 0 .484-.016.719-.031a.779.779 0 0 0 .671-.657l.157-1.25a.836.836 0 0 1 .812-.687c.172 0 .343.047.468.141l1.016.781a.695.695 0 0 0 .453.156.671.671 0 0 0 .468-.172c.375-.297.719-.64 1.015-1.015a.73.73 0 0 0 .016-.923 40.336 40.336 0 0 1-.781-1.03.744.744 0 0 1-.14-.453c0-.391.281-.735.656-.813 0 0 .859-.109 1.28-.156a.778.778 0 0 0 .656-.672 7.35 7.35 0 0 0 0-1.438zM7.475 9.862c-1.377 0-2.494-1.106-2.494-2.469 0-1.364 1.117-2.47 2.494-2.47 1.377 0 2.494 1.106 2.494 2.47 0 1.363-1.117 2.469-2.494 2.469z" class="cls-1"></path>
	</symbol>
	<symbol id="googleplus-square" viewBox="0 0 128 128">
		<rect fill="#D95032"></rect>
		<path d="M49.211 70.991h16.883c-1.94 7.84-7.912 12.213-17.035 12.213-10.781 0-19.554-8.616-19.554-19.203 0-10.589 8.773-19.205 19.554-19.205 4.716 0 9.269 1.671 12.821 4.707l8.117-9.162c-5.803-4.958-13.239-7.689-20.937-7.689-17.6 0-31.917 14.063-31.917 31.348 0 17.284 14.317 31.347 31.917 31.347 17.995 0 30.083-12.23 30.083-30.428v-6.073H49.211v12.145zm67.932-12.25h-11v-11h-8v11h-11v8h11v11h8v-11h11z" fill="#FFF"></path>
	</symbol>
	<symbol id="group" viewBox="0 0 18 16">
		<path d="M10.862 2.746a3.477 3.477 0 0 1 1.522 2.567c.34.167.716.264 1.116.264 1.458 0 2.64-1.249 2.64-2.788C16.14 1.248 14.958 0 13.5 0c-1.444 0-2.616 1.226-2.638 2.746zM9.133 8.455c1.458 0 2.64-1.248 2.64-2.788 0-1.54-1.182-2.788-2.64-2.788-1.458 0-2.641 1.248-2.641 2.788 0 1.54 1.183 2.788 2.641 2.788zm1.12.191H8.012c-1.863 0-3.38 1.601-3.38 3.57v2.893l.007.046.189.062c1.779.587 3.324.783 4.596.783 2.484 0 3.924-.748 4.013-.796l.176-.094h.019v-2.894c.001-1.969-1.516-3.57-3.379-3.57zm4.367-2.879h-2.223a3.523 3.523 0 0 1-1.004 2.393c1.657.521 2.869 2.144 2.869 4.06v.892c2.195-.085 3.459-.742 3.543-.786l.176-.095H18V9.337c0-1.968-1.516-3.57-3.38-3.57zm-10.119-.19a2.52 2.52 0 0 0 1.403-.43 3.494 3.494 0 0 1 1.228-2.201c.003-.052.008-.104.008-.157 0-1.54-1.182-2.788-2.639-2.788-1.459 0-2.64 1.248-2.64 2.788 0 1.54 1.181 2.788 2.64 2.788zm2.37 2.583a3.53 3.53 0 0 1-1.003-2.38c-.083-.006-.164-.013-.248-.013H3.38C1.516 5.767 0 7.369 0 9.337v2.894l.007.044.189.064c1.426.47 2.7.687 3.806.755v-.874c.001-1.916 1.212-3.539 2.869-4.06z" class="cls-1"></path>
	</symbol>
	<symbol id="hamburger-test" viewBox="0 0 800 600">
		<path d="M300 220h240c200 0 100 320-20 200L300 200m0 120h240"></path>
		<path d="M300 428h240c200 0 100-320-20-200L300 448"></path>
	</symbol>
	<symbol id="hd" viewBox="0 0 24 20">
		<path d="M23.047 0H.952C.426 0 0 .462 0 1.033v17.934C0 19.537.426 20 .952 20h22.096c.526 0 .952-.463.952-1.033V1.033C24 .462 23.573 0 23.047 0zM8.604 14.035l.826-3.136H6.348l-.826 3.136H3.63l2.125-8.07h1.893l-.875 3.32h3.082l.875-3.32h1.893l-2.127 8.07H8.604zM20.202 10c-.629 2.386-2.7 4.035-5.092 4.035h-3.125l1.404-5.332h1.892l-.973 3.695h1.35c1.159 0 2.243-.969 2.617-2.387.376-1.429-.237-2.409-1.438-2.409h-3.158l.431-1.637h3.2c2.339 0 3.523 1.637 2.892 4.035z" class="cls-1"></path>
	</symbol>
	<symbol id="heart-empty" viewBox="0 0 24.96 22.281">
		<path d="M18.083-.011v-.003c-2.319.003-4.368 1.167-5.613 2.946C11.227 1.153 9.179-.011 6.86-.014 3.061-.011-.018 3.097-.018 6.934c.125 4.08 5.26 8.379 11.504 14.858.102.106.206.194.316.266.146.118.329.193.505.218l.022.01h.145l.043-.003c.233-.003.48-.101.647-.242.065-.033.182-.134.294-.249 6.244-6.479 11.377-10.778 11.504-14.858 0-3.837-3.08-6.945-6.879-6.945zM12.495 20.83l-.022.026-.027-.027C6.118 14.439 1.208 9.764 1.334 6.934c.005-3.083 2.471-5.573 5.526-5.579 2.212 0 4.117 1.319 4.997 3.221l.141.308c.009.028.023.051.035.076.086.159.248.27.44.27a.5.5 0 0 0 .419-.241l.059-.128.132-.285c.882-1.902 2.788-3.221 5-3.221 3.056.006 5.519 2.496 5.525 5.579.127 2.83-4.783 7.505-11.113 13.896z" class="cls-1"></path>
	</symbol>
	<symbol id="heart" viewBox="0 0 17 16">
		<path d="M8.5 16l-1.19-1.217c-4.42-4-7.31-6.696-7.31-10C0 2.087 2.04 0 4.675 0 6.12 0 7.565.696 8.5 1.826 9.435.696 10.88 0 12.325 0 14.96 0 17 2.087 17 4.783c0 3.304-2.89 6-7.31 10L8.5 16z" class="cls-1"></path>
	</symbol>
	<symbol id="human-empty" viewBox="0 0 13 18">
		<path d="M12.438 17.998c-.309 0-.812-.253-.812-.564V10.18c0-.289-.34-.547-.627-.547H7.688c-.31 0-.868-.337-.868-.649v-.703c-.206.034-.104.073-.32.073-.216 0-.141-.039-.347-.073v.703c0 .312-.531.649-.841.649H2c-.286 0-.626.258-.626.547v7.254c0 .311-.503.564-.813.564A.563.563 0 0 1 0 17.434V10.18c0-.922.833-1.929 1.749-1.929h3.003v-.313c-1.398-.664-2.767-2.079-2.767-3.734 0-2.289 2.241-4.206 4.515-4.206 2.274 0 4.404 1.917 4.404 4.206 0 1.655-1.257 3.069-2.655 3.734v.313h3.001c.916 0 1.75 1.007 1.75 1.929v7.254a.564.564 0 0 1-.562.564zM9.502 4.204c0-1.666-1.347-2.824-3.002-2.824-1.655 0-3.059 1.158-3.059 2.824S4.845 6.945 6.5 6.945c1.655 0 3.002-1.075 3.002-2.741zm-5.967 8.168c.315 0 .561.202.561.459v4.708c-.001.258-.247.459-.561.459-.315 0-.813-.201-.813-.459v-4.708c0-.257.498-.459.813-.459zm5.91 0c.314 0 .812.202.812.459v4.708c0 .258-.498.459-.812.459-.315 0-.562-.201-.562-.459v-4.708c0-.257.247-.459.562-.459z" class="cls-1"></path>
	</symbol>
	<symbol id="human" viewBox="0 0 6 14.313">
		<path d="M5.685 9.074l-.005.056-.001.006a.555.555 0 0 1-.545.477v.001H4.97l-.024-.001-.023.001a.371.371 0 0 0-.345.365l-.295 3.771-.009.109a.553.553 0 0 1-.542.462H2.277a.555.555 0 0 1-.545-.474l-.007-.085-.295-3.778a.37.37 0 0 0-.345-.37l-.023-.001-.024.001H.871v-.001a.556.556 0 0 1-.542-.457L.32 9.047.002 5.442a.86.86 0 0 1 .85-.869h4.303a.86.86 0 0 1 .851.869l-.321 3.632zM3.003 3.838c-1.04 0-1.883-.861-1.883-1.922 0-1.062.843-1.923 1.883-1.923 1.041 0 1.885.861 1.885 1.923 0 1.061-.844 1.922-1.885 1.922z" class="cls-1"></path>
	</symbol>
	<symbol id="images" viewBox="0 30.3 485.2 424.6">
		<path d="M454.9 121.3v303.3H91V121.3h363.9M485.2 91H60.7v363.9h424.6V91h-.1zM121.3 363.9h303.3l-60.7-182-91 121.3-60.6-60.7-91 121.4zM151.6 182c-16.8 0-30.3 13.6-30.3 30.3 0 16.8 13.6 30.3 30.3 30.3 16.8 0 30.3-13.6 30.3-30.3.1-16.8-13.5-30.3-30.3-30.3zM0 30.3v363.9h30.3V60.6h394.2V30.3H0z"></path>
	</symbol>
	<symbol id="info" viewBox="0 0 24.75 25">
		<path d="M12.379 25C5.57 25 0 19.375 0 12.5S5.57 0 12.379 0c6.808 0 12.378 5.625 12.378 12.5S19.187 25 12.379 25zm0-22.917C6.705 2.083 2.063 6.771 2.063 12.5s4.642 10.417 10.316 10.417c5.673 0 10.315-4.688 10.315-10.417S18.052 2.083 12.379 2.083zm-1.032 9.375h2.063v7.292h-2.063v-7.292zm0-5.208h2.063v3.125h-2.063V6.25z" class="cls-1"></path>
	</symbol>
	<symbol id="key" viewBox="0 0 23.97 24">
		<path d="M22.012 1.936a6.597 6.597 0 0 0-9.341.001c-1.858 1.864-2.355 4.558-1.533 6.889l-9.809 9.831-1.338 4.014 4.006 1.337 1.194-1.337 3.864-1.339v-2.674l1.473-1.337 1.406 1.337 2.704-2.676-1.317-1.338 1.811-1.808c2.329.828 5.02.329 6.88-1.536a6.628 6.628 0 0 0 0-9.364zm-3.164 5.32a2.146 2.146 0 0 1-2.142-2.148c0-1.184.96-2.147 2.142-2.147a2.147 2.147 0 0 1 0 4.295z" class="cls-1"></path>
	</symbol>
	<symbol id="like" viewBox="0 0 35.97 40.031">
		<path d="M32.701 22.116a3.147 3.147 0 0 0 3.279-3.014c.076-1.744-1.143-3.311-2.878-3.386l-11.517-1.43s2.401-3.968 2.401-9.846c0-3.919-2.833-4.447-4.499-4.447-1.31 0-1.663 2.509-1.663 2.509h-.003c-.297 1.609-.683 3.015-2.026 5.603-1.503 2.9-3.551 2.628-5.94 5.211-.422.455-.988 1.206-1.54 2.13a.725.725 0 0 0-.128.214c-.05.114-.109.193-.16.299-.093.167-.181.333-.271.508-1.478 1.461-3.783 1.311-4.765 1.311-1.968 0-2.999 1.131-2.999 2.965v13.552c0 2.057.852 2.752 2.999 2.752h3c1.51 0 2.692.855 4.499 1.482 2.474.848 6.17 1.483 12.536 1.483l4.566.004c1.058 0 1.906-.477 2.519-1.054.233-.219.476-.537.581-1.158.017-.096.035-.5.033-.554.079-1.777-1.008-2.42-1.624-2.623.017-.005.006-.021.04-.019l1.954.087c1.734.077 3.447-1.158 3.447-3.259 0-1.741-1.428-2.969-3.158-3.053l1.037.047a3.156 3.156 0 0 0 3.282-3.017 3.158 3.158 0 0 0-3.002-3.297z" class="cls-1"></path>
	</symbol>
	<symbol id="link" viewBox="0 0 10 10">
		<path d="M6.794 10c-.878 0-1.68-.348-2.29-.965L3.55 8.07a.376.376 0 0 1 0-.541l.534-.541a.366.366 0 0 1 .534 0l.955.966c.305.309.725.502 1.183.502.916 0 1.679-.773 1.679-1.699 0-.464-.191-.888-.496-1.197l-.954-.965a.376.376 0 0 1 0-.541l.534-.54a.365.365 0 0 1 .534 0l.955.965c.61.617.954 1.428.992 2.278C10 8.533 8.55 10 6.794 10zm0-3.707l-.535.541a.366.366 0 0 1-.534 0L3.206 4.286a.376.376 0 0 1 0-.541l.535-.541a.366.366 0 0 1 .534 0l2.519 2.549a.377.377 0 0 1 0 .54zm-.878-3.282a.365.365 0 0 1-.534 0l-.955-.965a1.664 1.664 0 0 0-1.183-.502c-.916 0-1.679.773-1.679 1.699 0 .464.191.888.496 1.197l.916 1.004a.376.376 0 0 1 0 .541l-.534.54a.366.366 0 0 1-.535 0L.954 5.56A3.33 3.33 0 0 1 0 3.243C0 1.467 1.45 0 3.206 0c.878 0 1.679.348 2.29.965l.954.965a.376.376 0 0 1 0 .541l-.534.54z" class="cls-1"></path>
	</symbol>
	<symbol id="list-checked" viewBox="0 0 20 18">
		<path d="M19.95 15.883H7.489a.05.05 0 0 1-.05-.05v-1.168a.05.05 0 0 1 .05-.05H19.95c.029 0 .05.021.05.05v1.168a.05.05 0 0 1-.05.05zM7.489 9.027a.05.05 0 0 1-.05-.05V7.809a.05.05 0 0 1 .05-.049H19.95c.029 0 .05.02.05.049v1.168a.05.05 0 0 1-.05.05H7.489zm0-6.86a.05.05 0 0 1-.05-.05V.949A.05.05 0 0 1 7.489.9H19.95c.029 0 .05.02.05.049v1.168a.05.05 0 0 1-.05.05H7.489zM2.198 4.809L1.08 3.682 0 2.6l1.126-1.122 1.076 1.085L4.779 0l1.118 1.127L3.32 3.69 2.198 4.809zm.75 6.203c-.863 0-1.562-.7-1.562-1.564a1.563 1.563 0 1 1 3.125 0c0 .864-.699 1.564-1.563 1.564zm0 3.723a1.572 1.572 0 1 1 0 3.145 1.572 1.572 0 0 1 0-3.145zM13.377 3.013a.05.05 0 0 1 .05.05v1.168a.05.05 0 0 1-.05.049H7.489c-.025 0-.046-.021-.05-.049V3.063a.05.05 0 0 1 .05-.05h5.888zm0 6.86a.05.05 0 0 1 .05.049v1.169a.05.05 0 0 1-.05.049H7.489c-.025 0-.046-.025-.05-.049V9.922a.05.05 0 0 1 .05-.049h5.888zm0 6.86c.029 0 .05.021.05.049v1.169a.05.05 0 0 1-.05.049H7.489a.05.05 0 0 1-.05-.049v-1.169a.05.05 0 0 1 .05-.049h5.888z" class="cls-1"></path>
	</symbol>
	<symbol id="list" viewBox="0 0 40 37">
		<path d="M39.428 36.438H12.997a.578.578 0 0 1-.573-.581v-6.803c0-.321.257-.582.573-.582h26.431c.315 0 .572.261.572.582v6.803c0 .32-.257.581-.572.581zm0-13.955H12.997a.578.578 0 0 1-.573-.581v-6.804c0-.32.257-.581.573-.581h26.431c.315 0 .572.261.572.581v6.804c0 .32-.257.581-.572.581zm0-13.955H12.997a.578.578 0 0 1-.573-.582V1.143c0-.32.257-.581.573-.581h26.431c.315 0 .572.261.572.581v6.803a.578.578 0 0 1-.572.582zM4.472 37C2.002 37 0 34.965 0 32.455s2.002-4.545 4.472-4.545c2.47 0 4.472 2.035 4.472 4.545S6.942 37 4.472 37zm0-13.955C2.002 23.045 0 21.01 0 18.5s2.002-4.545 4.472-4.545c2.47 0 4.472 2.035 4.472 4.545s-2.002 4.545-4.472 4.545zm0-13.955C2.002 9.09 0 7.055 0 4.545S2.002 0 4.472 0c2.47 0 4.472 2.035 4.472 4.545S6.942 9.09 4.472 9.09z" class="cls-1"></path>
	</symbol>
	<symbol id="lock" viewBox="0 0 16 23">
		<path d="M8 23c-4.418 0-8-3.451-8-7.707 0-4.257 3.582-7.708 8-7.708s8 3.451 8 7.708C16 19.549 12.418 23 8 23zm0-10.574c-.982 0-1.778.767-1.778 1.713a1.71 1.71 0 0 0 1.016 1.548v2.621h1.524v-2.621a1.71 1.71 0 0 0 1.016-1.548c0-.946-.796-1.713-1.778-1.713zm3.682-5.597V5.016c0-1.956-1.651-3.548-3.682-3.548-2.031 0-3.683 1.592-3.683 3.548v1.813l-.025.01a9.626 9.626 0 0 0-1.499.769V5.016C2.793 2.25 5.129 0 8 0s5.206 2.25 5.206 5.016v2.592a9.616 9.616 0 0 0-1.524-.779z" class="cls-1"></path>
	</symbol>
	<symbol id="marker" viewBox="0 0 10 13">
		<path d="M4.999 0C2.243 0 0 2.147 0 4.786a4.5 4.5 0 0 0 .437 1.953c1.25 2.618 3.647 5.383 4.352 6.168a.286.286 0 0 0 .422 0c.704-.785 3.101-3.549 4.351-6.168a4.5 4.5 0 0 0 .437-1.953c0-2.639-2.243-4.786-5-4.786zm0 7.272c-1.431 0-2.597-1.116-2.597-2.486 0-1.371 1.166-2.486 2.597-2.486 1.432 0 2.598 1.115 2.598 2.486 0 1.37-1.165 2.486-2.598 2.486z" class="cls-1"></path>
	</symbol>
	<symbol id="messages-check" viewBox="-252 383.9 90 75">
		<path d="M-207 383.9c-24.9 0-45 15.2-45 34 0 8.9 4.5 17 11.9 23.1-.3 5.9-1.6 13.8-6.1 17.9 8.6 0 17.4-5.4 22.6-9.4 5.1 1.5 10.7 2.4 16.5 2.4 24.9 0 45-15.2 45-34 .1-18.8-20-34-44.9-34zm25.8 23.3s-24.5 32.4-26.5 34.9c-2.7 3.4-7.9 3.6-10.7 0-1.9-2.5-17.9-23.4-17.9-23.4-.8-1.1-.8-2.6.3-3.5 2.3-1.8 4.7-3.3 7.4-4.7 1.2-.5 2.7-.1 3.6 1l12.1 15.8 20.7-27.3c.8-1.1 2.4-1.6 3.6-1 2.6 1.3 5 2.8 7.3 4.7.8.9.9 2.4.1 3.5z"></path>
	</symbol>
	<symbol id="messages" viewBox="0 0 18 15">
		<path d="M9 0C4.029 0-.001 3.046-.001 6.801c0 1.778.905 3.399 2.385 4.611-.057 1.181-.311 2.768-1.212 3.587 1.72 0 3.48-1.088 4.524-1.872 1.023.306 2.138.475 3.304.475 4.971 0 9-3.046 9-6.801C18 3.046 13.971 0 9 0z" class="cls-1"></path>
	</symbol>
	<symbol id="model" viewBox="0 0 11 10.969">
		<path d="M8.708 1.032s-2.737-2.638-5.971.397c0 0-1.741 1.446-1.386 5.746 0 0-.249 2.168-1.351 2.278 0 0 1.173.036 1.6-.688 0 0 .035 1.445-1.422 1.807 0 0 1.457-.108 1.955-.939 0 0 .143.831-.924 1.12 0 0 2.097.072 2.986-.904 0 0-1.173-1.663-.782-2.566 0 0 .995-2.493 2.56-2.819 0 0-.605.94-1.138 1.229 0 0 2.595-.397 3.804-1.3 0 0 .213-.037.213.397 0 0 .177 1.409.64 1.988 0 0 .107 3.396-2.133 4.191 0 0 3.306-.216 3.626-4.156-.002 0 .354-5.637-2.277-5.781z" class="cls-1"></path>
	</symbol>
	<symbol id="more-points" viewBox="0 0 4.59 23.125">
		<path d="M2.298 13.877a2.304 2.304 0 0 1-2.293-2.315 2.304 2.304 0 0 1 2.293-2.315 2.303 2.303 0 0 1 2.292 2.315 2.303 2.303 0 0 1-2.292 2.315zm0-9.26A2.304 2.304 0 0 1 .005 2.303 2.304 2.304 0 0 1 2.298-.012 2.303 2.303 0 0 1 4.59 2.303a2.303 2.303 0 0 1-2.292 2.314zm0 13.889c1.266 0 2.292 1.037 2.292 2.315s-1.026 2.315-2.292 2.315a2.305 2.305 0 0 1-2.293-2.315 2.305 2.305 0 0 1 2.293-2.315z" class="cls-1"></path>
	</symbol>
	<symbol id="new" viewBox="0 0 40 40.031">
		<path d="M39.745 21.806l-2.94-4.73a1.716 1.716 0 0 1-.219-1.261l1.167-5.464a1.696 1.696 0 0 0-1.122-1.97l-5.25-1.707a1.68 1.68 0 0 1-.967-.823L27.844.904a1.659 1.659 0 0 0-2.108-.778l-5.104 2.115a1.652 1.652 0 0 1-1.262 0L14.267.126a1.66 1.66 0 0 0-2.109.779L9.589 5.851a1.676 1.676 0 0 1-.968.822l-5.25 1.708a1.695 1.695 0 0 0-1.121 1.97l1.166 5.464c.093.433.014.886-.219 1.261l-2.939 4.73a1.71 1.71 0 0 0 .39 2.241l4.357 3.425a1.7 1.7 0 0 1 .631 1.109l.747 5.54c.116.863.86 1.496 1.718 1.462l5.51-.216c.436-.018.862.14 1.186.437l4.083 3.757a1.653 1.653 0 0 0 2.243 0l4.083-3.757c.324-.298.75-.455 1.187-.437l5.509.216a1.676 1.676 0 0 0 1.718-1.462l.747-5.54a1.7 1.7 0 0 1 .631-1.109l4.357-3.425a1.71 1.71 0 0 0 .39-2.241zm-24.369 4.949l-4.865-3.446 1.999 4.7-1.332.583-3.067-7.206 1.396-.611 4.957 3.539-2.049-4.812 1.333-.583 3.067 7.206-1.439.63zm2.956-1.294l-3.066-7.206 5.269-2.307.518 1.22-3.834 1.678.679 1.597 3.569-1.562.516 1.215-3.568 1.561.835 1.961 3.97-1.737.516 1.214-5.404 2.366zm12.012-5.257l-3.708-4.769.882 6.005-1.556.681-4.763-6.463 1.468-.643 3.179 4.481-.808-5.518 1.707-.747 3.388 4.488-1.052-5.511 1.444-.632 1.341 7.962-1.522.666z" class="cls-1"></path>
	</symbol>
	<symbol id="play" viewBox="0 0 54.53 62.784">
		<path d="M.008-.008v62.776L54.52 31.372.008-.008z" class="cls-1"></path>
	</symbol>
	<symbol id="playlist" viewBox="0 0 306.4 262.2">
		<path d="M293.9 0H98.3c-6.9 0-12.5 5.6-12.5 12.5v32h25V25h170.6v127.5h-22.7v25h35.2c6.9 0 12.5-5.6 12.5-12.5V12.5c0-6.9-5.6-12.5-12.5-12.5z"></path>
		<path d="M208.1 84.7H12.5C5.6 84.7 0 90.3 0 97.2v152.5c0 6.9 5.6 12.5 12.5 12.5h195.6c6.9 0 12.5-5.6 12.5-12.5V97.2c0-6.9-5.6-12.5-12.5-12.5zm-12.5 152.5H25V109.7h170.6v127.5z"></path>
		<path d="M71.1 219.9c2 1.2 4.3 1.9 6.6 1.9 1.9 0 3.8-.4 5.6-1.3l71.8-35.8c4.2-2.1 6.9-6.4 6.9-11.2s-2.7-9.1-6.9-11.2l-71.8-35.8c-3.9-1.9-8.5-1.7-12.2.6-3.7 2.3-5.9 6.3-5.9 10.6v71.7c0 4.2 2.2 8.2 5.9 10.5z"></path>
	</symbol>
	<symbol id="plus-circle-fill" viewBox="0 0 18 18">
		<path d="M9 0C4.009 0 0 4.009 0 9s4.009 9 9 9 9-4.009 9-9-4.009-9-9-9zm4.909 9.409c0 .245-.164.409-.409.409h-3.477c-.123 0-.205.082-.205.205V13.5c0 .245-.164.409-.409.409h-.818c-.246 0-.409-.164-.409-.409v-3.477c0-.123-.082-.205-.205-.205H4.5c-.246 0-.409-.164-.409-.409v-.818c0-.246.163-.409.409-.409h3.477c.123 0 .205-.082.205-.205V4.5c0-.246.163-.409.409-.409h.818c.245 0 .409.163.409.409v3.477c0 .123.082.205.205.205H13.5c.245 0 .409.163.409.409v.818z" class="cls-1"></path>
	</symbol>
	<symbol id="rating-dislike" viewBox="0 9.1 456.8 438.5">
		<path d="M15.7 204.1C5.2 216.1 0 230.3 0 246.7c0 14.8 5.4 27.7 16.3 38.5 10.8 10.9 23.7 16.3 38.5 16.3h79.1c-.8 2.7-1.5 4.9-2.3 6.9-.8 1.9-1.8 4-3.1 6.3s-2.3 4-2.9 5.1c-3.4 6.5-6 11.8-7.9 16-1.8 4.2-3.6 9.9-5.4 17.1-1.8 7.2-2.7 14.5-2.7 21.7 0 4.6.1 8.3.1 11.1.1 2.9.6 7.1 1.4 12.8.9 5.7 2 10.5 3.4 14.3 1.4 3.8 3.7 8.1 6.9 12.8 3.1 4.8 6.9 8.6 11.4 11.6s10.2 5.4 17.1 7.4c7 2 14.8 3 23.6 3 4.9 0 9.2-1.8 12.8-5.4 3.8-3.8 7-8.6 9.7-14.3 2.7-5.7 4.5-10.7 5.6-14.8 1-4.2 2.2-10 3.6-17.4 1.7-8 3-13.8 3.9-17.3.9-3.5 2.5-8.1 5-13.8 2.5-5.7 5.4-10.3 8.9-13.7 6.3-6.3 15.9-17.7 28.8-34.3 9.3-12.2 18.9-23.7 28.8-34.5 9.9-10.9 17.1-16.5 21.7-16.8 4.8-.4 8.8-2.3 12.3-5.9 3.4-3.5 5.1-7.7 5.1-12.4V64c0-4.9-1.8-9.2-5.4-12.7-3.6-3.5-7.9-5.4-12.8-5.6-6.7-.2-21.7-4.4-45.1-12.6-14.7-4.9-26.1-8.7-34.4-11.3-8.3-2.6-19.8-5.3-34.7-8.3-14.8-2.9-28.6-4.4-41.1-4.4h-36.9c-25.3.4-44 7.8-56.2 22.3-11 13.1-15.7 30.4-14 51.7-7.4 7-12.6 16-15.4 26.8-3.2 11.6-3.2 22.7 0 33.4-8.7 11.6-12.8 24.7-12.3 39.1 0 6.1 1.4 13.4 4.3 21.7zm340.6 60.8h82.2c5 0 9.2-1.8 12.9-5.4 3.6-3.6 5.4-7.9 5.4-12.8V63.9c0-4.9-1.8-9.2-5.4-12.8-3.6-3.6-7.9-5.4-12.9-5.4h-82.2c-4.9 0-9.2 1.8-12.8 5.4-3.6 3.6-5.4 7.9-5.4 12.8v182.7c0 4.9 1.8 9.2 5.4 12.8 3.6 3.7 7.9 5.5 12.8 5.5zm32.8-177.4c3.6-3.5 7.9-5.3 12.8-5.3 5.1 0 9.5 1.8 13 5.3s5.3 7.8 5.3 13c0 4.9-1.8 9.2-5.3 12.8-3.5 3.6-7.9 5.4-13 5.4-4.9 0-9.2-1.8-12.8-5.4-3.6-3.6-5.4-7.9-5.4-12.8 0-5.1 1.8-9.5 5.4-13z"></path>
	</symbol>
	<symbol id="rating-like" viewBox="0 9.1 456.8 438.5">
		<path d="M441.1 252.7c10.5-12 15.7-26.2 15.7-42.5 0-14.8-5.4-27.7-16.3-38.5-10.8-10.9-23.7-16.3-38.5-16.3h-79.1c.8-2.7 1.5-4.9 2.3-6.9.8-1.9 1.8-4 3.1-6.3s2.3-4 2.9-5.1c3.4-6.5 6-11.8 7.9-16 1.8-4.2 3.6-9.9 5.4-17.1 1.8-7.2 2.7-14.5 2.7-21.7 0-4.6-.1-8.3-.1-11.1-.1-2.9-.6-7.1-1.4-12.8-.9-5.7-2-10.5-3.4-14.3-1.4-3.8-3.7-8.1-6.9-12.8-3.1-4.8-6.9-8.6-11.4-11.6s-10.2-5.4-17.1-7.4c-7-2-14.8-3-23.6-3-4.9 0-9.2 1.8-12.8 5.4-3.8 3.8-7 8.6-9.7 14.3-2.7 5.7-4.5 10.7-5.6 14.8-1 4.2-2.2 10-3.6 17.4-1.7 8-3 13.8-3.9 17.3-.9 3.5-2.5 8.1-5 13.8-2.5 5.7-5.4 10.3-8.9 13.7-6.3 6.3-15.9 17.7-28.8 34.3-9.3 12.2-18.9 23.7-28.8 34.5-9.9 10.9-17.1 16.5-21.7 16.8-4.8.4-8.8 2.3-12.3 5.9-3.4 3.5-5.1 7.7-5.1 12.4v183c0 4.9 1.8 9.2 5.4 12.7 3.6 3.5 7.9 5.4 12.8 5.6 6.7.2 21.7 4.4 45.1 12.6 14.7 4.9 26.1 8.7 34.4 11.3 8.3 2.6 19.8 5.3 34.7 8.3 14.8 2.9 28.6 4.4 41.1 4.4h36.9c25.3-.4 44.1-7.8 56.2-22.3 11-13.1 15.7-30.4 14-51.7 7.4-7 12.6-16 15.4-26.8 3.2-11.6 3.2-22.7 0-33.4 8.8-11.6 12.8-24.6 12.3-39.1 0-6.2-1.4-13.4-4.3-21.8zm-340.6-60.8H18.3c-5 0-9.2 1.8-12.9 5.4-3.6 3.6-5.4 7.9-5.4 12.8v182.7c0 4.9 1.8 9.2 5.4 12.8 3.6 3.6 7.9 5.4 12.9 5.4h82.2c4.9 0 9.2-1.8 12.8-5.4 3.6-3.6 5.4-7.9 5.4-12.8V210.1c0-4.9-1.8-9.2-5.4-12.8-3.6-3.6-7.9-5.4-12.8-5.4zM67.7 369.3c-3.6 3.5-7.9 5.3-12.8 5.3-5.1 0-9.5-1.8-13-5.3s-5.3-7.8-5.3-13c0-4.9 1.8-9.2 5.3-12.8 3.5-3.6 7.9-5.4 13-5.4 4.9 0 9.2 1.8 12.8 5.4 3.6 3.6 5.4 7.9 5.4 12.8 0 5.2-1.8 9.5-5.4 13z"></path>
	</symbol>
	<symbol id="rss" viewBox="0 0 16 16">
		<path d="M15.751 16h-3.113a.248.248 0 0 1-.245-.247c0-6.704-5.448-12.158-12.146-12.158A.247.247 0 0 1 0 3.349V.248C0 .111.111 0 .247 0c8.651 0 15.696 7.023 15.739 15.675l.014.078a.25.25 0 0 1-.249.247zM.247 5.226c5.771 0 10.475 4.682 10.518 10.449l.012.077a.248.248 0 0 1-.248.247H7.506a.248.248 0 0 1-.247-.247c0-3.87-3.147-7.019-7.012-7.019A.247.247 0 0 1 0 8.487V5.474c0-.137.111-.248.247-.248zm2.125 6.028a2.375 2.375 0 0 1 2.372 2.374A2.376 2.376 0 0 1 2.372 16 2.376 2.376 0 0 1 0 13.628a2.375 2.375 0 0 1 2.372-2.374z" class="cls-1"></path>
	</symbol>
	<symbol id="searchIcon" viewBox="0 0 16 16">
		<path d="M15.706 14.29l-4.819-4.819A5.962 5.962 0 0 0 12 6 6 6 0 0 0 0 6a6 6 0 0 0 6 6 5.963 5.963 0 0 0 3.471-1.112l4.819 4.819a.998.998 0 0 0 1.416 0 1 1 0 0 0 0-1.417zM9.258 8.312l-.393.553-.553.393A3.971 3.971 0 0 1 6 10c-2.206 0-4-1.794-4-4 0-2.205 1.794-4 4-4s4 1.795 4 4c0 .831-.256 1.63-.742 2.312z" class="cls-1"></path>
	</symbol>
	<symbol id="settings" viewBox="0 0 20 20">
		<path d="M19.429 3.522h-8.805c-.262 1.342-1.415 2.357-2.797 2.357-1.382 0-2.536-1.015-2.798-2.357H.558a.568.568 0 0 1-.562-.579c0-.321.25-.578.562-.578h4.471C5.291 1.019 6.44.008 7.822.008c1.383 0 2.536 1.016 2.798 2.357h8.809c.312 0 .562.257.562.578 0 .322-.25.579-.562.579zM7.827 1.165c-.954 0-1.728.797-1.728 1.778 0 .982.774 1.779 1.728 1.779.953 0 1.727-.797 1.727-1.779 0-.981-.774-1.778-1.727-1.778zM.558 9.406h9.184c.262-1.346 1.419-2.357 2.797-2.357 1.382 0 2.536 1.016 2.798 2.357h4.088c.312 0 .562.257.562.579 0 .321-.25.578-.562.578h-4.088c-.262 1.342-1.416 2.357-2.798 2.357-1.382 0-2.535-1.015-2.797-2.357H.558a.568.568 0 0 1-.562-.578c0-.322.25-.579.562-.579zm11.981 2.357c.954 0 1.728-.797 1.728-1.778 0-.982-.774-1.779-1.728-1.779-.953 0-1.727.797-1.727 1.779 0 .981.774 1.778 1.727 1.778zM.558 16.49h4.471c.262-1.341 1.411-2.357 2.793-2.357 1.383 0 2.536 1.016 2.798 2.357h8.805c.312 0 .562.257.562.579 0 .321-.25.578-.562.578h-8.801c-.262 1.342-1.415 2.357-2.797 2.357-1.382 0-2.536-1.015-2.798-2.357H.558a.568.568 0 0 1-.562-.578c0-.322.25-.579.562-.579zm7.269 2.357c.953 0 1.727-.797 1.727-1.778 0-.978-.774-1.779-1.727-1.779-.954 0-1.728.797-1.728 1.779 0 .981.774 1.778 1.728 1.778z" class="cls-1"></path>
	</symbol>
	<symbol id="sex-female" viewBox="1845 -379.7 768 1126.1">
		<path d="M2540.9-220.5l-.5.2c-40.3-55.8-95.3-100.4-159.2-128.1-1.1-.5-2.3-1-3.5-1.5-45.8-19.1-96-29.7-148.8-29.7-79 0-152.3 23.7-213.4 64.4C1912.9-246.7 1845-129.9 1845 2.4c0 183.3 130.1 336.9 303.2 373.9v89h-85.4c-41.1 0-74.5 33.5-74.5 74.4 0 41 33.4 74.4 74.5 74.4h85.4v58.2c0 41 33.6 74.2 74.8 74.2 41.1 0 74.7-33.1 74.7-74.2V614h89.7c41.1 0 74.4-33.5 74.4-74.4 0-40.9-33.3-74.4-74.4-74.4h-89.7v-86.5c172.3-31.3 306.6-179.1 314.9-358.8v-.2c.1-2.7.2-5.5.3-8.3v-1c.1-2.7.1-5.4.1-8.1v-5.2c-.4-25.9-3.5-51.2-8.8-75.6l.2-.1c-11.3-52.1-33.3-100.2-63.5-141.9zM2229 236.4c-129.6-.1-235-105-235-234.1s105.4-234 235-234 234.9 104.9 234.9 234-105.3 234.1-234.9 234.1z"></path>
	</symbol>
	<symbol id="sex-gay" viewBox="0 0 22.59 22.625">
		<path d="M22.342 12.112l-2.358 4.085c-.422.729-1.341.987-2.056.574-.715-.413-.951-1.337-.53-2.067l.532-.922-2.382.605c.15 3.599-2.266 6.955-5.861 7.955a7.6 7.6 0 0 1-5.872-.731 7.585 7.585 0 0 1-3.572-4.714c-1.072-4.156 1.416-8.464 5.55-9.614.803-.223 1.614-.277 2.417-.238l-.714-.239.738-2.074-.713.507a.15.15 0 0 0-.018.009c-.734.358-1.686-.068-2.061-.813-.379-.752-.146-1.78.592-2.152 0 0 4.139-2.08 4.201-2.105 1.361-.563 1.862.319 2.043.683L14.347 5.1c.37.757.069 1.663-.673 2.025-.742.363-1.641.042-2.01-.715l-.467-.956-.73 2.036c.405.15.807.319 1.189.54 1.38.796 2.43 2.011 3.084 3.439l2.133-.547-.82-.308-.017-.01c-.707-.408-.886-1.436-.478-2.163.412-.735 1.395-1.116 2.115-.712 0 0 4.039 2.27 4.094 2.307 1.227.814.777 1.722.575 2.076zm-10.01 1.415a4.638 4.638 0 0 0-2.184-2.887 4.636 4.636 0 0 0-3.592-.448c-2.532.703-4.057 3.345-3.399 5.887a4.64 4.64 0 0 0 2.184 2.888 4.64 4.64 0 0 0 3.592.447c2.53-.702 4.055-3.344 3.399-5.887z" class="cls-1"></path>
	</symbol>
	<symbol id="sex-male" viewBox="246 -167.9 1089 814.2">
		<path d="M1286.2-66.3c-3-1.6-220.6-96.2-220.6-96.2-38.8-16.8-86.9 7.7-104 47.3-17 39.2-2.6 90.6 35.6 107.7.3.2.6.3.9.4l43.3 11.4-100 36.9c-52-71.9-128.3-125.3-216.9-147.8-23.2-5.9-47.3-9.7-72-11.1-7.5-.4-15-.7-22.6-.7-211.7 0-384 171.6-384 382.4 0 183.3 130.1 336.9 303.2 373.9v.3c25.8 5.4 52.6 8.3 80 8.3 56.2 0 109.6-12 157.7-33.5 133.1-60 227.1-193.8 227.1-348.9 0-27.8-3.2-54.8-8.9-80.9l105.1-38.4-22.3 49.7c-17.7 39.4-.9 85.2 37.7 102.6 38.6 17.3 84.1-.6 101.7-39.9l98.9-220.4c8.6-19.4 26.8-68-39.9-103.1zM630 497.9c-129.6-.1-235-105-235-234.1s105.4-234 235-234 234.9 104.9 234.9 234S759.5 497.9 630 497.9z"></path>
	</symbol>
	<symbol id="sex-straight" viewBox="0 0 25 25">
		<path d="M23.502 7.533a1.484 1.484 0 0 1-1.497-1.481V5.017l-1.8 1.666c1.954 2.957 1.518 6.956-1.128 9.544a7.853 7.853 0 0 1-5.52 2.239 7.98 7.98 0 0 1-2.569-.421 8.5 8.5 0 0 0 3.459-2.627 4.759 4.759 0 0 0 2.488-1.287 4.603 4.603 0 0 0 0-6.608 4.796 4.796 0 0 0-3.377-1.369 4.8 4.8 0 0 0-3.378 1.369 4.602 4.602 0 0 0-.459 6.086 3.92 3.92 0 0 1-3.281.36 7.51 7.51 0 0 1 1.598-8.524c1.475-1.441 3.434-2.22 5.519-2.22 1.613 0 3.148.514 4.447 1.397l1.592-1.495-.874.141h-.02c-.825 0-1.502-.779-1.512-1.59-.012-.819.66-1.619 1.494-1.629 0 0 4.694-.052 4.763-.049 1.486.09 1.554 1.073 1.554 1.469v4.583c0 .817-.662 1.481-1.499 1.481zM5.94 6.116c-1.726.681-2.949 2.335-2.949 4.265 0 2.536 2.108 4.598 4.7 4.598 2.594 0 4.701-2.062 4.701-4.598a4.49 4.49 0 0 0-.754-2.493 3.589 3.589 0 0 1 1.918-.549c.436 0 .858.078 1.254.224.363.87.558 1.821.558 2.818 0 3.686-2.722 6.759-6.316 7.393v1.675h1.813c.822 0 1.489.66 1.489 1.465 0 .806-.666 1.465-1.489 1.465H9.052v1.165c0 .804-.673 1.456-1.497 1.456-.823 0-1.497-.652-1.497-1.456v-1.165H4.37c-.824 0-1.492-.659-1.492-1.465 0-.804.668-1.465 1.492-1.465h1.688v-1.723C2.613 17.001 0 13.985 0 10.382 0 6.24 3.459 2.869 7.694 2.869a7.84 7.84 0 0 1 1.654.177 8.944 8.944 0 0 0-3.408 3.07z" class="cls-1"></path>
	</symbol>
	<symbol id="sex-trans" viewBox="0 0 21.25 22.969">
		<path d="M21.099 3.995l-1.931 4.304c-.345.769-1.234 1.118-1.987.78-.754-.338-1.082-1.234-.737-2.003l.435-.97-2.052.75c.111.51.173 1.039.173 1.581 0 3.663-2.683 6.72-6.158 7.351v1.689h1.752c.804 0 1.454.654 1.454 1.453 0 .8-.651 1.454-1.454 1.454H8.842v1.136c0 .802-.656 1.449-1.459 1.449a1.457 1.457 0 0 1-1.461-1.449v-1.136H4.254a1.457 1.457 0 0 1-1.455-1.454c0-.799.652-1.453 1.455-1.453h1.668v-1.738C2.541 15.018 0 12.018 0 8.437 0 4.32 3.366.969 7.501.969a7.497 7.497 0 0 1 6.082 3.118l1.953-.72-.846-.223-.018-.008c-.745-.334-1.027-1.338-.695-2.103.334-.773 1.273-1.252 2.031-.924 0 0 4.249 1.848 4.308 1.879 1.303.686.947 1.635.783 2.007zm-13.6-.128c-2.53 0-4.589 2.049-4.589 4.57 0 2.521 2.058 4.571 4.589 4.572 2.531 0 4.588-2.051 4.588-4.572s-2.057-4.57-4.588-4.57z" class="cls-1"></path>
	</symbol>
	<symbol id="share" viewBox="0 0 21.09 22">
		<path d="M17.205 14.156a3.866 3.866 0 0 0-3.014 1.457l.003-.002-6.588-3.561a3.859 3.859 0 0 0-.048-2.248l6.503-3.58a3.865 3.865 0 0 0 3.144 1.629c2.147-.001 3.883-1.754 3.883-3.919 0-2.167-1.736-3.92-3.883-3.92-2.142 0-3.878 1.753-3.878 3.92 0 .331.045.65.118.958L6.86 8.51c.011.016.022.028.03.042a3.852 3.852 0 0 0-3.023-1.467c-2.143 0-3.88 1.754-3.88 3.919 0 2.167 1.737 3.918 3.88 3.919a3.86 3.86 0 0 0 3.092-1.557l6.545 3.538c-.117.37-.177.762-.177 1.173 0 2.165 1.736 3.92 3.878 3.92 2.147 0 3.883-1.755 3.883-3.92 0-2.164-1.736-3.919-3.883-3.921zm0-12.672a2.438 2.438 0 0 1 2.427 2.448c-.003 1.353-1.086 2.448-2.427 2.448-1.339 0-2.42-1.095-2.426-2.448.006-1.355 1.087-2.446 2.426-2.448zM3.867 13.454c-1.34-.002-2.423-1.096-2.427-2.45a2.445 2.445 0 0 1 2.427-2.45 2.442 2.442 0 0 1 2.425 2.45c-.002 1.354-1.086 2.448-2.425 2.45zm13.338 7.073c-1.339-.004-2.42-1.097-2.426-2.45.006-1.353 1.087-2.448 2.426-2.449 1.341.001 2.424 1.096 2.427 2.449a2.443 2.443 0 0 1-2.427 2.45z" class="cls-1"></path>
	</symbol>
	<symbol id="star" viewBox="0 0 18 18">
		<path d="M17.929 6.843a.9.9 0 0 0-.488-.526c-.898-.394-3.258-.543-5.3-.619-.637-1.856-1.667-4.594-2.529-5.438a.882.882 0 0 0-.618-.263.958.958 0 0 0-.637.263c-.843.844-1.873 3.582-2.51 5.438-2.042.076-4.402.225-5.301.619a.841.841 0 0 0-.487.526.885.885 0 0 0 .056.75c.581 1.068 2.061 2.663 3.914 4.238-.504 2.063-.842 3.9-.936 5.194a.909.909 0 0 0 .412.825.866.866 0 0 0 .9.057c.318-.188.674-.357 1.03-.526.974-.487 2.154-1.031 3.559-1.912 1.404.881 2.584 1.425 3.559 1.912.356.169.712.338 1.029.526.131.056.262.093.413.093a.925.925 0 0 0 .487-.15.908.908 0 0 0 .413-.825c-.094-1.294-.432-3.131-.938-5.194 1.855-1.575 3.335-3.17 3.915-4.238a.881.881 0 0 0 .057-.75z" class="cls-1"></path>
	</symbol>
	<symbol id="text" viewBox="0 0 18 15">
		<path d="M16.615 15H1.384c-.765 0-1.385-.395-1.385-.882V.882C-.001.395.619 0 1.384 0h15.231C17.38 0 18 .395 18 .882v13.236c0 .487-.62.882-1.385.882zM3.461 11.443h6.923c.383 0 .693-.198.693-.442 0-.243-.31-.441-.693-.441H3.461c-.382 0-.691.198-.691.441 0 .244.309.442.691.442zm11.078-8.824H3.461c-.382 0-.691.198-.691.441 0 .244.309.442.691.442h11.078c.381 0 .692-.198.692-.442 0-.243-.311-.441-.692-.441zm0 2.647H3.461c-.382 0-.691.198-.691.441 0 .244.309.441.691.441h11.078c.381 0 .692-.197.692-.441 0-.243-.311-.441-.692-.441zm0 2.647H3.461c-.382 0-.691.198-.691.441 0 .244.309.442.691.442h11.078c.381 0 .692-.198.692-.442 0-.243-.311-.441-.692-.441z" class="cls-1"></path>
	</symbol>
	<symbol id="time" viewBox="0 0 12 12">
		<path d="M6.3 3h-.9v3.6l3.12 1.92.48-.78-2.7-1.62V3M6 10.8c-2.64 0-4.8-2.16-4.8-4.8 0-2.64 2.16-4.8 4.8-4.8 2.64 0 4.8 2.16 4.8 4.8 0 2.64-2.16 4.8-4.8 4.8M6 0C2.7 0 0 2.7 0 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6" class="cls-1"></path>
	</symbol>
	<symbol id="top-rated" viewBox="0 0 39.97 37">
		<path d="M39.338 15.008l-4.452-4.53L19.993 25.64a.883.883 0 0 1-1.264 0l-4.518-4.594-8.052 8.2a3.528 3.528 0 0 1-5.054.001l-.088-.09a3.685 3.685 0 0 1-.001-5.146L13.58 11.214a.883.883 0 0 1 1.263 0l4.518 4.598 8.542-8.702 1.842-1.867L25.229.644C24.88.288 24.998 0 25.491 0h12.694c.909 0 1.785.815 1.785 1.745v12.996c0 .502-.283.622-.632.267zM5.469 33.687l1.696-1.729c.349-.355.631-.236.631.266V37H3.633v-.72c0-.399.283-1.009.631-1.365l1.205-1.228zm7.16-7.29c.349-.356.632-.236.632.266v10.336h-3.73v-6.54c0-.501.282-1.196.632-1.552l2.466-2.51zm2.995-.169l1.898 1.93.6.607c.331.336.6 1.015.6 1.517v6.717h-3.73V26.494c0-.502.283-.621.632-.266zm5.571 1.929l2.355-2.395c.349-.356.632-.236.632.266v10.971h-3.73v-7.174c0-.502.167-1.08.371-1.289l.372-.379zm7.817-7.959c.349-.355.632-.236.632.266V37h-3.73V24.265c0-.503.283-1.198.632-1.554l2.466-2.513zm5.196-5.29c.349-.356.633-.236.633.266v21.825h-3.468V18.698c0-.502.283-1.197.633-1.552l2.202-2.238z" class="cls-1"></path>
	</symbol>
	<symbol id="triangle-right" viewBox="0 0 3 5">
		<path d="M3 2.5L0 0v5l3-2.5z" class="cls-1"></path>
	</symbol>
	<symbol id="tube" viewBox="0 0 21 16">
		<path d="M17.062 0H3.938C1.772 0 0 1.8 0 4v8c0 2.2 1.772 4 3.938 4h13.124C19.228 16 21 14.2 21 12V4c0-2.2-1.772-4-3.938-4zm2.625 12c0 .708-.274 1.376-.772 1.882a2.585 2.585 0 0 1-1.852.785H3.938a2.585 2.585 0 0 1-1.853-.785A2.667 2.667 0 0 1 1.313 12V4c0-.708.274-1.376.772-1.882a2.585 2.585 0 0 1 1.853-.785h13.124c.697 0 1.355.279 1.853.785s.772 1.174.772 1.882v8zM7.875 13.333L14.438 8 7.875 2.667v10.666z" class="cls-1"></path>
	</symbol>
	<symbol id="tumbler" viewBox="0 0 512 512">
		<path d="M121 212.7v-6.2c0-20.3.1-40.7-.1-61 0-4.2.8-6.1 5.4-8.1 16.6-7.4 31.9-17 44.5-30.7 16.5-17.9 27.7-38.4 33.4-61.9 2.8-11.4 4.8-23 6.8-34.6.7-3.7 1.9-5.6 6-5.5 18.2.2 36.3.2 54.5 0 4.8-.1 4.5 2.7 4.5 5.8.2 33.2.4 66.3.7 99.5v22.3h6c31.5 0 63 .1 94.5-.1 4.9 0 6.7 1.3 6.7 6.4-.2 22.7-.2 45.3 0 68 0 4.8-1.5 6.3-6.3 6.2-31.2-.2-62.3-.1-93.5-.1h-7c-.1 1.9-.3 3.5-.3 5.1.1 47.8.1 95.7.4 143.5.1 10.1.5 20.4 1.9 30.4 2.4 16.4 13 26.4 28 31.8 20 7.1 39.8 4.6 59-2.8 10.6-4.1 20.5-10.2 30.9-15.4.1.6.4 2 .4 3.4 0 23.5-.1 47 .1 70.5 0 3.9-1.9 5.3-5 6.8-14.4 6.9-29.1 12.6-44.3 17.5-22.6 7.2-45.8 6.8-68.9 5.9-34.6-1.4-64.2-14.7-87-40.9-11.8-13.5-19.9-29.4-20.2-47.9-.8-61.1-1.2-122.3-1.8-183.4-.1-6.5-.2-13 0-19.5.1-3.8-1.4-4.9-5-4.8-12.7.1-25.3.1-38 .1-1.8-.3-3.6-.3-6.3-.3z"></path>
	</symbol>
	<symbol id="tv" viewBox="0 0 37 39.969">
		<path d="M33.636 13.301H25.9l10.595-10.5c.673-.667.673-1.667 0-2.334s-1.682-.667-2.355 0L20.686 13.801c-.504.5-.672 1.167-.336 1.833.168.667.841 1 1.513 1h11.773v16.668H3.363V16.634h11.773c.673 0 1.345-.333 1.514-1 .336-.666.168-1.333-.337-1.833L9.586 7.134c-.673-.667-1.682-.667-2.355 0-.672.667-.672 1.667 0 2.334l3.869 3.833H3.363c-1.85 0-3.363 1.5-3.363 3.334v16.667c0 1.833 1.513 3.333 3.363 3.333h6.728v3.334h16.818v-3.334h6.727c1.849 0 3.363-1.499 3.363-3.333V16.634c0-1.833-1.513-3.333-3.363-3.333z" class="cls-1"></path>
	</symbol>
	<symbol id="tv2" viewBox="0 0 18 20">
		<path d="M10.496 4.462l3.649-3.307L13.113 0l-4.87 4.414-3.326-3.35-1.091 1.099 2.283 2.299H0v13.984h3.086V20h11.828v-1.554H18V4.462h-7.504zm2.618 12.171H1.8V6.275h11.314v10.358zm3.086 0h-1.543V15.08H16.2v1.553zm0-3.366h-1.543v-1.554H16.2v1.554z" class="cls-1"></path>
	</symbol>
	<symbol id="twitter-circle" viewBox="0 0 26 26">
		<path d="M13 0C5.82 0 .001 5.82.001 13 .001 20.179 5.82 26 13 26c7.18 0 13-5.821 13-13 0-7.18-5.82-13-13-13zm5.287 10.649c.005.11.007.221.007.332 0 3.383-2.575 7.285-7.284 7.285a7.237 7.237 0 0 1-3.924-1.151 5.131 5.131 0 0 0 3.79-1.06 2.563 2.563 0 0 1-2.392-1.778 2.557 2.557 0 0 0 1.156-.044 2.562 2.562 0 0 1-2.053-2.51v-.032c.345.191.74.306 1.159.32A2.56 2.56 0 0 1 7.608 9.88c0-.47.125-.909.346-1.288a7.267 7.267 0 0 0 5.277 2.676 2.56 2.56 0 0 1 4.363-2.336 5.145 5.145 0 0 0 1.626-.621 2.567 2.567 0 0 1-1.126 1.417 5.176 5.176 0 0 0 1.471-.403 5.173 5.173 0 0 1-1.278 1.324z" class="cls-1"></path>
	</symbol>
	<symbol id="upload-arr" viewBox="0 0 10 12">
		<path d="M-.005 12.009v-1.413h10.006v1.413H-.005zm7.147-2.825H2.854V4.945H-.005L4.999.001l5.002 4.944H7.142v4.239z" class="cls-1"></path>
	</symbol>
	<symbol id="upload" viewBox="0 0 22 14">
		<path d="M20.084 7.066c.076-.38.117-.773.117-1.175C20.201 2.637 17.579 0 14.345 0A5.855 5.855 0 0 0 8.98 3.528a4.39 4.39 0 0 0-2.416-.723c-2.346 0-4.262 1.832-4.421 4.15A3.69 3.69 0 0 0 0 10.309C0 12.347 1.643 14 3.669 14H8.97c.26 0 .273-.29.273-.29v-2.368a.369.369 0 0 0-.366-.368H7.874c-.201 0-.268-.133-.148-.296l3.056-4.15c.12-.162.316-.162.436 0l3.056 4.15c.119.163.053.296-.148.296h-1.003a.368.368 0 0 0-.365.368v2.373s.015.285.284.285h5.289C20.357 14 22 12.347 22 10.309c0-1.4-.775-2.617-1.916-3.243z" class="cls-1"></path>
	</symbol>
	<symbol id="upload2" viewBox="0 0 29 24">
		<path d="M23.807 18.383H18.73c-.03 0-.06-.003-.09-.005l-.14-.005-.103.005c-.03.002-.059.005-.09.005h-1.203v-2.218l.606.598c.405.399.935.599 1.465.599.531 0 1.061-.2 1.465-.599a2.022 2.022 0 0 0 0-2.888L16.497 9.79a2.05 2.05 0 0 0-.316-.255c-.05-.034-.106-.057-.159-.085-.065-.035-.128-.074-.197-.103-.069-.028-.14-.044-.21-.064-.059-.018-.116-.04-.177-.052a2.128 2.128 0 0 0-.811 0c-.061.013-.117.035-.176.052-.071.02-.142.036-.21.064-.07.028-.133.068-.198.103-.053.028-.109.052-.159.085a2.05 2.05 0 0 0-.316.255l-4.143 4.085a2.022 2.022 0 0 0 0 2.888c.405.399.935.599 1.465.599s1.061-.2 1.465-.599l.606-.598v2.218H5.616C2.519 18.383 0 15.899 0 12.845c0-2.122 1.401-4.323 3.331-5.236l.294-.138v-.322c0-.065.004-.131.008-.197l.004-.082-.006-.107a1.715 1.715 0 0 1-.006-.125c0-1.97 1.627-3.574 3.625-3.574a3.724 3.724 0 0 1 .595.052c.138.023.275.052.409.09l.055.014c.147.045.291.101.432.164.037.016.073.034.109.052a3.558 3.558 0 0 1 2.025 3.202c0 .283.231.511.518.511a.514.514 0 0 0 .518-.511 4.561 4.561 0 0 0-1.657-3.508C11.738 1.215 14.183 0 16.615 0c4.011 0 7.342 3.133 7.69 7.091a10.152 10.152 0 0 0-2.115.063.512.512 0 0 0-.434.582.516.516 0 0 0 .59.428c1.153-.172 2.355-.011 2.426-.001 2.41.453 4.228 2.643 4.228 5.099 0 2.824-2.33 5.121-5.193 5.121zM10.875 16.34a1.04 1.04 0 0 1-.733-.299 1.012 1.012 0 0 1 0-1.444l4.143-4.084a.972.972 0 0 1 .158-.128c.026-.017.054-.029.081-.044.032-.017.063-.036.097-.05.035-.014.071-.023.106-.033.029-.008.057-.02.087-.025.135-.027.272-.027.406 0 .03.006.058.017.087.025.036.01.072.019.107.033.034.014.064.032.096.05.028.015.056.027.082.044.057.037.11.08.159.128l4.142 4.084a1.012 1.012 0 0 1 0 1.444c-.202.2-.467.299-.732.299-.265 0-.53-.099-.732-.299l-2.375-2.342v9.28A1.03 1.03 0 0 1 15.018 24a1.03 1.03 0 0 1-1.036-1.021v-9.28l-2.375 2.342c-.202.2-.467.299-.732.299z" class="cls-1"></path>
	</symbol>
	<symbol id="user" viewBox="0 0 15 15">
		<path d="M7.5 0C3.364 0 0 3.365 0 7.5S3.364 15 7.5 15c4.135 0 7.5-3.365 7.5-7.5S11.636 0 7.5 0zm4.325 11.633v-.295c0-1.023-1.182-1.396-1.815-1.667a29.506 29.506 0 0 1-1.103-.522.605.605 0 0 1-.302-.434l-.049-.485c.34-.316.645-.755.696-1.245h.077c.12 0 .233-.09.251-.2l.12-.741c.016-.159-.133-.255-.254-.255a4.316 4.316 0 0 0 .055-.513c.012-.35-.046-.656-.145-.827a1.96 1.96 0 0 0-.461-.672c-.586-.554-1.265-.77-1.845-.325-.394-.068-.863.113-1.202.509a1.59 1.59 0 0 0-.349.708 2.241 2.241 0 0 0-.08.535c-.008.222.013.425.054.588a.25.25 0 0 0-.231.252l.12.741c.018.111.131.2.251.2h.069c.109.53.39.965.702 1.269l-.047.468a.608.608 0 0 1-.302.435c-.428.209-.85.41-1.103.514-.595.245-1.815.644-1.815 1.667v.231a5.96 5.96 0 0 1-1.605-4.068 5.995 5.995 0 0 1 5.989-5.989 5.996 5.996 0 0 1 5.99 5.989 5.971 5.971 0 0 1-1.666 4.132z" class="cls-1"></path>
	</symbol>
	<symbol id="windows" viewBox="0 0 24.72 23.156">
		<path d="M23.731 16.725h-2.824v-5.083h1.821V1.916H9.042v1.501H7.036V.954c0-.531.45-.961 1.003-.961h15.692c.554 0 1.002.43 1.002.961v14.809c0 .531-.448.962-1.002.962zm-6.037-9.328v14.809c0 .531-.45.962-1.003.962H.999c-.554 0-1.003-.431-1.003-.962V7.397c0-.531.449-.961 1.003-.961h15.692c.553 0 1.003.43 1.003.961zm-2.006.962H2.002v9.726h13.686V8.359z" class="cls-1"></path>
	</symbol>
	<symbol id="wordpress" viewBox="0 0 56.693 56.693">
		<path d="M3.59 29.355c0 9.742 5.663 18.17 13.878 22.156L5.723 19.332A24.51 24.51 0 0 0 3.59 29.355zm41.246-1.244c0-3.043-1.096-5.152-2.031-6.791-1.248-2.03-2.416-3.745-2.416-5.776 0-2.264 1.711-4.369 4.135-4.369.105 0 .211.014.316.021A24.527 24.527 0 0 0 28.215 4.73c-8.605 0-16.172 4.416-20.573 11.1.579.018 1.121.031 1.583.031 2.577 0 6.563-.316 6.563-.316 1.324-.074 1.481 1.871.154 2.031 0 0-1.332.154-2.817.234l8.968 26.668 5.389-16.158-3.838-10.51a47.748 47.748 0 0 1-2.582-.234c-1.328-.08-1.17-2.105.158-2.031 0 0 4.064.316 6.482.316 2.574 0 6.566-.316 6.566-.316 1.322-.074 1.48 1.871.152 2.031 0 0-1.33.154-2.816.234l8.9 26.465 2.457-8.203c1.248-3.201 1.875-5.851 1.875-7.961zm-16.19 3.399l-7.389 21.469c2.205.648 4.535 1 6.957 1 2.865 0 5.617-.496 8.176-1.398a2.137 2.137 0 0 1-.176-.336L28.646 31.51zm21.172-13.971c.109.787.17 1.627.17 2.535 0 2.497-.469 5.305-1.875 8.819l-7.52 21.742c7.318-4.266 12.24-12.195 12.24-21.279a24.508 24.508 0 0 0-3.015-11.817z"></path>
	</symbol>
	<symbol id="world" viewBox="0 0 16 16">
		<path d="M8.002 16.004c-4.414 0-8.006-3.591-8.006-8.006S3.588-.008 8.002-.008s8.006 3.591 8.006 8.006-3.592 8.006-8.006 8.006zm4.39-3.278c.082-.027.221-.193.303-.248.082-.055.22-.276.248-.386.027-.11.081-.303.11-.413.027-.11.055-.331.055-.414 0-.082-.028-.193-.026-.192l-.221.413s-.331.138-.359.248c-.027.111-.055.276-.055.276s-.137.303-.166.413c-.027.11.029.331.111.303zM2.165 4.098c-.15-.277 0-.303 0-.413 0 0-.02-.008-.048-.011A7.252 7.252 0 0 0 .697 7.82c.048-.116.109-.399.09-.495-.028-.138.082-.469.082-.635 0-.166.276-.496.331-.634.055-.139.303-.496.441-.717.137-.221-.028-.138-.166-.056-.137.084.083-.22.166-.275.082-.055.551-.386.551-.386s.165-.165-.027-.524zm.303 7.058a2.154 2.154 0 0 1-.193-.359c-.055-.139-.027-.139-.248-.386-.22-.248-.165-.193-.331-.276-.165-.083-.221-.164-.358-.22a.93.93 0 0 1-.304-.193.396.396 0 0 0-.158-.099A7.314 7.314 0 0 0 3.02 13.34c.111.03.146-.034.248-.034.109 0 .275-.139.275-.139l-.111-.33.111-.414.138-.385s0-.276-.138-.304c-.138-.027-.386-.275-.469-.275a.678.678 0 0 1-.303-.11c-.083-.056-.247-.11-.303-.193zm.583-8.528a1.156 1.156 0 0 0-.033.092c-.027.111.194.028.194.028l.248.11.303-.11s.084-.166.276-.303c.193-.139.166-.167.248-.277.083-.109 0 .001.358-.385.359-.387.551-.386.551-.386s.105-.113.19-.223a7.335 7.335 0 0 0-2.335 1.454zM5.859 6.33c-.082.056-.386.221-.468.358a1.519 1.519 0 0 0-.193.442c-.027.138-.027.248-.027.44 0 .193-.028.304 0 .47.026.165.302.578.412.717.111.138.249.303.387.385.137.083.331.248.331.248s.109.165.329.083c.222-.083.441-.083.635-.193s.497-.249.497-.083c0 .165.109.276.275.221.165-.056.441-.193.413 0-.027.193.055.441.055.552 0 .109-.027.22.055.303.083.082.304.22.304.303 0 .082.027.358.082.469.056.11.11.275.055.385-.055.111-.166.414-.166.414s-.058.248-.03.33a.8.8 0 0 0 .216.331c.138.138.102.304.185.414.083.11.167.441.167.441l.012.276v.247s.608-.082.719-.11c.11-.027.541-.221.65-.331.111-.11.089-.193.226-.303.138-.11.389-.304.389-.304l-.027-.413.332-.44.249-.249s.22-.303.193-.413c-.027-.11-.083-.662-.083-.662s.111-.247.194-.358c.082-.111.083-.248.248-.386.165-.137.275-.303.358-.413.082-.11.248-.331.303-.413.055-.083.111-.331.111-.331l-.498.083c-.165-.276-.634-.882-.716-1.02-.083-.138-.165-.414-.193-.524-.028-.11-.166-.414-.194-.496-.027-.083-.219-.331-.274-.414-.056-.082-.249-.138-.359-.221-.111-.082-.799-.22-.799-.22s-.211-.133-.276-.083c-.275.221-.91.083-1.046.027-.139-.056-.221-.137-.221-.221 0-.083-.124-.482-.124-.482s-.269.048-.352.048c-.083 0-.299-.087-.299-.087s-.275.026-.385.082c-.109.055-.33.137-.412.165-.083.027-.303.055-.303.055-.248-.111-.44.165-.496.275-.055.11-.165.165-.165.275 0 .111-.194.276-.276.331zm1.226-3.692l-.211-.304-.254.138v.492l-.265-.106-.11.248.11.165.265-.292v.292l.574-.11.215-.165-.324-.358zm4.79-.834c-.115.064-.315.214-.392.136-.083-.083-.276-.202-.276-.202.166-.111-.331-.295-.331-.295l-.578.047h-.413c-.083 0-.249-.06-.387-.06-.137 0-.468.126-.579.154-.109.027-.358.168-.468.168-.11 0-.276.208-.276.208s-.192.353-.026.298c.164-.056.385-.059.412.025.028.082.082.218.11.301.028.082.414.109.414.109s.055-.138.137-.248c.083-.111.165-.248.165-.248s.138.275.166.358c.027.083-.055.331-.055.331s-.303.192-.386.192c-.083 0-.552-.414-.552-.414-.166.056-.303.139-.303.139-.193.193-.386.358-.386.358s-.167.056-.277.138c-.11.083-.348.1-.414.193-.137.193.053.331.053.331l-.169.22-.119.083-.402.083-.242.041c.027.082-.026.345-.026.345v.467s.398.11.481.11c.082 0 .287.028.398-.083.109-.11.288-.358.343-.44a.523.523 0 0 1 .296-.221c.164-.054.187-.137.27-.164.083-.028.408-.084.408-.084s.191.139.273.194c.039.026-.051.112.295.196v.218l.366-.028-.166-.22-.028-.008-.319-.323-.024-.248s.34.193.45.248c.11.055.345.22.345.22l.163.385.221.269c-.074-.092-.257-.352.028-.352 0 0 .262.028.345 0 .083-.027.158-.054.267-.109.11-.056-.043-.248.066-.386a.701.701 0 0 1 .337-.221 5.14 5.14 0 0 0 .361-.165l.305.055c-.137.11-.302.275-.192.248.11-.027.359.111.469.193.11.082.027.276.027.276s-.302-.028-.495-.056c-.193-.027-.497.028-.497.028l-.247.137s-.166.056-.138.193c.027.138.138.304.248.441.109.139.192.304.274.249.084-.056.36-.221.36-.221s-.056.193-.028.331c.028.137.028.33.028.33s-.276 0-.193.056c.081.054.199.263.247.331.276.385.855 1.24.91 1.35.054.111.029.278.11.358.165.166.496 0 .578-.055.083-.054.359-.193.441-.275.084-.083.056-.056.221-.165.166-.111.276-.194.193-.276-.082-.083-.247-.386-.247-.386-.249.055-.497-.083-.497-.165 0-.083-.22-.413-.22-.413s.027-.055.22.055c.194.11.358.331.524.331.165 0 .247.055.441.165.193.11.276.248.386.303.11.055.275.276.275.276s.166.386.193.497c.019.072.097.242.148.372.009-.138.021-.275.021-.415a7.31 7.31 0 0 0-3.441-6.194zM6.62 2.979v-.015l.009.004-.009.011z" class="cls-1"></path>
	</symbol>
</svg>

