{include file='header.tpl' p="general"}
<div id="wrapper">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12 extra-space">
                <div id="primary">
                    <h1>{$smarty.const._SITENAME} {$lang.members}</h1>

                    <div id="sorting">
                        <div class="btn-group btn-group-sort">
                            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                {if $gv_sortby == ''}{$lang.sorting}{/if} {if $gv_sortby == 'name'}{$lang.name}{/if}{if $gv_sortby == 'lastseen'}{$lang.last_seen}{/if}{if $gv_sortby == 'online'}{$lang.whois_online}{/if}
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li style="text-align: right;"><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?page={$gv_pagenumber}&sortby=name" rel="nofollow" class="{if $gv_sortby == 'name'}selected{/if}">{$lang.name}</a></li>
                                <li style="text-align: right;"><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?page={$gv_pagenumber}&sortby=lastseen" rel="nofollow" class="{if $gv_sortby == 'lastseen'}selected{/if}">{$lang.last_seen}</a></li>
                                <li style="text-align: right;"><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?do=online&sortby=online" rel="nofollow" class="{if $gv_sortby == 'online'}selected{/if}">{$lang.whois_online}</a></li>
                            </ul>
                        </div>
                    </div>
                    <ul class="pm-ul-memberlist">
                        {foreach from=$user_list key=k item=user_data}
                            <li>
				<span class="pm-ml-username">
				<a href="{$user_data.profile_url}">{$user_data.name}</a>{if $user_data.user_is_banned} <span class="label label-important">{$lang.user_account_banned_label}</span>{/if}
                    {if $user_data.channel_verified == 1 && $smarty.const._MOD_SOCIAL}<a href="#" rel="tooltip" title="{$lang.verified_channel}"><img src="{$smarty.const._URL}/templates/{$smarty.const._TPLFOLDER}/img/ico-verified.png" /></a>{/if}
                    {if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $user_data.id != $s_user_id}
                        {if $user_data.is_following_me}
                            <span class="label pm-follows">{$lang.subscriber}</span>
                        {/if}
                    {/if}
                </span>
                                <span class="pm-ml-avatar"><a href="{$user_data.profile_url}"><img src="{$user_data.avatar_url}" alt="{$user_data.username}" width="60" height="60" border="0" class="img-polaroid"></a></span>
                                <span class="pm-ml-country"><small><i class="icon-map-marker"></i> {$user_data.country_label}</small></span>
                                <span class="pm-ml-lastseen"><small><i class="icon-eye-open"></i> {$user_data.last_seen}</small></span>

                                <div class="pm-ml-buttons">
                                    {if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $user_data.id != $s_user_id}
                                        {include file="user-subscribe-button.tpl" profile_data=$user_data profile_user_id=$user_data.id}
                                    {/if}
                                </div>
                                <div class="clearfix"></div>
                            </li>
                            {foreachelse}
                            {if $problem != ''}
                                {$problem}
                            {else}
                                {$lang.memberlist_msg2}
                            {/if}
                        {/foreach}
                    </ul>

                    <!-- pagination -->
                    <div class="clearfix"></div>
                    {if is_array($pagination)}
                        <div class="pagination pagination-centered">
                            <ul>
                                {foreach from=$pagination key=k item=pagination_data}
                                    <li{foreach from=$pagination_data.li key=attr item=attr_val} {$attr}="{$attr_val}"{/foreach}>
                                    <a{foreach from=$pagination_data.a key=attr item=attr_val} {$attr}="{$attr_val}"{/foreach}>{$pagination_data.text}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div><!-- #primary -->
            </div><!-- #content -->
        </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
{include file='footer.tpl'}