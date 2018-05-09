<a id="back-top" class="hidden-phone hidden-tablet" title="{$lang.top}">
	<i class="icon-chevron-up"></i>
	<span></span>
</a>

{if $ad_6}
<div class="floating_ad_left sticky_ads">
{$ad_6}
</div>
{/if}

{if $ad_7}
<div class="floating_ad_right sticky_ads">
{$ad_7}
</div>
{/if}

</div><!-- end wrapper -->

<div class="row-fluid fixed960">
	<div class="row-fluid">
	{if $tpl_name == "video-category"}
	<a href="{$smarty.const._URL}/rss.php?c={$cat_id}" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> RSS</a>
	{elseif $tpl_name == "video-new"}
	<a href="{$smarty.const._URL}/rss.php" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> RSS</a>
	{elseif $tpl_name == "video-top"}
	<a href="{$smarty.const._URL}/rss.php?feed=topvideos" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> RSS</a>
	{elseif $tpl_name == "article-category" || $tpl_name == "article-read"}
	<a href="{$smarty.const._URL}/rss.php?c={$cat_id}&feed=articles" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> RSS</a>
	{else}
	<a href="{$smarty.const._URL}/rss.php" title="{$meta_title} RSS" class="pm-rss-link"><i class="pm-vc-sprite ico_rss"></i> RSS</a>
	{/if}
	</div>
</div>

{if $ad_2 != ''}
<div class="pm-ad-zone" align="center">{$ad_2}</div>
{/if}

<footer>
<div class="row-fluid fixed960">
	<div class="span8">
	<ul>
		{if $smarty.const.MOBILE_MELODY && $smarty.const.USER_DEVICE == 'mobile'}
			<li><a href="{$_footer_switch_ui_link}" rel="nofollow">{$lang.switch_to_mobile_ui}</a></li>
		{/if}
		<li><a href="{$smarty.const._URL}/index.{$smarty.const._FEXT}">{$lang.homepage}</a></li>
		<li><a href="{$smarty.const._URL}/contact_us.{$smarty.const._FEXT}">{$lang.contact_us}</a></li>
		{if $logged_in != '1' && $allow_registration == '1'}
			{if $allow_facebook_login || $allow_twitter_login}
			<li><a class="primary ajax-modal" data-toggle="modal" data-backdrop="true" data-keyboard="true" href="#header-register-form">{$lang.register}</a></li>
			{else}
			<li><a href="{$smarty.const._URL}/register.{$smarty.const._FEXT}">{$lang.register}</a></li>
			{/if}
		{/if}
		{if $logged_in == '1' && $s_power == '1'}<li><a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/">{$lang.admin_area}</a></li>{/if}
		{if is_array($footer_page_links)}
		  {foreach from=$footer_page_links key=k item=page_data}
			<li><a href="{$page_data.page_url}">{$page_data.title}</a></li>
		  {/foreach}
		{/if}
	</ul>
	<p>
	{if $smarty.const._POWEREDBY == 1}{$lang.powered_by}<br />{/if}
	&copy; {$smarty.now|date_format:'%Y'} {$smarty.const._SITENAME}. {$lang.rights_reserved}
	</p>
	</div>
	<div class="span3">

	</div>
</div>
</footer>
<div id="lights-overlay"></div>

{literal}

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="/js/jquery.browser.min.js"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.cookee.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/theme.js" type="text/javascript"></script>
{/literal}
<script type="text/javascript" src="js/video.js"></script>

{if $smarty.const._EU_WARNING == '1'}
{literal}
<script type="text/javascript">
window.cookieconsent_options = {"message":"{/literal}{$lang.eu_cookie_warn_message}{literal}","dismiss":"{/literal}{$lang.i_agree}{literal}","learnMore":"{/literal}{$lang.more_info}{literal}","link":null,"theme":"cookieconsent-{/literal}{$smarty.const._EU_WARNING_POSITION}{literal}"};
</script>
<script type="text/javascript" src="{/literal}{$smarty.const._URL}/js/cookieconsent.min.js"></script>
{/if}

{if $p == "index"}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.carouFredSel.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.touchwipe.min.js" type="text/javascript"></script>
{/literal}
{/if}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery-scrolltofixed-min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.uniform.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.ba-dotimeout.min.js" type="text/javascript"></script>
{/literal}{if $tpl_name == "upload" || $tpl_name == "video-edit"}{literal}
<script src="{/literal}{$smarty.const._URL}{literal}/js/jquery.ui.widget.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}{literal}/js/jquery.iframe-transport.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}{literal}/js/jquery.fileupload.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}{literal}/js/upload.js" type="text/javascript"></script>
{/literal}{/if}
{if $smarty.const._SEARCHSUGGEST == 1}{literal}
<script src="{/literal}{$smarty.const._URL}{literal}/js/jquery.typewatch.js" type="text/javascript"></script>
{/literal}{/if}{literal}
<script type="text/javascript" src="{/literal}{$smarty.const._URL}{literal}/js/bootstrap-notify.min.js"></script>
<script src="{/literal}{$smarty.const._URL}{literal}/js/melody.dev.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/melody.dev.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/lightbox.min.js" type="text/javascript"></script>
<script type="text/javascript">
// Global settings for notifications
$.notifyDefaults({
	// settings
	element: 'body',
	position: null,
	type: "info",
	allow_dismiss: true,
	newest_on_top: true,
	showProgressbar: false,
	placement: {
		from: "top", // top, bottom
		align: "right" // left, right, center
	},
	offset: {
		x: 20,
		y: 100
	  },
	spacing: 10,
	z_index: 1031,
	delay: 10000,
	timer: 1000,
	url_target: '_blank',
	mouse_over: null,
	animate: {
		enter: 'animated fadeInDown',//'animated fadeIn',
		exit: 'animated fadeOutUpBig',//'animated fadeOut'
	},
	onShow: null,
	onShown: null,
	onClose: null,
	onClosed: null,
	template: '<div data-notify="container" class="growl alert alert-{0}" role="alert">' +
				'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button>' +
				'<span data-notify="icon"></span> ' +
				'<span data-notify="title">{1}</span> ' +
				'<span data-notify="message">{2}</span>' +
				'<div class="progress" data-notify="progressbar">' +
					'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
				'</div>' +
				'<a href="{3}" target="{4}" data-notify="url"></a>' +
			'</div>',
	PM_exitAnimationTimeout: 0 // PHP Melody custom settings
});
</script>
{/literal}


{if $smarty.const._SEARCHSUGGEST == 1}
{literal}
<script type="text/javascript">
$(document).ready(function () {
	// live search
	$('#appendedInputButton').typeWatch({
		callback: function() {
			$.ajax({
				type: "POST",
				url: MELODYURL2 + "/ajax_search.php",
				data: {
					"queryString": $('#appendedInputButton').val()
				},
				dataType: "html",
				success: function(b) {
					if (b.length > 0) {
						$("#autoSuggestionsList").html(b);

						$(".suggestionsBox").show(function() {

							var $listItems = $('#autoSuggestionsList').find('li');

							$('#appendedInputButton').keydown(function(e) {
								var key = e.keyCode,
									$selected = $listItems.filter('.selected'),
									$current;

								if(key == 38 || key == 40) {
									e.preventDefault();
								}

								if ( key != 40 && key != 38 ) return;

								$listItems.removeClass('selected');

								if ( key == 40 ) // Down key
								{
									if ( ! $selected.length || $selected.is(':last-child') ) {
										$current = $listItems.eq(0);
									}
									else {
										$current = $selected.next();
									}
								}
								else if ( key == 38 ) // Up key
								{
									if ( ! $selected.length || $selected.is(':first-child') ) {
										$current = $listItems.last();
									}
									else {
										$current = $selected.prev();
									}
								}

								$current.addClass('selected');
								$selected_url = $current.find('a').attr('href');

								$selected_id = $current.attr('data-video-id');

								($('#pm-video-id').val($selected_id));
							});
						});
					} else {
						$(".suggestionsBox").hide();
					}
				}
			});
		},
		wait: 400,
		highlight: true,
		captureLength: 3
	});
});
</script>
{/literal}
{/if}

{if $tpl_name == 'video-watch' || $tpl_name == 'article-read'}

{if $show_addthis_widget == '1'}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.socialite.min.js" type="text/javascript"></script>
<script src="//apis.google.com/js/platform.js" async defer></script>
<script type="text/javascript">
$(document).ready(function()
{
	$('#pm-socialite').one('mouseenter', function()
	{
		Socialite.load($(this)[0]);
	});
});
</script>
{/literal}
{/if}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	$('#nav-link-comments-native').click(function(){
		$.cookie('pm_comment_view', 'native', { expires: 180, path: '/' });
	});
	$('#nav-link-comments-facebook').click(function(){
		$.cookie('pm_comment_view', 'facebook', { expires: 180, path: '/' });
	});
	$('#nav-link-comments-disqus').click(function(){
		$.cookie('pm_comment_view', 'disqus', { expires: 180, path: '/' });
	});
});
</script>
{/literal}
{/if}

{if $p == "detail" && $playlist}
{literal}
<script type="text/javascript">
$(document).ready(function () {
	$('.pm-video-playlist').animate({
		scrollTop: $('.pm-video-playlist-playing').offset().top - $('.pm-video-playlist').offset().top + $('.pm-video-playlist').scrollTop()
	});
});
</script>
{/literal}
{/if}

{if $p == "detail"}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.readmore.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {
	var pm_elastic_player = $.cookie('pm_elastic_player');
	if (pm_elastic_player == null) {
		$.cookie('pm_elastic_player', 'normal');
	}
	else if (pm_elastic_player == 'wide') {
		$('#player_extend').find('i').addClass('icon-resize-small');
		$('#secondary').addClass('secondary-wide');
		$('#video-wrapper').addClass('video-wrapper-wide');
		$('.pm-video-head').addClass('pm-video-head-wide');
		$('.entry-title').addClass('ellipsis');
	} else {
		$('#secondary').removeClass('secondary-wide');
		$('#video-wrapper').removeClass('video-wrapper-wide');
		$('.pm-video-head-wide').removeClass('pm-video-head-wide');
		$('.entry-title').removeClass('ellipsis');
	}

	$("#player_extend").click(function() {
		if ($(this).find('i').hasClass("icon-resize-full")) {
			$(this).find('i').removeClass("icon-resize-full").addClass("icon-resize-small");
		} else {
			$(this).find('i').removeClass("icon-resize-small").addClass("icon-resize-full");
		}
		$('#secondary').animate({
			}, 10, function() {
				$('#secondary').toggleClass("secondary-wide");
		});
		$('#video-wrapper').animate({
			}, 150, function() {
				$('#video-wrapper').toggleClass("video-wrapper-wide");
				$('.pm-video-head').toggleClass('pm-video-head-wide');
		});
		if ($.cookie('pm_elastic_player') == 'normal') {
			$.cookie('pm_elastic_player','wide');
			$('#player_extend').find('i').removeClass('icon-resize-full').addClass('icon-resize-small');
		} else {
			$.cookie('pm_elastic_player', 'normal');
			$('#player_extend').find('i').removeClass('icon-resize-small').addClass('icon-resize-full');
		}
		return false;
	});

	$('.pm-video-description').readmore({
		speed: 275,
		maxHeight: 100,
		moreLink: '<p class="show-more-description"><a href="#" class="show-now">'+ pm_lang.show_more +'</a></p>',
		lessLink: '<p class="show-more-description"><a href="#" class="show-now">'+ pm_lang.show_less +'</a></p>',
	});

});
</script>
{/literal}
{/if}
{if $p == "index"}
{literal}
<script type="text/javascript">
$(document).ready(function() {

	$("ul[class^='pm-ul-carousel-videos']").each(
	function() {

		var id = $(this).data("slider-id");
		var slides = $(this).data("slides");
		var video_carousel = $('#pm-carousel_' + id);

		if(slides == null) {
			var slides = 4;
		}

		video_carousel.carouFredSel({
			items				: slides,
			circular			: false,
			direction			: "left",
			height				: null,
			width       		: null,
			infinite			: false,
			responsive			: true,
			prev	: {
				button	: "#pm-slide-prev_" + id,
				key		: "left"
			},
			next	: {
				button	: "#pm-slide-next_" + id,
				key		: "right",
			},
			scroll		: {
				items			: 1,		//	items.visible
				fx				: "scroll",
				easing			: "swing",
				duration		: 500,
				wipe			: true,
				event			: "click",
			},
			auto: false
		});
	});



	$("#pm-ul-wn-videos").carouFredSel({
		items				: 4,
		circular			: false,
		direction			: "left",
		height				: null,
		width       		: null,
		infinite			: false,
		responsive			: true,
		prev	: {
			button	: "#pm-slide-prev",
			key		: "left"
		},
		next	: {
			button	: "#pm-slide-next",
			key		: "right"
		},
	scroll		: {
		items			: null,		//	items.visible
		fx				: "scroll",
		easing			: "swing",
		duration		: 500,
		wipe			: true,
		event			: "click",
	},
	auto: false

	});
});

$(document).ready(function() {
	$("#pm-ul-top-videos").carouFredSel({
	items: 5,
	direction: "up",
	width: "variable",
	height:  "variable",
	circular: false,
	infinite: false,
	scroll: {
		fx: "fade",
		event: "click",
		wipe: true,
		duration: 150
	},
	auto: false,
		prev	: {
			button	: "#pm-slide-top-prev",
			key		: "left"
		},
		next	: {
			button	: "#pm-slide-top-next",
			key		: "right"
		}
	});
});
</script>
{/literal}
{/if}
{if ! $logged_in}
	{literal}
	<script type="text/javascript">
		$('#header-login-form').on('shown', function () {
			$('.hocusfocus').focus();
		});
	</script>
	{/literal}
{/if}
{if $smarty.const._MOD_SOCIAL == '1'}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/waypoints.min.js" type="text/javascript"></script>
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/melody.social.dev.js" type="text/javascript"></script>
{/literal}
{/if}

{if $display_preroll_ad == true}
{literal}
<script src="{/literal}{$smarty.const._URL}{literal}/js/jquery.timer.min.js" type="text/javascript"></script>
<script type="text/javascript">

function timer_pad(number, length) {
	var str = '' + number;
	while (str.length < length) {str = '0' + str;}
	return str;
}

var preroll_timer;
var preroll_player_called = false;
var skippable = {/literal}{if $preroll_ad_data.skip != 1}0{else}1{/if}{literal};
var skippable_timer_current = {/literal}{if $preroll_ad_data.skip_delay_seconds}{$preroll_ad_data.skip_delay_seconds}{else}0{/if}{literal} * 1000;
var preroll_disable_stats = {/literal}{if $preroll_ad_data.disable_stats == 1}1{else}0{/if}{literal};

$(document).ready(function(){
	if (skippable == 1) {
		$('#preroll_skip_btn').hide();
	}

	var preroll_timer_current = {/literal}{$preroll_ad_data.duration}{literal} * 1000;

	preroll_timer = $.timer(function(){

		var seconds = parseInt(preroll_timer_current / 1000);
		var hours = parseInt(seconds / 3600);
		var minutes = parseInt((seconds / 60) % 60);
		var seconds = parseInt(seconds % 60);

		var output = "00";
		if (hours > 0) {
			output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
		} else if (minutes > 0) {
			output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
		} else {
			output = timer_pad(seconds, 1);
		}

		$('.preroll_timeleft').html(output);

		if (preroll_timer_current == 0 && preroll_player_called == false) {

			$.ajax({
				type: "GET",
				url: MELODYURL2 + "/ajax.php",
			dataType: "html",
				data: {
					"p": "video",
					"do": "getplayer",
					"vid": "{/literal}{$preroll_ad_player_uniq_id}{literal}",
					"aid": "{/literal}{$preroll_ad_data.id}{literal}",
					"player": "{/literal}{$preroll_ad_player_page}{literal}",
					"playlist": "{/literal}{$playlist.list_uniq_id}{literal}"
				},
				success: function(data){
					$('#preroll_placeholder').replaceWith(data);
				}
			});

			preroll_player_called = true;
			preroll_timer.stop();
		} else {
			preroll_timer_current -= 1000;
			if(preroll_timer_current < 0) {
				preroll_timer_current = 0;
			}
		}
	}, 1000, true);

	if (skippable == 1) {

		skippable_timer = $.timer(function(){

			var seconds = parseInt(skippable_timer_current / 1000);
			var hours = parseInt(seconds / 3600);
			var minutes = parseInt((seconds / 60) % 60);
			var seconds = parseInt(seconds % 60);

			var output = "00";
			if (hours > 0) {
				output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
			} else if (minutes > 0) {
				output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
			} else {
				output = timer_pad(seconds, 1);
			}

			$('.preroll_skip_timeleft').html(output);

			if (skippable_timer_current == 0 && preroll_player_called == false) {
				$('#preroll_skip_btn').show();
				$('.preroll_skip_countdown').hide();
				skippable_timer.stop();
			} else {
				skippable_timer_current -= 1000;
				if(skippable_timer_current < 0) {
					skippable_timer_current = 0;
				}
			}
		}, 1000, true);

		$('#preroll_skip_btn').click(function(){
			preroll_timer_current = 0;
			skippable_timer_current = 0;

			if (preroll_disable_stats == 0) {
				$.ajax({
					type: "GET",
					url: MELODYURL2 + "/ajax.php",
					dataType: "html",
					data: {
						"p": "stats",
						"do": "skip",
						"aid": "{/literal}{$preroll_ad_data.id}{literal}",
						"at": "{/literal}{$smarty.const._AD_TYPE_PREROLL}{literal}",
					},
					dataType: "html",
					success: function(data){}
				});
			}
			return false;
		});
	}
});
</script>


{/literal}
{/if}
{if $allow_emojis && ($tpl_name == 'article-read' || $tpl_name == 'video-watch' || $tpl_name == 'channel')}
<!-- Emoji provided free by http://emojione.com -->
<script src="{$smarty.const._URL}/js/jquery.textcomplete.min.js"></script>
<script src="{$smarty.const._URL}/js/melody.emoji.js"></script>
<!-- Modal -->
<div class="modal hide fade" id="modalEmojiList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body modal-content">
			</div>
		</div>
	</div>
</div>

{/if}
{if $profile_data.id == $s_user_id}
{literal}
<script src="{/literal}{$smarty.const._URL}/templates/{$template_dir}{literal}/js/jquery.cropit.js" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
	var cropit_avatar_notify = null;
	var cropit_cover_notify = null;
	var cropit_notify_type = 'info';

	// Avatar
	$('.pm-profile-avatar-pic').cropit({
		smallImage: 'allow',
		// width: {/literal}{if $smarty.const.THUMB_W_AVATAR}{$smarty.const.THUMB_W_AVATAR}{else}180{/if}{literal},
		// height: {/literal}{if $smarty.const.THUMB_H_AVATAR}{$smarty.const.THUMB_H_AVATAR}{else}180{/if}{literal},
		width: 120,
		height: 120,
		preview: '.pm-profile-avatar-preview',
		onImageLoading: function(){
			cropit_avatar_notify = $.notify({message: pm_lang.please_wait}, {type: cropit_notify_type});
		},
		onImageLoaded: function() {
			cropit_avatar_notify.close();
		},
		onImageError: function(){
			cropit_avatar_notify.close();
		}
	});

	$('#btn-edit-avatar').click(function() {
	  $('#cropit-avatar-input').click();
	  $('#cropit-avatar-form').css('visibility', 'visible');
	  $('.cropit-image-preview').removeClass('animated fadeIn');
	});

	$('.btn-cancel-avatar').click(function() {
		$('.pm-profile-avatar-pic .cropit-image-preview').removeClass('cropit-image-loaded').addClass('animated fadeIn');
		$('#cropit-avatar-form').css('visibility', 'hidden');
		return false;
	});

	$('form#cropit-avatar-form').submit(function() {

		var image_data = $('.pm-profile-avatar-pic').cropit('export', {
			type: 'image/jpeg',
			quality: .9,
			fillBg: '#333'
		});

		// Move cropped image data to hidden input
		$('.hidden-avatar-data-img').val(image_data);

		$.ajax({
			url: MELODYURL2 + "/ajax.php",
			type: "POST",
			dataType: "json",
			data: $('#cropit-avatar-form').serialize(),
			beforeSend: function(jqXHR, settings) {
				// clean error message container
				//cropit_avatar_notify.close();
				$.notifyClose();
				cropit_avatar_notify = $.notify({message: pm_lang.swfupload_status_uploading}, {type: cropit_notify_type});
			},
		})
		.done(function(data){
			cropit_avatar_notify.close();
			if (data.success) {
				// hide form action buttons
				$('#cropit-avatar-form').css('visibility', 'hidden');

				// reset background with uploaded image
				$('.pm-profile-avatar-pic .cropit-image-preview img').attr('src', data.file_url);

				// stop image movement ability
				$('.pm-profile-avatar-pic .cropit-image-preview').addClass('animated fadeIn');
				// timeout required to allow time for the uploaded image to load before removing the current image obj (and avoid a image-swapping 'glitch')
				setTimeout(function(){
					$('.pm-profile-avatar-pic .cropit-image-preview').removeClass('cropit-image-loaded')
				}, 700);

				// unload selected image to let the user re-select the same image
				$('.pm-profile-avatar-pic input.cropit-image-input')[0].value = null;
			}
			cropit_avatar_notify = $.notify({message: data.msg}, {type: data.alert_type});
		});

		return false;
	});

	var cropit_cover_height = parseInt($('.pm-profile-cover-preview').attr('data-cropit-height'));
	if ( ! cropit_cover_height) {
		cropit_cover_height = 200;
	}

	// Cover
	$('.pm-profile-cover-preview').cropit({
		smallImage: 'allow',
		height: cropit_cover_height,
		onImageLoading: function(){
			cropit_cover_notify = $.notify({message: pm_lang.please_wait}, {type: cropit_notify_type});
		},
		onImageLoaded: function() {
			cropit_cover_notify.close();
		},
		onImageError: function(){
			cropit_cover_notify.close();
		}
	});

	$('#btn-edit-cover').click(function() {
	  $('#cropit-cover-input').click();
	  $('#cropit-cover-form').css('visibility', 'visible');
	  $('.cropit-image-preview').removeClass('animated fadeIn');
	});

	$('.btn-cancel').click(function() {
		$('.pm-profile-cover-preview .cropit-image-preview').removeClass('cropit-image-loaded').addClass('animated fadeIn');
		$('#cropit-cover-form').css('visibility', 'hidden');
		return false;
	});

	$('form#cropit-cover-form').submit(function() {

		var image_data = $('.pm-profile-cover-preview').cropit('export', {
			type: 'image/jpeg',
			quality: .9,
			fillBg: '#333'
		});

		// Move cropped image data to hidden input
		$('.hidden-cover-data-img').val(image_data);

		$.ajax({
			url: MELODYURL2 + "/ajax.php",
			type: "POST",
			dataType: "json",
			data: $('#cropit-cover-form').serialize(),
			beforeSend: function(jqXHR, settings) {
				// clean error message container
				//cropit_cover_notify.close();
				$.notifyClose();
				cropit_cover_notify = $.notify({message: pm_lang.swfupload_status_uploading}, {type: cropit_notify_type});
			},
		})
		.done(function(data){
			cropit_cover_notify.close();
			if (data.success) {
				// hide form action buttons
				$('#cropit-cover-form').css('visibility', 'hidden');

				// reset background with uploaded image
				$('.pm-profile-cover-preview .cropit-image-preview img').attr('src', data.file_url);

				// stop image movement ability
				$('.pm-profile-cover-preview .cropit-image-preview').addClass('animated fadeIn');
				// timeout required to allow time for the uploaded image to load before removing the current image obj (and avoid a image-swapping 'glitch')
				setTimeout(function(){
					$('.pm-profile-cover-preview .cropit-image-preview').removeClass('cropit-image-loaded')
				}, 700);

				// unload selected image to let the user re-select the same image
				$('.pm-profile-cover-preview input.cropit-image-input')[0].value = null;
			}
			cropit_cover_notify = $.notify({message: data.msg}, {type: data.alert_type});
		});

		return false;
	});
});
</script>
{/literal}
{/if}
{if $tpl_name == 'channel' && $smarty.get.view == 'playlists'}
{literal}
<script type="text/javascript">
$(document).ready(function(){
	$('.pm-pro-playlists-btn').trigger('click');
});
</script>
{/literal}
{/if}
{$smarty.const._HTMLCOUNTER}

<script type="text/javascript" src="{$smarty.const._URL}/js/menu.js"></script>

</body>
</html>