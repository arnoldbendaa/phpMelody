<div class="tools-drop well well-small" id="playlist-container">
	<ul class="pm-playlist-items tools-drop-list">



        {foreach from=$my_playlists key=index  item=playlist_data name=my_playlists_foreach}
			<li data-playlist-id="{$playlist_data.list_id}" class="tools-drop-item {if $playlist_data.has_current_video} pm-playlist-item-selected{/if}">
				<a href="" class="tools-drop-link" onclick="{if $playlist_data.has_current_video}playlist_remove_item({$playlist_data.list_id}, {$video_data.id});{else}playlist_add_item({$playlist_data.list_id}, {$video_data.id});{/if} return false;">
                    {smarty_fewchars s=$playlist_data.title length=40}
                    {if $playlist_data.has_current_video}
						<i class="icon-ok"></i>
                    {/if}
				</a>
			</li>
        {/foreach}
		<li class="tools-drop-item">
			<a href="" class="tools-drop-link">Add to New Playlist</a>
		</li>


	</ul>
</div>
