{include file="header.tpl" p="article" tpl_name="article-category"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span8">
        <div id="primary">
        <h1>{$article_h2}</h1>
		<hr />
        {if $cat_id > 0 && $categories.$cat_id.description}
        <div class="pm-browse-desc">
        {$categories.$cat_id.description} 
        <div class="clearfix"></div>
        </div>
        {/if}

        <ul class="pm-ul-browse-articles">
        {if ! is_array($articles)}
            {$articles}
        {else}
            {foreach from=$articles key=id item=article}
            <li {if $article.featured == '1'}class="sticky-article"{/if}>
            <article class="post">
            <header class="entry-header">
            {if $logged_in && $is_admin == 'yes'}
            <span class="pull-right"><a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/article_manager.php?do=edit&id={$article.id}" title="{$lang.edit}" target="_blank" class="btn btn-mini">{$lang.edit}</a></span>
            {/if}
            <h2 class="entry-title">
            <a href="{$article.link}" title="{$article.title}">{$article.title}</a>
            </h2>
            </header><!-- .entry-header -->
            
            <div class="pm-article-info">
            <time class="entry-date" datetime="{$article.html5_datetime}" title="{$article.full_datetime}" pubdate>{$article.date}</time> 
            {$lang.articles_by} <a href="{$article.author_profile_href}">{$article.name}</a> / <strong>{$article.views_formatted}</strong> {$lang.views}
            </div>
            
            <div class="entry-summary">
			{if $article.restricted == '1' && ! $logged_in}
				{$lang.article_restricted_sorry}
			{else}
				{$article.content}
			{/if}
            <span class="entry-summary-nav more-link"><a href="{$article.link}">{$lang.read_more} &raquo;</a>
            </div>
            </article>
            </li>
            {/foreach}
        {/if}
        </ul>
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
        <div class="span4">
		<div id="secondary">

		<div class="widget" id="sticky">
		<h4>{$lang._categories}</h4>
		<ul class="pm-browse-ul-subcats">
        {$article_categories}
        </ul>
        </div>
        
		</div><!-- #secondary -->
        </div><!-- #sidebar -->
      </div><!-- .row-fluid -->
    </div><!-- .container-fluid -->
{include file="footer.tpl" tpl_name="article-category"} 