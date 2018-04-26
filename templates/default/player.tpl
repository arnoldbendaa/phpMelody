{if $video_data.restricted == '1' && ! $logged_in}
<div class="restricted-video border-radius4">
	<h2>{$lang.restricted_sorry}</h2>
	<p>{$lang.restricted_register}</p>
	<div class="restricted-login">
	{include file='user-auth-login-form.tpl'}
	</div>
</div>
{else}
{if $page == "detail"}
	{if $total_video_ads > 0}
		{literal} 
		<script type="text/javascript">
		var embed_code = {/literal}'{$video_data.embed_code}'{literal};
		</script>
		<link href="{/literal}{$smarty.const._URL2}{literal}/players/video-js/video-js.min.css" rel="stylesheet">
		<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/video.js"></script>
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs.persistvolume.js"></script>
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs.adz.js"></script>
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs-preroll.min.js"></script>
		
		<div id="embed_Playerholder">
		 <div id="{/literal}{$video_ad_hash}{literal}-playerholder">
		 </div>
		</div>
		<video src="" id="video-js" class="video-js vjs-default-skin" poster="" preload="auto" data-setup='{ "techOrder": ["flash","html5"], "controls": true, "autoplay": true }' width="100%" height="{/literal}{$smarty.const._PLAYER_H}{literal}"></video>
		<script type="text/javascript">
				var video = videojs('video-js').ready(function(){
					var player = this;
					
					player.on('loadedmetadata', function() {
						$('.vjs-big-play-button').addClass('vjs-pm-show-big-play');
						$('.vjs-control-bar').css({"display": "block"});
					});

					player.ads();
					player.preroll({
						src:"{/literal}{$smarty.const._URL2}/videoads.php?h={$video_ad_hash}{literal}", 
						href:"{/literal}{$smarty.const._URL2}/videoads.php?h={$video_ad_hash}&tz=t{literal}",
						target:"{/literal}{$video_ad_target}{literal}", 
						skipTime: 5});

					player.src([
						{
							src: "{/literal}{$smarty.const._URL2}/videoads.php?h={$video_ad_hash}{literal}",
							type: "video/mp4"
						}
					]);
					player.persistvolume({
						namespace: "Melody-vjs-Volume"
					});

					player.on('ended', function() {
						player.dispose(); 
						ajax_request("video", "do=getplayer&vid={/literal}{$video_data.uniq_id}{literal}&aid=&player={/literal}{$page}{literal}&playlist={/literal}{$playlist.list_uniq_id}{literal}", "#embed_Playerholder", "html");
					});
				});
		</script>
		{/literal}
	{else}
	
	
		{if $video_data.video_player == "flvplayer"}
			{literal}
				<div id="Playerholder">
				<noscript>
					{$lang.enable_javascript}
				</noscript>
				<em>{$lang.please_wait}</em>
				</div>
				<script type="text/javascript">
				var clips = "[ { name: 'ClickToPlay', overlayId: 'play', url: '{/literal}{$video_data.preview_image}{literal}' },";
				
				clips = clips + " { name: '{/literal}{$video_data.uniq_id}{literal}', url: '{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}', {/literal}{if $video_data.source_id == $_sources.mp3.source_id}type: 'mp3'{/if}{literal} } ]";
				
				var flashvars = {
					config: "{ playList: " + clips + ", useSmoothing: true, baseURL: '', autoPlay: {/literal}{if $playlist}true{else}{$video_data.video_player_autoplay}{/if}{literal}, autoBuffering: {/literal}{$smarty.const._AUTOBUFF}{literal}, startingBufferLength: 2, bufferLength: 5, loop: false,hideControls: false,initialScale: 'fit', showPlayListButtons: false, useNativeFullScreen: true,controlBarGloss: 'high', controlsOverVideo: 'locked', watermarkUrl: '{/literal}{$smarty.const._WATERMARKURL}{literal}', showWatermark: '{/literal}{$smarty.const._WATERMARKSHOW}{literal}', watermarkLinkUrl: '{/literal}{$smarty.const._WATERMARKLINK}{literal}', controlBarBackgroundColor: '0x{/literal}{$smarty.const._BGCOLOR}{literal}', progressBarColor1: '0xFFFFFF', progressBarColor2: '0x000000', timeDisplayFontColor: '0x{/literal}{$smarty.const._TIMECOLOR}{literal}', noVideoClip: { url: '{/literal}{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/notfound.jpg{literal}' }, menuItems: [ false, false, true, true, true, false, false ], showStopButton: true, useHwScaling: false, showOnLoadBegin: true }"
				};
				
				var params = {
					allowfullscreen: "true",
					allowScriptAccess: "always",
					wmode: "opaque"
				};
				var attributes = {};
				swfobject.embedSWF("{/literal}{$smarty.const._URL2}{literal}/players/flowplayer2/flowplayer.swf", "Playerholder", "{/literal}{$smarty.const._PLAYER_W}{literal}", "{/literal}{$smarty.const._PLAYER_H}{literal}", "9.0.115", false, flashvars, params, attributes);
	
				function onStreamNotFound(a){
				   {/literal}{if $playlist}{literal}
					window.location = "{/literal}{$playlist_next_url}{literal}";
					{/literal}{/if}{literal}
				}
				function onClipDone(clip) {
					if (clip.name != "Advertisment") {
						{/literal}{if $playlist}{literal}
						window.location = "{/literal}{$playlist_next_url}{literal}";
						{/literal}{else}{literal}
							if (pm_video_data.autoplay_next && pm_video_data.autoplay_next_url != "") {
								window.location = pm_video_data.autoplay_next_url;
							}
						{/literal}{/if}{literal}
					}
				}
				</script>
			{/literal}
		{elseif $video_data.video_player == "jwplayer"}
					<div id="Playerholder">
				<noscript>
					{$lang.enable_javascript}
				</noscript>
				<em>{$lang.please_wait}</em>
					</div>
					{literal}
					<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/jwplayer.js"></script>
					<script type='text/javascript'>
						var flashvars = {
							"flashplayer": "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/jwplayer.swf",
							"playlist": [{
							{/literal}
							{if $video_data.source_id == 0}
								file: '{$video_data.jw_flashvars.file}',
								image: '{$video_data.preview_image}',
								streamer: '{$video_data.jw_flashvars.streamer}',
								{if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
								{if $video_data.jw_flashvars.startparam != ''} 'http.startparam': '{$video_data.jw_flashvars.startparam}',{/if}
								{if $video_data.jw_flashvars.loadbalance != ''} 'rtmp.loadbalance': '{$video_data.jw_flashvars.loadbalance}',{/if}
								{if $video_data.jw_flashvars.subscribe != ''} 'rtmp.subscribe': '{$video_data.jw_flashvars.subscribe}',{/if}
							{elseif $video_data.source_id == $_sources.youtube.source_id}
								{literal}
								file: '{/literal}{$video_data.direct}{literal}',
								image: '{/literal}{$video_data.preview_image}{literal}',
							{/literal}
						{elseif $video_data.source_id == $_sources.gfycat.source_id}
							{literal}
							file: '{/literal}{$video_data.url_flv}{literal}',
								{/literal}
							{else}
								{literal}
								file: '{/literal}{$smarty.const._URL2}{literal}/videos.php?vid={/literal}{$video_data.uniq_id}{literal}',
								{/literal}{if $video_data.source_id == $_sources.mp3.source_id}{literal}
								type: 'audio',
								{/literal}{else}{literal}
								type: 'video',
								{/literal}{/if}{literal}
								image: '{/literal}{$video_data.preview_image}{literal}',
								{/literal}
							{/if}
							{literal}
							}],
							"controlbar": {
								"position": "bottom",
							},
							'wmode': 'transparent',
							'allowfullscreen': 'true',
							'allowscriptaccess': 'always',
							'allownetworking': 'all',
							'name': '{/literal}{$video_data.uniq_id}{literal}',
							'id': '{/literal}{$video_data.uniq_id}{literal}',
							'width': "100%",
							{/literal}{if $playlist}{literal}
							'height': "401px",
							'autostart': "true", 
							{/literal}{else}{literal}
							'height': "{/literal}{$smarty.const._PLAYER_H}{literal}",
							'autostart': "{/literal}{$video_data.video_player_autoplay}{literal}", 
							{/literal}{/if}{literal}
							'enablejs': "true",
							'backcolor': "{/literal}{$smarty.const._BGCOLOR}{literal}",
							'frontcolor': "{/literal}{$smarty.const._TIMECOLOR}{literal}",
							'screencolor': "000000",
							'repeat': "false",
							'logo': "{/literal}{$smarty.const._WATERMARKURL}{literal}",
							'linktarget': "_blank",
							'link': "{/literal}{$smarty.const._WATERMARKLINK}{literal}",
							'image': "{/literal}{$video_data.preview_image}{literal}", // XTRA
							'bufferlength': "5",
							'shownavigation':"true",
							'skin': "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/skins/{/literal}{$smarty.const._JWSKIN}{literal}",
							'stretching': "fill",
							"plugins": {
								'captions': {
									{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
									file: "{/literal}{$video_subtitles.filename}{literal}",
									{/literal}{/foreach}{literal}
								},
							{/literal}{if !$playlist}{literal}
									'{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/plugins/drelated.swf': {
										'dxmlpath': '{/literal}{$smarty.const._URL2}{literal}/relatedclips.php?vid={/literal}{$video_data.uniq_id}{literal}',
										'dposition': 'center', // center, bottom, top
										'dskin': '{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/skins/grayskin2.swf',
										'dtarget': '_self' // where to open the related videos when clicked on   			        			
									},
							{/literal}{/if}{literal}
									//'timeslidertooltipplugin-3': {},
									'sharing-3': {
										'link': '{/literal}{$video_data.video_href}{literal}',
										'code': encodeURIComponent('{/literal}{$embedcode}{literal}')
									}
							},
							"events": {
									onComplete: function() {
										{/literal}{if $playlist}{literal}
										window.location = "{/literal}{$playlist_next_url}{literal}";
										{/literal}{else}
											{literal}
											if (pm_video_data.autoplay_next && pm_video_data.autoplay_next_url != "") {
												window.location = pm_video_data.autoplay_next_url;
											}
											{/literal}
										{/if}{literal}
									},
									onError: function(object) { 
										ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
										{/literal}{if $playlist}{literal}
										window.location = "{/literal}{$playlist_next_url}{literal}";
										{/literal}{/if}{literal}
									}
								}
				};
			{/literal}{$jw_flashvars_override}{literal}
			jwplayer("Playerholder").setup(flashvars);
			</script>
			{/literal}	
		
	  {elseif $video_data.video_player == "jwplayer6"}
			<div id="Playerholder">
		<noscript>
			{$lang.enable_javascript}
		</noscript>
		<em>{$lang.please_wait}</em>
			</div>
			{literal}
			<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer6/jwplayer.js"></script>
			<script type="text/javascript">jwplayer.key="{/literal}{$jwplayerkey}{literal}";</script>
			<script type="text/javascript">
				var flashvars = {
						{/literal}
							{if $video_data.source_id == 0}
								file: '{$video_data.jw_flashvars.file}',
								streamer: '{$video_data.jw_flashvars.streamer}',
								{literal}rtmp: {{/literal}
								{if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
								{if $video_data.jw_flashvars.startparam != ''} startparam: '{$video_data.jw_flashvars.startparam}',{/if}
								{if $video_data.jw_flashvars.loadbalance != ''} loadbalance: {$video_data.jw_flashvars.loadbalance},{/if}
								{if $video_data.jw_flashvars.subscribe != ''} subscribe: {$video_data.jw_flashvars.subscribe},{/if}
								{if $video_data.jw_flashvars.securetoken != ''} securetoken: "{$video_data.jw_flashvars.securetoken}",{/if}
								},
							{elseif $video_data.source_id == $_sources.localhost.source_id}
								{literal}
								file: '{/literal}{$video_data.url_flv}{literal}',
							//image: '{/literal}{$video_data.preview_image}{literal}',
								{/literal}
							{elseif $video_data.source_id == $_sources.youtube.source_id}
								{literal}
								file: '{/literal}{$video_data.direct}{literal}',
								//image: '//img.youtube.com/vi/{/literal}{$video_data.yt_id}{literal}/hqdefault.jpg',
								{/literal}
							{elseif $video_data.source_id == $_sources.mp3.source_id}
								{literal}
								file: '{/literal}{$video_data.url_flv}{literal}',
								type: 'mp3',
								//image: '{/literal}{$video_data.preview_image}',
							{else}		
								{literal}
								file: '{/literal}{$video_data.url_flv}{literal}',
								//image: '{/literal}{$video_data.preview_image}',
							{/if}
							{literal}
							flashplayer: "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer6/jwplayer.flash.swf",                        
							primary: "html5",
							width: "100%",
							{/literal}{if $playlist}{literal}
							height: "401",
							autostart: true, 
							{/literal}{else}{literal}
							height: "{/literal}{$smarty.const._PLAYER_H}{literal}",
							autostart: "{/literal}{$video_data.video_player_autoplay}{literal}", 
							{/literal}{/if}{literal}					
							image: '{/literal}{$video_data.preview_image}{literal}',
							stretching: "fill",
							events: {
								onComplete: function() {
									{/literal}{if $playlist}{literal}
									window.location = "{/literal}{$playlist_next_url}{literal}";
									{/literal}{else}{literal}
										if (pm_video_data.autoplay_next && pm_video_data.autoplay_next_url != "") {
											window.location = pm_video_data.autoplay_next_url;
										}
									{/literal}{/if}{literal}
								},
								onError: function(object) { 
									ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
									{/literal}{if $playlist}{literal}
									window.location = "{/literal}{$playlist_next_url}{literal}";
									{/literal}{/if}{literal}
								}
							},
							logo: {
								file: '{/literal}{$smarty.const._WATERMARKURL}{literal}',
								link: '{/literal}{$smarty.const._WATERMARKLINK}{literal}',
							},
							"tracks": [
							{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
								{ file: "{/literal}{$video_subtitles.filename}{literal}", label: "{/literal}{$video_subtitles.language}{literal}", kind: "subtitles" },
							{/literal}{/foreach}{literal}
							]
							};
							{/literal}{$jw_flashvars_override}{literal}
				jwplayer("Playerholder").setup(flashvars);
			</script>
			{/literal}
	{elseif $video_data.video_player == "jwplayer7"}
		<div id="Playerholder">
			<noscript>
				{$lang.enable_javascript}
			</noscript>
			<em>{$lang.please_wait}</em>
		</div>
		{literal}
		<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer7/jwplayer.js"></script>
		<script type="text/javascript">jwplayer.key="{/literal}{$jwplayer7key}{literal}";</script>
		<script type="text/javascript">
			var flashvars = {
					{/literal}
						{if $video_data.source_id == 0}
							file: '{$video_data.jw_flashvars.file}',
							image: '{$video_data.preview_image}',
							streamer: '{$video_data.jw_flashvars.streamer}',
							{literal}rtmp: {{/literal}
							{if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
							{if $video_data.jw_flashvars.startparam != ''} startparam: '{$video_data.jw_flashvars.startparam}',{/if}
							{if $video_data.jw_flashvars.loadbalance != ''} loadbalance: {$video_data.jw_flashvars.loadbalance},{/if}
							{if $video_data.jw_flashvars.subscribe != ''} subscribe: {$video_data.jw_flashvars.subscribe},{/if}
							{if $video_data.jw_flashvars.securetoken != ''} securetoken: "{$video_data.jw_flashvars.securetoken}",{/if}
							},
						{elseif $video_data.source_id == $_sources.localhost.source_id}
							{literal}
							file: '{/literal}{$video_data.url_flv}{literal}',
							image: '{/literal}{$video_data.preview_image}{literal}',
							{/literal}
						{elseif $video_data.source_id == $_sources.youtube.source_id}
							{literal}
							file: '{/literal}{$video_data.direct}{literal}',
							//image: '//img.youtube.com/vi/{/literal}{$video_data.yt_id}{literal}/hqdefault.jpg',
							{/literal}
						{elseif $video_data.source_id == $_sources.mp3.source_id}
							{literal}
							file: '{/literal}{$video_data.url_flv}{literal}',
							type: 'mp3',
							//image: '{/literal}{$video_data.preview_image}',
						{else}		
							{literal}
							file: '{/literal}{$video_data.url_flv}{literal}',
							//image: '{/literal}{$video_data.preview_image}',
						{/if}
						{literal}
						flashplayer: "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer7/jwplayer.flash.swf",                        
						primary: "html5",
						width: "100%",
						{/literal}{if $playlist}{literal}
						height: "401",
						autostart: true, 
						{/literal}{else}{literal}
						height: "{/literal}{$smarty.const._PLAYER_H}{literal}",
						autostart: "{/literal}{$video_data.video_player_autoplay}{literal}", 
						{/literal}{/if}{literal}					
						image: '{/literal}{$video_data.preview_image}{literal}',
						stretching: "fill",
						events: {
							onComplete: function() {
								{/literal}{if $playlist}{literal}
								window.location = "{/literal}{$playlist_next_url}{literal}";
								{/literal}{else}{literal}
									if (pm_video_data.autoplay_next && pm_video_data.autoplay_next_url != "") {
										window.location = pm_video_data.autoplay_next_url;
									}
								{/literal}{/if}{literal}
							},
							onError: function(object) { 
								ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
								{/literal}{if $playlist}{literal}
								window.location = "{/literal}{$playlist_next_url}{literal}";
								{/literal}{/if}{literal}
							}
						},
						logo: {
							file: '{/literal}{$smarty.const._WATERMARKURL}{literal}',
							link: '{/literal}{$smarty.const._WATERMARKLINK}{literal}',
						},
						"tracks": [
						{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
							{ file: "{/literal}{$video_subtitles.filename}{literal}", label: "{/literal}{$video_subtitles.language}{literal}", kind: "subtitles" },
						{/literal}{/foreach}{literal}
						]
						};
						{/literal}{$jw_flashvars_override}{literal}
			jwplayer("Playerholder").setup(flashvars);
		</script>
		{/literal}
		{elseif $video_data.video_player == "videojs"}
		{literal}
	
			<link href="{/literal}{$smarty.const._URL2}{literal}/players/video-js/video-js.min.css" rel="stylesheet">
			<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/video.js"></script>
		{/literal}{if $video_data.source_id == $_sources.youtube.source_id}{literal}
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/youtube.js"></script>
		{/literal}{/if}{literal}
			<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs.persistvolume.js"></script>
		{/literal}{if $video_data.source_id == $_sources.dailymotion.source_id}{literal}
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/dailymotion.js"></script>
		<script type="text/javascript">
			player.options.flash.swf = "{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/video-js.swf";
		</script>
			{/literal}
		{/if}

			<script src="{$smarty.const._URL2}/players/video-js/plugins/videojs.socialShare.js"></script>

			{if $smarty.const._WATERMARKURL != ''}
			{literal}
			<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs.logobrand.js"></script>
			{/literal}
			{/if}
			{literal}
	
			<div id="Playerholder">
		<video src="" id="video-js" class="video-js vjs-default-skin" poster="{/literal}{if $video_data.source_id != $_sources.youtube.source_id}{$video_data.preview_image}{/if}{literal}" preload="auto" data-setup='{ "techOrder": [{/literal}{if $video_data.source_id == $_sources.youtube.source_id}"youtube",{/if}{if $video_data.source_id == $_sources.dailymotion.source_id}"dailymotion",{/if}{literal}"html5","flash"], "controls": true, "autoplay": {/literal}{$video_data.video_player_autoplay}{literal} }' width="100%" height="{/literal}{$smarty.const._PLAYER_H}{literal}">
			{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
			<track kind="captions" src="{/literal}{$video_subtitles.filename}{literal}" srclang="{/literal}{$video_subtitles.language_tag|lower}{literal}" label="{/literal}{$video_subtitles.language}{literal}">
			{/literal}{/foreach}{literal}
			</video>
	
			<script type="text/javascript">
			var video = videojs('video-js').ready(function(){
				var player = this;
				
				player.on('loadedmetadata', function() {
					$('.vjs-big-play-button').addClass('vjs-pm-show-big-play');
					$('.vjs-control-bar').css({"display": "block"});
				});
				
				player.on('error', function(){
					var MediaError = player.error();
					
					if (MediaError.code == 4) {
						ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ MediaError.message, "", "", false);
					}
					if (MediaError.code == 101 || MediaError.code == 150) {
						ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message=Playback disabled by owner", "", "", false);
					}
				});
	
	
				{/literal}{if $smarty.const._WATERMARKURL != ''}{literal}
				player.logobrand({
					image: "{/literal}{$smarty.const._WATERMARKURL}{literal}",
					destination: "{/literal}{$smarty.const._WATERMARKLINK}{literal}"
				});
				{/literal}{/if}
	
				{if $video_data.source_id == 0}
				{literal}
				player.src([
					{
						src: "{/literal}{$video_data.jw_flashvars.streamer}&mp4:{$video_data.jw_flashvars.file}{literal}", 
						type: "rtmp/mp4"
					}
				]);
				{/literal}
				{/if}
	
				{if $video_data.source_id == $_sources.localhost.source_id || $video_data.source_id == $_sources.other.source_id}
				{literal}
				player.src([
					{
						src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
						type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "video/flv" {/if}{literal}
					}
				]);
				{/literal}
				{/if}
	
				{if $video_data.source_id == $_sources.youtube.source_id}
				{literal}
				player.src([{
					src: "{/literal}{$video_data.direct}{literal}",
					type: "video/youtube"
				}]);
				{/literal}
				{/if}

				{if $video_data.source_id == $_sources.dailymotion.source_id}
				{literal}
				player.src([{
					src: "{/literal}{$video_data.direct}{literal}",
					type: "video/dailymotion"
				}]);
				{/literal}
				{/if}

				{if $video_data.source_id == $_sources.vimeo.source_id}
				{literal}
				player.src([
					{
						src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
						type: "video/mp4"
					}
				]);
				{/literal}
				{/if}
	
				{if $video_data.source_id == $_sources.mp3.source_id}
				{literal}
				player.src([
					{
						src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
						type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "audio/mp3" {/if}{literal}
					}
				]);
				{/literal}
				{/if}
				{if $video_data.source_id == $_sources.imgur.source_id || $video_data.source_id == $_sources.gfycat.source_id}
				{literal}
				player.src([
					{
						src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
						type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "video/mp4" {/if}{literal}
					}
				]);
				{/literal}
				{/if}


				{if $video_data.source_id == $_sources.vbox7.source_id}
				{literal}
				player.src([
					{
						src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
						type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "video/mp4" {/if}{literal}
					}
				]);
				{/literal}
				{/if}




				{literal}
				player.on('waiting', function() {
					$('.vjs-loading-spinner').removeClass('vjs-hidden');
				});

				player.persistvolume({
					namespace: "Melody-vjs-Volume"
				});

				player.socialShare({
					twitter: {}
				});

	
				{/literal}{if $playlist}{literal}
				player.play(); //autoplayback
				{/literal}{/if}{literal}
				player.on('ended', function() {
					{/literal}{if $playlist}{literal}
					window.location = "{/literal}{$playlist_next_url}{literal}";
					{/literal}{else}{literal}
						if (pm_video_data.autoplay_next && pm_video_data.autoplay_next_url != "") {
							window.location = pm_video_data.autoplay_next_url;
						}
					{/literal}{/if}{literal}
				});
				{/literal}
			});
			</script>
			</div>
	
	  {elseif $video_data.video_player == "embed"}
		<div id="Playerholder">
			{$video_data.embed_code}
		</div>
	   {/if}
	{/if}
{/if}


{if $page == "index"}
  {if $video_data.video_player == "flvplayer"}
		{literal}
		<div id="Playerholder">
			<noscript>
				{$lang.enable_javascript}
			</noscript>
			<em>{$lang.please_wait}</em>
		</div>
	
		<script type="text/javascript">

		var clips = "[ { name: 'ClickToPlay', overlayId: 'play', url: '{/literal}{$video_data.preview_image}{literal}' }, { name: '', url: '{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}', {/literal}{if $video_data.source_id == $_sources.mp3.source_id}type: 'mp3'{/if}{literal} } ]";
		
		var flashvars = {
			config: "{ playList: " + clips + ", showPlayList: true,useSmoothing: true, baseURL: '', autoPlay: {/literal}{$video_data.video_player_autoplay}{literal}, autoBuffering: {/literal}{$smarty.const._AUTOBUFF}{literal}, startingBufferLength: 2, bufferLength: 5, loop: false,hideControls: false,initialScale: 'fit', showPlayListButtons: false, useNativeFullScreen: true,controlBarGloss: 'high', controlsOverVideo: 'ease', controlBarBackgroundColor: '0x{/literal}{$smarty.const._BGCOLOR}{literal}', progressBarColor1: '0xFFFFFF', progressBarColor2: '0x000000', timeDisplayFontColor: '0x{/literal}{$smarty.const._TIMECOLOR}{literal}', noVideoClip: { url: '{/literal}{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/notfound.jpg{literal}' },	useHwScaling: false,showMenu: false }"
		};
		var params = {
			allowfullscreen: "true",
			allowScriptAccess: "always",
			wmode: "transparent"
		};
		var attributes = {};
		swfobject.embedSWF("{/literal}{$smarty.const._URL2}{literal}/players/flowplayer2/flowplayer.swf", "Playerholder", "{/literal}{$smarty.const._PLAYER_W_INDEX}{literal}", "{/literal}{$smarty.const._PLAYER_H_INDEX}{literal}", "9.0.115", false, flashvars, params, attributes);
		
		</script>
		{/literal}
  
  {elseif $video_data.video_player == "jwplayer"}
	   <div id="Playerholder">
			<noscript>
				{$lang.enable_javascript}
			</noscript>
			<em>{$lang.please_wait}</em>
		</div>
		{literal}
		<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/jwplayer.js"></script>
		<script type='text/javascript'>
			var flashvars = {
				"flashplayer": "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/jwplayer.swf",
				"playlist": [{
				{/literal}
				{if $video_data.source_id == 0}
					file: '{$video_data.jw_flashvars.file}',
					streamer: '{$video_data.jw_flashvars.streamer}',
					{if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
					{if $video_data.jw_flashvars.startparam != ''} 'http.startparam': '{$video_data.jw_flashvars.startparam}',{/if}
					{if $video_data.jw_flashvars.loadbalance != ''} 'rtmp.loadbalance': '{$video_data.jw_flashvars.loadbalance}',{/if}
					{if $video_data.jw_flashvars.subscribe != ''} 'rtmp.subscribe': '{$video_data.jw_flashvars.subscribe}',{/if}
				{elseif $video_data.source_id == $_sources.youtube.source_id}
					{literal}
					file: '{/literal}{$video_data.direct}{literal}',
					image: '{/literal}{$video_data.preview_image}{literal}',
					{/literal}
				{elseif $video_data.source_id == $_sources.gfycat.source_id}
					{literal}
					file: '{/literal}{$video_data.url_flv}{literal}',
					{/literal}
				{else}		
					{literal}
					file: '{/literal}{$smarty.const._URL2}{literal}/videos.php?vid={/literal}{$video_data.uniq_id}{literal}',
					{/literal}{if $video_data.source_id == $_sources.mp3.source_id}{literal}
					type: 'audio',
					{/literal}{else}{literal}
					type: 'video',
					{/literal}{/if}{literal}
					image: '{/literal}{$video_data.preview_image}{literal}',
					{/literal}
				{/if}
				{literal}
				}],
				"controlbar": {
					"position": "bottom",
				},
				'wmode': 'transparent',
				'allowfullscreen': 'true',
				'allowscriptaccess': 'always',
				'allownetworking': 'all',
				'name': '{/literal}{$video_data.uniq_id}{literal}',
				'id': '{/literal}{$video_data.uniq_id}{literal}',
				'width': "100%",
				{/literal}{if $playlist}{literal}
				'height': "401px",
				'autostart': "true", 
				{/literal}{else}{literal}
				'height': "{/literal}{$smarty.const._PLAYER_H_INDEX}{literal}",
				'autostart': "{/literal}{$video_data.video_player_autoplay}{literal}", 
				{/literal}{/if}{literal}
				'enablejs': "true",
				'backcolor': "{/literal}{$smarty.const._BGCOLOR}{literal}",
				'frontcolor': "{/literal}{$smarty.const._TIMECOLOR}{literal}",
				'screencolor': "000000",
				'repeat': "false",
				'logo': "{/literal}{$smarty.const._WATERMARKURL}{literal}",
				'linktarget': "_blank",
				'link': "{/literal}{$smarty.const._WATERMARKLINK}{literal}",
				'image': "{/literal}{$video_data.preview_image}{literal}", // XTRA
				'bufferlength': "5",
				'shownavigation':"true",
				'skin': "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer5/skins/{/literal}{$smarty.const._JWSKIN}{literal}",
				'stretching': "fill",
					"plugins": {
						'captions': {
							{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
							file: "{/literal}{$video_subtitles.filename}{literal}",
							{/literal}{/foreach}{literal}
						},
						'sharing-3': {
							'link': '{/literal}{$video_data.video_href}{literal}',
							'code': encodeURIComponent('{/literal}{$embedcode}{literal}')
						},
						// 'timeslidertooltipplugin-3': {},
					},
					"events": {
							onError: function(object) { 
								ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
							}
					}
			};
		{/literal}{$jw_flashvars_override}{literal}
		jwplayer("Playerholder").setup(flashvars);
		</script>
		{/literal}	

  {elseif $video_data.video_player == "jwplayer6"}
		<div id="Playerholder">
			<noscript>
				{$lang.enable_javascript}
			</noscript>
			<em>{$lang.please_wait}</em>
		</div>
		{literal}
		<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer6/jwplayer.js"></script>
		<script type="text/javascript">jwplayer.key="{/literal}{$jwplayerkey}{literal}";</script>
		<script type="text/javascript">
		var flashvars = {
				{/literal}
					{if $video_data.source_id == 0}
						file: '{$video_data.jw_flashvars.file}',
						streamer: '{$video_data.jw_flashvars.streamer}',
						{literal}rtmp: {{/literal}
						{if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
						{if $video_data.jw_flashvars.startparam != ''} startparam: '{$video_data.jw_flashvars.startparam}',{/if}
						{if $video_data.jw_flashvars.loadbalance != ''} loadbalance: {$video_data.jw_flashvars.loadbalance},{/if}
						{if $video_data.jw_flashvars.subscribe != ''} subscribe: {$video_data.jw_flashvars.subscribe},{/if}
						{if $video_data.jw_flashvars.securetoken != ''} securetoken: "{$video_data.jw_flashvars.securetoken}",{/if}
						},
					{elseif $video_data.source_id == $_sources.localhost.source_id}
						{literal}
						file: '{/literal}{$video_data.url_flv}{literal}',
						image: '{/literal}{$video_data.preview_image}{literal}',
						{/literal}
					{elseif $video_data.source_id == $_sources.youtube.source_id}
						{literal}
						file: '{/literal}{$video_data.direct}{literal}',
						//image: '//img.youtube.com/vi/{/literal}{$video_data.yt_id}{literal}/hqdefault.jpg',
						{/literal}
					{elseif $video_data.source_id == $_sources.mp3.source_id}
						{literal}
						file: '{/literal}{$video_data.url_flv}{literal}',
						type: 'mp3',
						//image: '{/literal}{$video_data.preview_image}',
					{else}		
						{literal}
						file: '{/literal}{$video_data.url_flv}{literal}',
						//image: '{/literal}{$video_data.preview_image}',
					{/if}
					{literal}
					flashplayer: "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer6/jwplayer.flash.swf",                    
					primary: "html5",
					width: "100%",
					height: "{/literal}{$smarty.const._PLAYER_H_INDEX}{literal}",
					image: '{/literal}{$video_data.preview_image}{literal}',
					stretching: "fill",
					logo: {
						file: '{/literal}{$smarty.const._WATERMARKURL}{literal}',
						link: '{/literal}{$smarty.const._WATERMARKLINK}{literal}',
						},
					events: {
						onError: function(object) { 
						   ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
						}
					},
					autostart: '{/literal}{$video_data.video_player_autoplay}{literal}',
					"tracks": [
					{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
						{ file: "{/literal}{$video_subtitles.filename}{literal}", label: "{/literal}{$video_subtitles.language}{literal}", kind: "subtitles" },
					{/literal}{/foreach}{literal}
					]
				};
			{/literal}{$jw_flashvars_override}{literal}
			jwplayer("Playerholder").setup(flashvars);
		</script>
		{/literal}

{elseif $video_data.video_player == "jwplayer7"}
		<div id="Playerholder">
			<noscript>
				{$lang.enable_javascript}
			</noscript>
			<em>{$lang.please_wait}</em>
		</div>
		{literal}
		<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer7/jwplayer.js"></script>
		<script type="text/javascript">jwplayer.key="{/literal}{$jwplayer7key}{literal}";</script>
		<script type="text/javascript">
		var flashvars = {
				{/literal}
					{if $video_data.source_id == 0}
						file: '{$video_data.jw_flashvars.file}',
						image: '{$video_data.preview_image}',
						streamer: '{$video_data.jw_flashvars.streamer}',
						{literal}rtmp: {{/literal}
						{if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
						{if $video_data.jw_flashvars.startparam != ''} startparam: '{$video_data.jw_flashvars.startparam}',{/if}
						{if $video_data.jw_flashvars.loadbalance != ''} loadbalance: {$video_data.jw_flashvars.loadbalance},{/if}
						{if $video_data.jw_flashvars.subscribe != ''} subscribe: {$video_data.jw_flashvars.subscribe},{/if}
						{if $video_data.jw_flashvars.securetoken != ''} securetoken: "{$video_data.jw_flashvars.securetoken}",{/if}
						},
					{elseif $video_data.source_id == $_sources.localhost.source_id}
						{literal}
						file: '{/literal}{$video_data.url_flv}{literal}',
						image: '{/literal}{$video_data.preview_image}{literal}',
						{/literal}
					{elseif $video_data.source_id == $_sources.youtube.source_id}
						{literal}
						file: '{/literal}{$video_data.direct}{literal}',
						//image: '//img.youtube.com/vi/{/literal}{$video_data.yt_id}{literal}/hqdefault.jpg',
						{/literal}
					{elseif $video_data.source_id == $_sources.mp3.source_id}
						{literal}
						file: '{/literal}{$video_data.url_flv}{literal}',
						type: 'mp3',
						//image: '{/literal}{$video_data.preview_image}{literal}',
						{/literal}
					{else}		
						{literal}
						file: '{/literal}{$video_data.url_flv}{literal}',
						//image: '{/literal}{$video_data.preview_image}{literal}',
						{/literal}
					{/if}
					{literal}
					flashplayer: "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer7/jwplayer.flash.swf",                    
					primary: "html5",
					width: "100%",
					height: "{/literal}{$smarty.const._PLAYER_H_INDEX}{literal}",
					image: '{/literal}{$video_data.preview_image}{literal}',
					stretching: "fill",
					logo: {
						file: '{/literal}{$smarty.const._WATERMARKURL}{literal}',
						link: '{/literal}{$smarty.const._WATERMARKLINK}{literal}',
						},
					events: {
						onError: function(object) { 
						   ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
						}
					},
					autostart: '{/literal}{$video_data.video_player_autoplay}{literal}',
					"tracks": [
					{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
						{ file: "{/literal}{$video_subtitles.filename}{literal}", label: "{/literal}{$video_subtitles.language}{literal}", kind: "subtitles" },
					{/literal}{/foreach}{literal}
					]
				};
			{/literal}{$jw_flashvars_override}{literal}
			jwplayer("Playerholder").setup(flashvars);
		</script>
		{/literal}

		{elseif $video_data.video_player == "videojs"}
		{literal}
		<link href="{/literal}{$smarty.const._URL2}{literal}/players/video-js/video-js.min.css" rel="stylesheet">
		<script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/video.js"></script>
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs.persistvolume.js"></script>

		{/literal}{if $video_data.source_id == $_sources.youtube.source_id}{literal}
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/youtube.js"></script>
		{/literal}
		{/if}
		{if $video_data.source_id == $_sources.dailymotion.source_id}{literal}
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/dailymotion.js"></script>
		<script type="text/javascript">
			player.options.flash.swf = "{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/video-js.swf";
		</script>
		{/literal}
		{/if}
		
		<script src="{$smarty.const._URL2}/players/video-js/plugins/videojs.socialShare.js"></script>
		
		{if $smarty.const._WATERMARKURL != ''}
		{literal}
		<script src="{/literal}{$smarty.const._URL2}{literal}/players/video-js/plugins/videojs.logobrand.js"></script>
		{/literal}
		{/if}
		{literal}

		<div id="Playerholder">
		<video src="" id="video-js" class="video-js vjs-default-skin" poster="{/literal}{if $video_data.source_id != $_sources.youtube.source_id}{$video_data.preview_image}{/if}{literal}" preload="auto" data-setup='{ "techOrder": [{/literal}{if $video_data.source_id == $_sources.youtube.source_id}"youtube",{/if}{if $video_data.source_id == $_sources.dailymotion.source_id}"dailymotion",{/if}{literal}"html5","flash"], "controls": true, "autoplay": {/literal}{$video_data.video_player_autoplay}{literal} }' width="100%" height="{/literal}{$smarty.const._PLAYER_H_INDEX}{literal}">
		{/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
		<track kind="captions" src="{/literal}{$video_subtitles.filename}{literal}" srclang="{/literal}{$video_subtitles.language_tag|lower}{literal}" label="{/literal}{$video_subtitles.language}{literal}">
		{/literal}{/foreach}{literal}
		</video>

		<script type="text/javascript">
		var video = videojs('video-js').ready(function(){
			var player = this;

			player.on('loadedmetadata', function() {
				$('.vjs-big-play-button').addClass('vjs-pm-show-big-play');
				$('.vjs-control-bar').css({"display": "block"});
			});
			
			player.on('error', function(){
				var MediaError = player.error();
				
				if (MediaError.code == 4) {
					ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ MediaError.message, "", "", false);
				}
				if (MediaError.code == 101 || MediaError.code == 150) {
					ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message=Playback disabled by owner", "", "", false);
				}
			});

			{/literal}{if $smarty.const._WATERMARKURL != ''}{literal}
			player.logobrand({
				image: "{/literal}{$smarty.const._WATERMARKURL}{literal}",
				destination: "{/literal}{$smarty.const._WATERMARKLINK}{literal}"
			});
			{/literal}{/if}


			{if $video_data.source_id == 0}
			{literal}
			player.src([
				{
					src: "{/literal}{$video_data.jw_flashvars.streamer}&mp4:{$video_data.jw_flashvars.file}{literal}", 
					type: "rtmp/mp4"
				}
			]);
			{/literal}
			{/if}

			{if $video_data.source_id == $_sources.localhost.source_id || $video_data.source_id == $_sources.other.source_id}
			{literal}
			player.src([
				{
					src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
					type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "video/flv" {/if}{literal}
				}
			]);
			{/literal}
			{/if}
			
			{if $video_data.source_id == $_sources.youtube.source_id}
			{literal}
			player.src([{
				src: "{/literal}{$video_data.direct}{literal}",
				type: "video/youtube"
			}]);
			{/literal}
			{/if}

			{if $video_data.source_id == $_sources.dailymotion.source_id}
			{literal}
			player.src([{
				src: "{/literal}{$video_data.direct}{literal}",
				type: "video/dailymotion"
			}]);
			{/literal}
			{/if}

			{if $video_data.source_id == $_sources.vimeo.source_id}
			{literal}
			player.src([
				{
					src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
					type: "video/mp4"
				}
			]);
			{/literal}
			{/if}

			{if $video_data.source_id == $_sources.mp3.source_id}
			{literal}
			player.src([
				{
					src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
					type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "audio/mp3" {/if}{literal}
				}
			]);
			{/literal}
			{/if}
			{if $video_data.source_id == $_sources.imgur.source_id || $video_data.source_id == $_sources.gfycat.source_id}
			{literal}
			player.src([
				{
					src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
					type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "video/mp4" {/if}{literal}
				}
			]);
			{/literal}
			{/if}
			
			{if $video_data.source_id == $_sources.vbox7.source_id}
			{literal}
			player.src([
				{
					src: "{/literal}{$smarty.const._URL2}/videos.php?vid={$video_data.uniq_id}{literal}",
					type: {/literal}{if $video_data.file_type != ''}"{$video_data.file_type}" {else} "video/mp4" {/if}{literal}
				}
			]);
			{/literal}
			{/if}

			{literal}
			player.on('waiting', function() {
				$('.vjs-loading-spinner').removeClass('vjs-hidden');
			});
			player.persistvolume({
				namespace: "Melody-vjs-Volume"
			});

			player.socialShare({
				twitter: {}
			});
		});
		</script>
		</div>
		{/literal}

	{elseif $video_data.video_player == "embed"}
		<div id="Playerholder">
			{$video_data.embed_code}
		</div>
	{/if}
{/if}

{/if}
