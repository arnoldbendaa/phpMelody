{include file='header.tpl' p="general" tpl_name="channel"}

<div id="wrapper" class="profile-page">
	<div class="container-fluid">
	  <div class="row-fluid">
		<div class="span12">
		<div class="pm-profile">
				<div class="pm-profile-header">
						<div class="pm-profile-cover">
							{if $profile_data.id == $s_user_id}
							<div class="pm-profile-cover-preview" data-cropit-height="200">
								<div class="cropit-image-preview">
									{if $profile_data.channel_cover.max != ''}
									<img src="{$profile_data.channel_cover.max}" >
									{else}
									<img src="{$smarty.const._URL}/templates/{$template_dir}/img/bg-channel-cover.png" alt=""  border="0" class="img-responsive">
									{/if}
									<span class="pm-profile-cover-edit"><a href="#" title="{$lang.update_channel_cover}" class="btn btn-link btn-edit" id="btn-edit-cover"><i class="icon icon-pencil"></i></a></span>

									<form action="#" class="cropit-form" id="cropit-cover-form">
										<input type="file" class="cropit-image-input" id="cropit-cover-input" />
										<input type="hidden" name="image-data" class="hidden-cover-data-img" />
										<input type="hidden" name="p" value="upload" />
										<input type="hidden" name="do" value="channel-cover" />
										<button class="btn btn-default btn-cancel">{$lang.submit_cancel}</button>
										<button type="submit" class="btn btn-success">{$lang.submit_save}</button>
									</form>
								</div>
							</div>
							{else}
									{if $profile_data.channel_cover.max != ''}
									<img src="{$profile_data.channel_cover.max}" >
									{else}
									<img src="{$smarty.const._URL}/templates/{$template_dir}/img/bg-channel-cover.png" alt=""  border="0" class="img-responsive">
									{/if}
							{/if}

							<div class="pm-profile-avatar-pic">
								{if $profile_data.id == $s_user_id}
								<div class="cropit-image-preview">
									<img src="{$profile_data.avatar_url}" alt="{$profile_data.username}"  border="0" class="img-responsive">
									<span class="pm-profile-avatar-edit"><a href="#" title="{$lang.update_avatar}" class="btn btn-link btn-edit" id="btn-edit-avatar"><i class="icon icon-pencil"></i></a></span>
									<form action="#" class="cropit-form" id="cropit-avatar-form">
										<input type="file" class="cropit-image-input" id="cropit-avatar-input" />
										<input type="hidden" name="image-data" class="hidden-avatar-data-img" />
										<input type="hidden" name="p" value="upload" />
										<input type="hidden" name="do" value="user-avatar" />
										<button class="btn btn-default btn-cancel-avatar">{$lang.submit_cancel}</button>
										<button type="submit" class="btn btn-mini btn-success">{$lang.submit_save}</button>
									</form>
								</div>
								{else}
									<img src="{$avatar}" alt="{$profile_data.username}" border="0" class="img-responsive">
								{/if}
							</div>
							
							<div class="pm-profile-user-info">
								<h1>{$profile_data.username}

								{if $profile_data.channel_verified && $smarty.const._MOD_SOCIAL}<span class="label">{$lang.verified}</span>{/if}
								<!--{if $profile_data.channel_featured == 1}<span class="label">{$lang.featured}</span>{/if}--> 

								{if $user_is_banned} <span class="label label-banned">{$lang.user_account_banned_label}</span>{/if}
								{if $smarty.const._MOD_SOCIAL && $logged_in == 1 && $s_user_id != $profile_data.id}
									{if $profile_data.is_following_me}
										<span class="label label-social-follows">{$lang.subscriber}</span>
									{/if}
								{/if}
								</h1>

								<div class="pm-profile-buttons">
									{if $smarty.const._MOD_SOCIAL && $logged_in == 1 && $s_user_id != $profile_data.id}
										{include file='user-subscribe-button.tpl' profile_user_id=$profile_data.id}
									{/if}
								</div>
							</div>
					</div>
				</div>

				<div class="pm-profile-body">
					<div class="row-fluid">
						<div class="span9">
							<ul class="pm-profile-stats list-inline">
								<li>{pm_number_format number=$total_submissions} <span>{$lang.videos|lower}</span></li>
								{if $smarty.const._MOD_SOCIAL}
								<li>{pm_number_format number=$profile_data.followers_count} <span>{$lang.subscribers|lower}</span></li>
								{/if}
								<li>{pm_number_format number=$total_playlists} <span>{$lang._playlists|lower}</span></li>
							</ul>
						</div>
						<div class="span3">
							{if count($profile_data.social_links) > 0}
							<ul class="pm-profile-links list-inline pull-right">
								{if $profile_data.social_links.website != ''}<li><a href="{$profile_data.social_links.website}" target="_blank" rel="nofollow"><i class="fa fa-globe"></i></a></li>{/if}
								{if $profile_data.social_links.facebook != ''}<li><a href="{$profile_data.social_links.facebook}" target="_blank" rel="nofollow"><i class="fa fa-facebook-square"></i></a></li>{/if}
								{if $profile_data.social_links.twitter != ''}<li><a href="{$profile_data.social_links.twitter}" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>{/if}
								{if $profile_data.social_links.instagram != ''}<li><a href="{$profile_data.social_links.instagram}" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>{/if}
								{if $profile_data.social_links.google_plus != ''}<li><a href="{$profile_data.social_links.google_plus}" target="_blank" rel="nofollow"><i class="fa fa-google-plus"></i></a></li>{/if}
								{if isset($mm_profile_webfields_inject)}{$mm_profile_webfields_inject}{/if}
							</ul>
							{/if}
						</div>
					</div>
					<div class="clearfix"></div>
					{if $profile_data.about}
						<div class="pm-profile-desc">
						<span class="ellipsis-line">{smarty_fewchars s=$profile_data.about length=280}</span>
						{if isset($mm_profile_info_inject)}{$mm_profile_info_inject}{/if}
						</div>
					{/if}
				</div>
		</div>

		<div class="row-fluid" id="sticky">
			<div class="span12">
				<ul class="nav nav-tabs">
					<li {if $smarty.get.view == 'videos' || $smarty.get.view == ''}class="active"{/if}><a href="#pm-pro-own" data-toggle="tab">{$lang.videos}</span></a></li>
					{if $smarty.const._MOD_SOCIAL}
					{if  $s_user_id == $profile_data.id}
						<li><a href="#pm-pro-activity-stream" data-toggle="tab">{$lang.activity_newsfeed}</a></li>
					{/if}
					{if $s_user_id == $profile_data.id || $profile_data.am_following}
						<li><a href="#pm-pro-user-activity" data-toggle="tab">{$lang.my_activity}</a></li>
					{/if}
					{/if}
					<li {if $smarty.get.view == 'playlists'}class="active"{/if}><a href="#pm-pro-playlists" id="pm-pro-playlists-btn" class="pm-pro-playlists-btn" data-profile-id="{$profile_data.id}" data-toggle="tab">{$lang._playlists}</a></li>
					{if $smarty.const._MOD_SOCIAL && $s_user_id == $profile_data.id}
						<li><a href="#pm-pro-followers" data-toggle="tab">{$lang.subscribers}</a></li>
						<li><a href="#pm-pro-following" data-toggle="tab">{$lang.subscribed_to}</a></li>
					{/if}

					<li><a href="#pm-pro-about" data-toggle="tab">{$lang._about}</a></li>
				</ul>
			</div>
		</div>

		<div class="row-fluid">
		<div class="span12">
		<div class="tab-content">
			<div class="tab-pane {if $smarty.get.view == 'videos' || $smarty.get.view == ''}active{else}fade{/if}" id="pm-pro-own">
			<h4>{$lang.videos_from_s|sprintf:$profile_data.username}</h4>

            <ul class="pm-ul-browse-videos thumbnails">
            {foreach from=$submitted_video_list key=k item=video_data}
              <li>
                <div class="pm-li-video{if $video_data.pending_approval} pending{/if}">
                    <span class="pm-video-thumb pm-thumb-234 pm-thumb border-radius2">

					{if $profile_data.id == $s_user_id && $allow_user_edit_video}
						{if $video_data.pending_approval}
						<a href="{$smarty.const._URL}/edit-video.php?vid={$video_data.id}&type=pending" class="btn btn-mini btn-edit-video" rel="tooltip" title="{$lang.edit}"><i class="fa fa-pencil"></i></a>
						{else}
						<a href="{$smarty.const._URL}/edit-video.php?vid={$video_data.uniq_id}" class="btn btn-mini btn-edit-video" rel="tooltip" title="{$lang.edit}"><i class="fa fa-pencil"></i></a>
						{/if}
					{/if}

                    <span class="pm-video-li-thumb-info">
	                    {if $video_data.yt_length != 0}<span class="pm-label-duration border-radius3 opac7">{$video_data.duration}</span>{/if}
						{if $logged_in}
						<span class="watch-later hide">
							<button class="btn btn-mini watch-later-add-btn-{$video_data.id}" onclick="watch_later_add({$video_data.id}); return false;" rel="tooltip" title="{$lang.add_to} {$lang.watch_later}"><i class="icon-time"></i></button>
							<button class="btn btn-mini btn-remove hide watch-later-remove-btn-{$video_data.id}" onclick="watch_later_remove({$video_data.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="icon-ok"></i></button>
						</span>
						{/if}
                    </span>
                    <a href="{$video_data.video_href}" class="pm-thumb-fix pm-thumb-234"><span class="pm-thumb-fix-clip"><img src="{$video_data.thumb_img_url}" alt="{$video_data.video_title}" width="234"><span class="vertical-align"></span></span></a>
                    {if $video_data.pending_approval}<div class="pending-approval">{$lang.pending_approval}</div>{/if}
                    </span>
                    
                    <h3><a href="{$video_data.video_href}" class="pm-title-link" title="{$video_data.video_title}">{$video_data.video_title}</a></h3>
                    <div class="pm-video-attr">
                        <span class="pm-video-attr-author">{$lang.articles_by} <a href="{$video_data.author_profile_href}">{$video_data.author_username}</a></span>
                        <span class="pm-video-attr-since"><small>{$lang.added} <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$video_data.time_since_added} {$lang.ago}</time></small></span>
                        <span class="pm-video-attr-numbers"><small>{$video_data.views_compact} {$lang.views} / {$video_data.likes_compact} {$lang._likes}</small></span>
                    </div>
                    <p class="pm-video-attr-desc">{$video_data.excerpt}</p>
                    {if $video_data.featured}
                    <span class="pm-video-li-info">
                        <span class="label label-featured">{$lang._feat}</span>
                    </span>
                    {/if}
                </div>
              </li>
            {foreachelse}
                {$lang.top_videos_msg2}
            {/foreach}
            </ul>
			
			{if count($submitted_video_list) >= 10}
			<a href="{$smarty.const._URL}/search.php?keywords={$username}&btn=Search&t=user" class="btn btn-small" title="{$lang.profile_watch_all}">{$lang.profile_watch_all}</a>
			{/if}
			</div>

			<div class="tab-pane fade" id="pm-pro-about">
			<h4>{$lang._about}</h4>
			{if $about}
				<p>{$about}</p>
			{else}
				<p>{$lang.profile_msg_about_empty}</p>
			{/if}
			{if isset($mm_profile_info_inject)}{$mm_profile_info_inject}{/if}

			<div class="clearfix"></div>

			{if count($profile_data.social_links) > 0}
			<h4>{$lang._social}</h4>
			<ul class="pm-pro-social-links list-unstyled">
			{if $profile_data.social_links.website != ''} 
				<li><i class="fa fa-globe"></i> <a href="{$profile_data.social_links.website}" target="_blank" rel="nofollow">{$profile_data.social_links.website}</a></li>
			{/if}
			{if $profile_data.social_links.facebook != ''}
				<li><i class="fa fa-facebook-square"></i> <a href="{$profile_data.social_links.facebook}" target="_blank" rel="nofollow">{$profile_data.social_links.facebook}</a></li>
			{/if}
			{if $profile_data.social_links.twitter != ''}
				<li><i class="fa fa-twitter"></i> <a href="{$profile_data.social_links.twitter}" target="_blank" rel="nofollow">{$profile_data.social_links.twitter}</a></li>
			{/if}
			{if $profile_data.social_links.instagram != ''}
				<li><i class="fa fa-instagram"></i> <a href="{$profile_data.social_links.instagram}" target="_blank" rel="nofollow">{$profile_data.social_links.instagram}</a></li> 
			{/if}
			{if $profile_data.social_links.google_plus != ''}
				<li><i class="fa fa-google-plus"></i> <a href="{$profile_data.social_links.google_plus}" target="_blank" rel="nofollow">{$profile_data.social_links.google_plus}</a></li> 
			{/if}
			{if isset($mm_profile_webfields_inject)}{$mm_profile_webfields_inject}{/if}
			</ul>
			{/if}
			</div>

			<div class="tab-pane {if $smarty.get.view == 'playlists'}fade in active{else}fade{/if}" id="pm-pro-playlists">
				{if $profile_data.id == $s_user_id}
				<h4>{$lang.my_playlists}</h4>
				{else}
				<h4>{$lang._playlists}</h4>
				{/if}
				<div id="profile-playlists-container">
					<img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" alt="{$lang.please_wait}" align="absmiddle" border="0" width="16" height="16" /> {$lang.please_wait}
				</div>
			</div>

			{if $smarty.const._MOD_SOCIAL}
			<div class="tab-pane fade" id="pm-pro-followers">
			<h4>{$lang.subscribers}</h4>
				<div id="pm-pro-followers-content"></div>
			</div>
			
			<div class="tab-pane fade" id="pm-pro-following">
			{if is_array($who_to_follow_list)}
				<h4>{$lang.suggested_channels}</h4>
				<a href="#" id="hide_who_to_follow" class="pm-pro-suggest-hide">&times; {$lang.hide}</a>
				<ul class="row-fluid pm-channels-list list-unstyled">
				{foreach from=$who_to_follow_list key=k item=user_data}
				<li class="span4">
					<div class="pm-channel">
						<div class="pm-channel-header">
							<div class="pm-channel-cover">
								{if $user_data.channel_cover.max}
									<img src="{$user_data.channel_cover.450}" alt="{$user_data.username}"  border="0" class="img-responsive">
								{/if}
							</div>
							<div class="pm-channel-profile-pic">
								<a href="{$user_data.profile_url}"><img src="{$user_data.avatar_url}" alt="{$user_data.username}"  border="0" class="img-responsive"></a>
							</div>
						</div>
						<div class="pm-channel-body">
							<h3><a href="{$user_data.profile_url}" class="ellipsis-line {if $user_data.user_is_banned}pm-user-banned{/if}">{$user_data.name}</a> {if $user_data.is_following_me}<span class="label label-success label-follow-status pm-follows">{$lang.subscriber}</span>{/if}</h3>
							<div class="pm-channel-stats"> {$user_data.videos_count_formatted} {$lang.videos|lower} / {$user_data.followers_count_formatted} {$lang.subscribers|lower}</div>
							<div class="pm-channel-desc">{$user_data.about}</div>
							<div class="pm-channel-buttons">
								{if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $user_data.id != $s_user_id}
									{include file="user-subscribe-button.tpl" profile_data=$user_data profile_user_id=$user_data.id}
								{/if}
							</div>
						</div>
					</div>
				</li>

				{/foreach}
				</ul>
			{/if}

			<h4>{$lang.subscribed_to}</h4>
				<div id="pm-pro-following-content"></div>
			</div>

			{if $s_user_id == $profile_data.id || $profile_data.am_following}
			<div class="tab-pane fade" id="pm-pro-user-activity"> 
			<h4>{$lang.my_activity}</h4>
				<div id="pm-pro-user-activity-content"></div>
			</div>
			{/if}
			
			{if $s_user_id == $profile_data.id}
			<div class="tab-pane fade" id="pm-pro-activity-stream">	
			<h4>{$lang.activity_newsfeed}</h4>
                <form name="user-update-status" method="post" action="" onsubmit="update_status();return false;" >
                    <textarea class="span12" name="post-status" ></textarea>
                    <br />
                    <button type="submit" name="btn-update-status" class="btn btn-blue" />{$lang.status_update}</button>
                </form>
				<div id="pm-pro-activity-stream-content">
					{include file='activity-stream.tpl'}
				</div>
			</div>
			{/if}
			{/if}
			
		  </div><!-- /tab-content -->
		</div>
		</div>
		<input type="hidden" name="profile_user_id" value="{$profile_data.id}" />
		</div><!-- #content -->
	  </div><!-- .row -->
	</div><!-- .container -->
{include file='footer.tpl' tpl_name='channel'}