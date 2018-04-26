<ul class="pm-ul-memberlist">
	{foreach from=$profile_list key=profile_user_id item=profile}
		<li>
			<span class="pm-ml-username"><a href="{$profile.profile_url}">{$profile.name}</a> 
              {if $profile.is_following_me}
                  <span class="label pm-follows">{$lang.subscriber}</span>
              {/if}
          	</span>
			<span class="pm-ml-avatar"><a href="{$profile.profile_url}"><img src="{$profile.avatar_url}" alt="{$profile.username}" width="60" height="60" border="0" class="img-polaroid"></a></span>
			{if $profile.channel_verified == 1}<a href="#" rel="tooltip" title="{$lang.verified_channel}"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" width="14" height="14" alt="" /></a>{/if}			
			<div class="pm-ml-buttons">
			{if $profile_user_id != $s_user_id}
				{include file="user-subscribe-button.tpl" profile_data=$profile profile_user_id=$profile_user_id}
			{/if}
            </div>
            <div class="clearfix"></div>
		</li>
	{/foreach}
	{if $follow_count == 0}
		{$lang.memberlist_msg3}
	{/if}
</ul>
{if $total_profiles == $smarty.const.FOLLOW_PROFILES_PER_PAGE}
	<div class="clearfix"></div>
	<span id="btn_follow_load_more"></span>
{/if}