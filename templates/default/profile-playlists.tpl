{include file='header.tpl' no_index='1' p="playlists"}
<div id="wrapper">
    <div class="container-fluid">


      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
		
	        {if $allow_playlists}
			<a href="#new-playlist" data-toggle="modal" data-backdrop="true" data-keyboard="true" class="btn btn-success pull-right">{$lang.playlist_create_new}</a>
			{/if}
			
			<h1>{$lang.manage_playlists}</h1>
			<hr />

	        <div class="row-fluid">
	        	<div class="span12">        	
				{include file='profile-playlists-ul.tpl' playlists=$playlists}
	            </div>
	        </div>

			<!-- #new-playlist modal -->
			{if $allow_playlists}
			<form name="new-playlist" class="form-horizontal" method="post" action="">
			<div class="modal hide" id="new-playlist" role="dialog" aria-labelledby="new-playlist-form-label">
			    <div class="modal-header">
			         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			         <h3 id="new-playlist-form-label">{$lang.playlist_create_new}</h3>
			    </div>
			    <div class="modal-body">
			    	<div id="playlist-modal-ajax-response" class="hide"></div>
										

					<div class="control-group">
						<label class="control-label">{$lang.playlist_name}</label>
						<div class="controls">
						<input type="text" name="playlist_name" value="" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">{$lang.playlist_privacy}</label>
						<div class="controls">
						<select name="visibility">
							<option value="{$smarty.const.PLAYLIST_PUBLIC}">{$lang.public}</option>
							<option value="{$smarty.const.PLAYLIST_PRIVATE}">{$lang.private}</option>
						</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">{$lang.video_ordering}</label>
						<div class="controls">
						<select name="sorting">
							<option value="default">{$lang._manual}</option>
							<option value="popular">{$lang.most_popular}</option>
							<option value="date-added-desc">{$lang.sort_added_new}</option>
							<option value="date-added-asc">{$lang.sort_added_old}</option>
							<option value="date-published-desc">{$lang.sort_published_new}</option>
							<option value="date-published-asc">{$lang.sort_published_old}</option>
						</select>
						</div>
					</div>
			    </div>
				<div class="modal-footer">
					<img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="16" height="16" alt="{$lang.please_wait}" align="absmiddle" border="0" class="hide" id="modal-loading-gif">
					<a href="#" class="btn btn-small btn-link" data-dismiss="modal" >{$lang.submit_cancel}</a>
					<a href="#" class="btn btn-success" id="create_playlist_submit_btn" onclick="playlist_create(this, 'playlists-modal'); return false;" disabled>{$lang._create}</a>
				</div>
			</div>
			</form>
			{/if}
			
		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
{include file='footer.tpl'} 