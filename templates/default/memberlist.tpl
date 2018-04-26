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
            			
			<div class="pagination pagination-centered">
			<ul>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}" rel="nofollow">{$lang.memberlist_all}</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=a" rel="nofollow">A</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=b" rel="nofollow">B</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=c" rel="nofollow">C</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=d" rel="nofollow">D</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=e" rel="nofollow">E</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=f" rel="nofollow">F</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=g" rel="nofollow">G</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=h" rel="nofollow">H</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=i" rel="nofollow">I</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=j" rel="nofollow">J</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=k" rel="nofollow">K</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=l" rel="nofollow">L</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=m" rel="nofollow">M</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=n" rel="nofollow">N</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=o" rel="nofollow">O</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=p" rel="nofollow">P</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=q" rel="nofollow">Q</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=r" rel="nofollow">R</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=s" rel="nofollow">S</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=t" rel="nofollow">T</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=u" rel="nofollow">U</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=v" rel="nofollow">V</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=w" rel="nofollow">W</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=x" rel="nofollow">X</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=y" rel="nofollow">Y</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=z" rel="nofollow">Z</a></li>
			<li><a href="{$smarty.const._URL}/memberlist.{$smarty.const._FEXT}?letter=other" rel="nofollow">#</a></li>
			</ul>
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