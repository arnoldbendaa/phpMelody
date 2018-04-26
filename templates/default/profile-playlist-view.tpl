{include file='header.tpl' no_index='1' p="playlists"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
        {if $my_playlist}
		<a href="{$smarty.const._URL}/playlists.{$smarty.const._FEXT}" class="btn btn-blue pull-right">{$lang.view_all}</a>
		<h1>{$lang.manage_playlists}</h1>
		<hr />
		{else}
		<br />
		{/if}

		<div class="row-fluid">
			<div class="span12">
			{if ! is_array($playlist)}
				<div class="alert alert-danger">
					{$lang.playlist_not_found}
				</div>
			{elseif $playlist.visibility == $smarty.const.PLAYLIST_PRIVATE && ! $my_playlist}
				<div class="alert alert-info">
					<i class="icon-lock opac7"></i> {$lang.playlist_private}
				</div>
			{else}
				<div class="pm-playlist-edit">
					<div class="pm-pl-header row-fluid">
						<div class="span3">
							<div class="pm-pl-thumb">
							<img src="{$playlist.thumb_url}" height="126" border="0">
							<a href="{$playlist.playlist_watch_all_href}" class="pm-pl-thumb-overlay" rel="nofollow">&#9658; {$lang.play_all}</a>
							</div>
						</div>
						<div class="pm-pl-header-content span9">
							<div class="pm-pl-header-title">
								{if $playlist.visibility == $smarty.const.PLAYLIST_PRIVATE}
									<a href="#playlist-settings" {if $my_playlist}data-toggle="modal" data-backdrop="true" data-keyboard="true"{/if} rel="tooltip" title="{$lang.playlist_private_desc}" class="pm-pl-status-icon"><i class="icon-lock opac7"></i></a>
								{/if}
								{if $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}
									<a href="#playlist-settings" {if $my_playlist}data-toggle="modal" data-backdrop="true" data-keyboard="true"{/if} rel="tooltip" title="{$lang.playlist_public_desc}" class="pm-pl-status-icon"><i class="icon-globe opac7"></i></a>
								{/if}
								<h3>{$playlist.title}</h3> 
							</div>

							<ul class="pm-pl-header-details unstyled">
								<li>{$lang._by} <a href="{$playlist.user_profile_href}">{$playlist.user_name}</a></li>
								<li>{if $playlist.items_count == 1}1 {$lang._video}{else}{$playlist.items_count} {$lang.videos}{/if}</li>
							</ul>
							
							<div class="pm-pl-actions">
								{if $playlist.items_count > 0}
								<a href="{$playlist.playlist_watch_all_href}" class="btn btn-small border-radius0 btn-video" rel="nofollow"><i class="icon-play"></i> {$lang.play_all}</a>
								{/if} 
								{if $share_link != '' && $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}
								<a href="#playlist-share" class="btn btn-small border-radius0 btn-video" data-toggle="modal" data-backdrop="true" data-keyboard="true"><i class="icon-share-alt"></i> {$lang._share}</a>
								{/if} 
								{if $my_playlist && $playlist.type != $smarty.const.PLAYLIST_TYPE_WATCH_LATER && $playlist.type != $smarty.const.PLAYLIST_TYPE_HISTORY}
								<a href="#playlist-settings" class="btn btn-small border-radius0 btn-video" data-toggle="modal" data-backdrop="true" data-keyboard="true"><i class="icon-cog"></i> {$lang.playlist_settings}</a>
								{/if}
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="pm-pl-content">
						<ul class="pm-pl-list unstyled">
							{if $playlist.items_count == 0}
							<li>
								<p>{$lang.playlist_empty}</p>
							</li>
							{else}
							{foreach from=$playlist_items key=index item=list_video name=playlist_items_foreach}
							<li class="playlist-item" id="playlist_item_{$list_video.id}">
								<div class="pm-pl-list-index">{$index+1}</div>
								<div class="pm-pl-list-thumb"><a href="{$list_video.playlist_video_href}" rel="nofollow"><img src="{$list_video.thumb_img_url}" height="40" border="0"></a></div>
								<div class="pm-pl-list-title"><a href="{$list_video.playlist_video_href}" rel="nofollow">{$list_video.video_title}</a></div>
								<div class="pm-pl-list-author"><a href="{$list_video.author_profile_href}">{$list_video.author_name}</a></div>
								{if $my_playlist}
								<div class="pm-pl-list-action">
									<a href="#" class="btn btn-link" onclick="playlist_delete_item({$playlist.list_id}, {$list_video.id}, '#playlist_item_{$list_video.id}'); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="icon-remove opac7"></i></a>
								</div>
								{/if}
							</li>
							{/foreach}
							{/if}
						</ul>
					</div>
				</div>
				{/if}
			</div>
		</div>


		<!-- #playlist-share modal -->
		{if $share_link != '' && $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}
		<div class="modal hide" id="playlist-share" role="dialog" aria-labelledby="playlist-share-form-label">
		    <div class="modal-header">
		         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		         <h3 id="playlist-share-form-label">{$lang._share}</h3>
		    </div>
		    <div class="modal-body">
		        <p>{$lang.playlist_share_msg}</p>
                <div class="row-fluid">
                    <div class="span3">
	                    <a href="https://www.facebook.com/sharer.php?u={$share_link_urlencoded}&t={$share_title_urlencoded}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on FaceBook"><i class="pm-vc-sprite facebook-icon"></i></a>
	                    <a href="https://twitter.com/home?status=Watching%20{$share_title_urlencoded}%20on%20{$share_link_urlencoded}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Twitter"><i class="pm-vc-sprite twitter-icon"></i></a>
	                    <a href="https://plus.google.com/share?url={$share_link_urlencoded}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" rel="tooltip" title="Share on Google+"><i class="pm-vc-sprite google-icon"></i></a>
                    </div>
                    <div class="span9">
                    	<div class="input-prepend">
                    		<span class="add-on">URL</span><input name="share_link" id="share_link" type="text" value="{$share_link}" class="span11" onClick="SelectAll('share_link');">
						</div>
                    </div>
                </div>
		    </div>
		</div>
		{/if}

		<!-- #playlist-settings modal -->
		{if $playlist.type != $smarty.const.PLAYLIST_TYPE_WATCH_LATER && $playlist.type != $smarty.const.PLAYLIST_TYPE_HISTORY}
		<form name="playlist-settings" class="form-horizontal" method="post" action="">
		<div class="modal hide" id="playlist-settings" role="dialog" aria-labelledby="playlist-settings-form-label">
		    <div class="modal-header">
		         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		         <h3 id="playlist-settings-form-label">{$lang.playlist_settings}</h3>
		    </div>
		    <div class="modal-body">
		    	<div id="playlist-modal-ajax-response" class="hide"></div>
				
				{if $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM}
				<div class="control-group">
					<label class="control-label">{$lang.playlist_name}</label>
					<div class="controls">
					<input type="text" name="playlist_name" value="{$playlist.title}" />
					</div>
				</div>
				{/if} 
				
				<div class="control-group">
					<label class="control-label">{$lang.playlist_privacy}</label>
					<div class="controls">
					<select name="visibility">
						<option value="{$smarty.const.PLAYLIST_PUBLIC}" {if $playlist.visibility == $smarty.const.PLAYLIST_PUBLIC}selected="selected"{/if}>{$lang.public}</option>
						<option value="{$smarty.const.PLAYLIST_PRIVATE}" {if $playlist.visibility == $smarty.const.PLAYLIST_PRIVATE}selected="selected"{/if}>{$lang.private}</option>
					</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">{$lang.video_ordering}</label>
					<div class="controls">
					<select name="sorting">
						<option value="default" {if $playlist.sorting == 'default'}selected="selected"{/if}>{$lang._manual}</option>
						<option value="popular" {if $playlist.sorting == 'popular'}selected="selected"{/if}>{$lang.most_popular}</option>
						<option value="date-added-desc" {if $playlist.sorting == 'date-added-desc'}selected="selected"{/if}>{$lang.sort_added_new}</option>
						<option value="date-added-asc" {if $playlist.sorting == 'date-added-asc'}selected="selected"{/if}>{$lang.sort_added_old}</option>
						<option value="date-published-desc" {if $playlist.sorting == 'date-published-desc'}selected="selected"{/if}>{$lang.sort_published_new}</option>
						<option value="date-published-asc" {if $playlist.sorting == 'date-published-asc'}selected="selected"{/if}>{$lang.sort_published_old}</option>
					</select>
					</div>
				</div>
		    </div>
			<div class="modal-footer">
				{if $my_playlist}
				<a href="#" class="btn btn-danger {if $playlist.type != $smarty.const.PLAYLIST_TYPE_CUSTOM}disabled{/if} pull-left" {if $playlist.type == $smarty.const.PLAYLIST_TYPE_CUSTOM} onclick="playlist_delete({$playlist.list_id}, this);" {/if}>{$lang.submit_delete}</a>
				{/if}
				<img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="16" height="16" alt="{$lang.please_wait}" class="hide" id="modal-loading-gif">
				<a href="#" class="btn btn-small btn-link" data-dismiss="modal" >{$lang.submit_cancel}</a>
				<a href="#" class="btn btn-success" onclick="playlist_save_settings({$playlist.list_id}, this); return false;">{$lang.submit_save}</a>
			</div>
		</div>
		</form>
		{/if}
		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
{include file='footer.tpl'} 