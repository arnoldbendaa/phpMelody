<span id="preview_status"></span>
<ul class="pm-activity-stream">
	{foreach from=$activity_meta_bucket key=activity_index item=activity_meta}
      	{include file='activity-stream-item.tpl'}
    {foreachelse}
        <li>{$lang.my_activity_empty}</li>
    {/foreach}
</ul>
{if $total_activities == $smarty.const.ACTIVITIES_PER_PAGE}
	<div class="clearfix"></div>
    <span id="btn_activity_stream_load_more"></span>
{/if}