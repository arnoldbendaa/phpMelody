{if ! $profile_data.am_following}
	<button id="btn_follow_{$profile_user_id}" class="btn btn-small btn-follow border-radius4" data-user-id="{$profile_user_id}">{$lang.subscribe}</button>
{else}
	<button id="btn_unfollow_{$profile_user_id}" class="btn btn-unfollow btn-small border-radius4" data-user-id="{$profile_user_id}"><i class="icon-ok icon-white"></i> {$lang.subscribed}</button>
{/if}