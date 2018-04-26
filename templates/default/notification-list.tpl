<ul class="pm-notifications">
	{foreach from=$notification_list key=notification_id item=el}
		{if $el.activity_type == $smarty.const.ACT_TYPE_FOLLOW}
			{assign var='main_href' value=$el.metadata.from_userdata.profile_url}
		{else}
			{assign var='main_href' value=$el.metadata.object.video_href}
		{/if}
		
		<li {if $el.seen == 0} class="pm-notification-unread"{/if}>
        	<div class="pm-n-avatar">
        		{if $el.metadata.from_userdata.id != 0}
					<a href="{$el.metadata.from_userdata.profile_url}"><img src="{$el.metadata.from_userdata.avatar_url}" width="40" height="" border="0" /></a>
				{else}
					<img src="{$el.metadata.from_userdata.avatar_url}" width="40" height="" border="0" />
				{/if}
            </div>
            <div class="pm-n-activity">
	            {if $el.metadata.from_userdata.id != 0}
					<a href="{$el.metadata.from_userdata.profile_url}">{$el.metadata.from_userdata.name}</a>
				{else}
					<strong>{$el.metadata.from_userdata.name}</strong>
				{/if}
                {if $el.activity_type == $smarty.const.ACT_TYPE_LIKE}{/if}
                {$el.lang}
                {if $el.metadata.object_type == $smarty.const.ACT_OBJ_VIDEO}
                    <a href="{$el.metadata.object.video_href}">{$el.metadata.object.video_title}</a>
                {/if}
                {if $el.metadata.object_type == $smarty.const.ACT_OBJ_ARTICLE}
                    <a href="{$el.metadata.object.link}">{$el.metadata.object.title}</a>
                {/if}
                <div class="pm-ml-activity-date">{$el.time_since} {$lang.ago}</div>
			</div>
            <div class="clearfix"></div>
		</li>
	{foreachelse}
		<li>{$lang.notification_list_empty}</li>
	{/foreach}
</ul>
{if $total_notifications == 7}
	<div class="clearfix"></div>
	<span name="notifications_load_more" id="btn_notifications_load_more"></span>
{/if}