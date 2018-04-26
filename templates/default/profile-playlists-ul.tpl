<ul class="pm-ul-browse-playlists thumbnails">
	{foreach from=$playlists key=k item=playlist_data name=playlists_foreach}
		<li>
		    <div class="pm-pl-thumb">
		    	<img src="{$playlist_data.thumb_url}">
		    	<div class="pm-pl-count"><span class="pm-pl-items">{$playlist_data.items_count}</span> {if $playlist_data.items_count == 1}{$lang._video}{else}{$lang.videos}{/if}</div> <a href="{$playlist_data.playlist_watch_all_href}" class="pm-pl-thumb-overlay" rel="nofollow">&#9658; {$lang.play_all}</a>
		    </div>
		    <h3>{if $playlist_data.visibility == $smarty.const.PLAYLIST_PRIVATE}<i class="icon-lock opac7"></i> {/if}<a href="{$playlist_data.playlist_href}" class="pm-title-link" title="{$playlist_data.title}">{smarty_fewchars s=$playlist_data.title length=80}</a></h3>
		    <span class="pm-video-attr-since"><small>{$lang.added} <time datetime="{$playlist_data.html5_datetime}" title="{$playlist_data.full_datetime}">{$playlist_data.time_since} {$lang.ago}</time></small></span>
		</li>
	{/foreach}
</ul>