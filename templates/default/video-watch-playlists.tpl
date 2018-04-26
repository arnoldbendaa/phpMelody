<div class="well well-small" id="playlist-container">
	{if count($my_playlists) > 0}
	<ul class="pm-playlist-items unstyled">
        {foreach from=$my_playlists key=index  item=playlist_data name=my_playlists_foreach}
		<li data-playlist-id="{$playlist_data.list_id}" {if $playlist_data.has_current_video}class="pm-playlist-item-selected"{/if}>
        <!--<li class="pm-playlist-item-selected">-->
            <a href="" onclick="{if $playlist_data.has_current_video}playlist_remove_item({$playlist_data.list_id}, {$video_data.id});{else}playlist_add_item({$playlist_data.list_id}, {$video_data.id});{/if} return false;">
                <span class="pm-playlists-name">
                	{smarty_fewchars s=$playlist_data.title length=40} <span class="pm-playlists-video-count">({$playlist_data.items_count})</span>
				</span> 
                <span class="pm-playlist-visibility">
                	{if $playlist_data.visibility == $smarty.const.PLAYLIST_PUBLIC}
                		{$lang.public}
					{/if}
					{if $playlist_data.visibility == $smarty.const.PLAYLIST_PRIVATE}
                		{$lang.private}
					{/if}
				</span>
                <span class="pm-playlist-created">
                	<time datetime="{$playlist_data.html5_datetime}" title="{$playlist_data.full_datetime}">{$playlist_data.time_since} {$lang.ago}</time>
				</span>
                <span class="pm-playlist-response">
                	{if $playlist_data.has_current_video}
                		<i class="icon-ok"></i>
					{else}
						<span class="pm-playlist-response"></span>
					{/if}
				</span>
            </a>
		{/foreach}
	</ul>
	{else}
	<img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" alt="{$lang.please_wait}" align="absmiddle" border="0" width="16" height="16" /> {$lang.please_wait}
	{/if}
</div>