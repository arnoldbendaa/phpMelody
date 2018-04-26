<li class="pm-li-activity" id="activity-{$activity_meta.activity_id}">
	<span class="pm-ml-avatar"><a href="{$actor_bucket.$activity_index.0.profile_url}"><img src="{$actor_bucket.$activity_index.0.avatar_url}" alt="{$actor_bucket.$activity_index.0.username}" width="40" height="40" border="0" class="img-polaroid"></a></span>
	<div class="pm-ml-activity">
	<span class="pm-ml-username">
		<a href="{$actor_bucket.$activity_index.0.profile_url}">{$actor_bucket.$activity_index.0.name}</a>
	</span>
	
	{if $activity_meta.actors_count > 2}
		{$lang.and} 
		<a href="#">{$activity_meta.actors_count-1} {$lang.other|strtolower}</a>
		(
			{foreach from=$actor_bucket.$activity_index key=kk item=actor name=actors_foreach}
				{if $kk > 0}
					<a href="{$actor.profile_url}">{$actor.name}</a>{if ! $smarty.foreach.actors_foreach.last},{/if}
				{/if}
			{/foreach}
		)
	{elseif $activity_meta.actors_count == 2}
		{$lang.and} <a href="{$actor_bucket.$activity_index.1.profile_url}">{$actor_bucket.$activity_index.1.name}</a>
	{/if}
    
	{if $activity_meta.activity_type == $smarty.const.ACT_TYPE_STATUS}
    	<div class="clearfix"></div>
		<div class="pm-ml-speech top">{$activity_meta.metadata.statustext}</div>
	{/if}
	{$activity_meta.lang}
	
	{if $activity_meta.objects_count == 1}
		{if $activity_meta.object_type == $smarty.const.ACT_OBJ_USER}
			<a href="{$object_bucket.$activity_index.0.profile_url}">{$object_bucket.$activity_index.0.name}</a>.
		{/if}
		
		{if $activity_meta.object_type == $smarty.const.ACT_OBJ_VIDEO}
			<a href="{$object_bucket.$activity_index.0.video_href}">{$object_bucket.$activity_index.0.video_title}</a>
				<span class="pm-video-thumb pm-thumb-76 pm-thumb border-radius2">
			    <a href="{$object_bucket.$activity_index.0.video_href}" class="pm-thumb-fix pm-thumb-76"><span class="pm-thumb-fix-clip"><img src="{$object_bucket.$activity_index.0.thumb_img_url}" width="76"><span class="vertical-align"></span></span></a>
			    </span>
		{/if}
		
		{if $activity_meta.object_type == $smarty.const.ACT_OBJ_ARTICLE}
			<a href="{$object_bucket.$activity_index.0.link}">{$object_bucket.$activity_index.0.title}</a>
		{/if}
		
		{if $activity_meta.object_type == $smarty.const.ACT_OBJ_PROFILE}
			
		{/if}	
		
	{elseif $activity_meta.objects_count == 2}
		<a href="{$object_bucket.$activity_index.0.profile_url}">{$object_bucket.$activity_index.0.name}</a> {$lang.and} <a href="{$object_bucket.$activity_index.1.profile_url}">{$object_bucket.$activity_index.1.name}</a> 
	{else}
		<a href="{$object_bucket.$activity_index.0.profile_url}">{$object_bucket.$activity_index.0.name}</a> {$lang.and} 
		<a href="#">{$activity_meta.objects_count-1} {$lang.other|strtolower}</a>
		(
		{foreach from=$object_bucket.$activity_index key=kk item=obj name=objects_foreach}
			{if $kk > 0}
				<a href="{$obj.profile_url}">{$obj.name}</a>{if ! $smarty.foreach.objects_foreach.last},{/if}
			{/if}
		{/foreach}
		)
	{/if}
	
	
	{if $activity_meta.targets_count == 1}
		{if $activity_meta.target_type == $smarty.const.ACT_OBJ_ARTICLE}
			<a href="{$target_bucket.$activity_index.0.link}">{$target_bucket.$activity_index.0.title}</a>
		{/if}
		
		{if $activity_meta.target_type == $smarty.const.ACT_OBJ_VIDEO}
			
			<a href="{$target_bucket.$activity_index.0.video_href}">{$target_bucket.$activity_index.0.video_title}</a>
				<span class="pm-video-thumb pm-thumb-76 pm-thumb border-radius2">
			    <a href="{$target_bucket.$activity_index.0.video_href}" class="pm-thumb-fix pm-thumb-76"><span class="pm-thumb-fix-clip"><img src="{$target_bucket.$activity_index.0.thumb_img_url}" width="76"><span class="vertical-align"></span></span></a>
			    </span>
		{/if}
	{elseif $activity_meta.targets_count == 2}
	
	{else}
	
	{/if}
	</div><!-- .pm-ml-activity -->
	<div class="pm-ml-activity-date">
    <small>{$activity_meta.time_since} {$lang.ago}</small>
	{if $s_user_id == $actor_bucket.$activity_index.0.user_id}
		<a href="#" class="pull-right pm-li-activity-hide opac7" id="hide-activity-{$activity_meta.activity_id}" rel="tooltip" title="{$lang.hide_from_stream}"><i class="icon-remove"></i> </a>
	{/if}
    </div>
	<div class="clearfix"></div>
</li>