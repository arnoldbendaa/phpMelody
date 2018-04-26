<span class="pm-comment-avatar">
	{if $comment_data.user_id == 0}
		<span class="pm-img-avatar"><img src="{$comment_data.avatar_url}" height="40" width="40" alt="" class="img-polaroid"></span>
	{else}
		<span class="pm-img-avatar"><a href="{$comment_data.user_profile_href}"><img src="{$comment_data.avatar_url}" height="40" width="40" alt="" class="img-polaroid"></a></span>
		
	{/if}
</span>
<span class="pm-comment-info">
    <span class="pm-comment-author">
    	{if $comment_data.user_id == 0} 
			{$comment_data.name}
		{else} 
			{if $comment_data.user_is_banned}
				<a href="{$comment_data.user_profile_href}">
					<span class="pm-comment-banned">{$comment_data.name}</span>
				</a>
			{else}
				<a href="{$comment_data.user_profile_href}">{$comment_data.name}</a>
			{/if}
			{if $comment_data.channel_verified == 1}<a href="#" rel="tooltip" title="{$lang.verified_channel}"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" width="12" height="12" /></a>{/if}
			<span class="label-banned-{$comment_data.user_id} label label-important {if ! $comment_data.user_is_banned}hide{/if}">{$lang.user_account_banned_label}</span>
		{/if}

	{if $can_manage_comments}
	<span class="opac5"><small>({$comment_data.user_ip})</small></span><!-- author ip -->
    {/if}
	</span>
    <span class="pm-comment-since"><small>{$lang.added} <time datetime="{$comment_data.html5_datetime}" title="{$comment_data.full_datetime}">{$comment_data.time_since_added} {$lang.ago}</time></small></span>
    <span class="pm-comment-txt">{$comment_data.comment}</span>
</span>
{if $logged_in}
<span class="pm-comment-action" id="users-{$smarty.foreach.comment_foreach.iteration}">
	<div class="btn-group pull-right">
		{if $comment_data.user_id > 0 && $comment_data.user_id != $s_user_id && $can_manage_comments && $comment_data.power != $smarty.const.U_ADMIN}
			{if $comment_data.user_is_banned}
				<button class="unban-{$comment_data.user_id} btn btn-mini active" type="button" id="unban-{$comment_data.id}" rel="tooltip" title="{$lang.user_account_remove_ban}"><i class="icon-ban-circle opac7"></i></button>
			{else}
				<button class="ban-{$comment_data.user_id} btn btn-mini" type="button" id="ban-{$comment_data.id}" rel="tooltip" title="{$lang.user_account_add_ban}"><i class="icon-ban-circle opac7"></i></button>
			{/if}
		{/if}
		<button class="btn btn-mini {if $comment_data.user_likes_this}active{/if}" type="button" {if $comment_data.user_id != $s_user_id}id="comment-like-{$comment_data.id}"{/if} rel="tooltip" title="{$lang._like}"><i class="icon-thumbs-up opac7"></i>
		<span id="comment-like-count-{$comment_data.id}">
		{if $comment_data.up_vote_count > 0}
			{$comment_data.up_vote_count}
		{/if}
		</span>
		</button>
		<button class="btn btn-mini {if $comment_data.user_dislikes_this}active{/if}" type="button" {if $comment_data.user_id != $s_user_id}id="comment-dislike-{$comment_data.id}"{/if} rel="tooltip" title="{$lang._dislike}"><i class="icon-thumbs-down opac7"></i>
		<span id="comment-dislike-count-{$comment_data.id}">
		{if $comment_data.down_vote_count > 0}
			{$comment_data.down_vote_count}
		{/if}
		</span>
		</button>
		<button class="btn btn-mini {if $comment_data.user_flagged_this}active{/if}" type="button" id="comment-flag-{$comment_data.id}" rel="tooltip" title="{$lang.report_form5}"><i class="icon-flag opac7"></i></button>
	{if $can_manage_comments}
	<button class="btn btn-mini btn-warning" onclick="onpage_delete_comment('{$comment_data.id}', '{$comment_data.uniq_id}', '#comment-{$smarty.foreach.comment_foreach.iteration}'); return false;" rel="tooltip" title="Delete comment"><i class="icon-remove opac7"></i></button>
	{/if}
	</div>
</span>
{/if}