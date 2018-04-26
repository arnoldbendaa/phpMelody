{include file="header.tpl" p="article" tpl_name="article-read"} 
<div id="wrapper">
{if $show_addthis_widget == '1'}
{include file='widget-socialite.tpl'}
{/if}
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span8">
		<div id="primary" itemscope itemtype="http://schema.org/Article">
        {if is_array($article)}
        <article class="post">
        <header class="entry-header">
        {if $logged_in && $is_admin == 'yes'}
        <a href="{$smarty.const._URL}/{$smarty.const._ADMIN_FOLDER}/article_manager.php?do=edit&id={$article.id}" rel="tooltip" class="btn btn-mini pull-right" title="{$lang.edit} ({$lang._admin_only})" target="_blank">{$lang.edit}</a>
        {/if}
        <h1 class="entry-title" itemprop="name">{$article.title}</h1>
		<meta itemprop="interactionCount" content="UserComments:{$article.comment_count}"/>

		{if $article.meta._post_thumb_show != ''}
		<meta itemprop="thumbnailUrl" content="{$smarty.const._ARTICLE_ATTACH_DIR}/{$article.meta._post_thumb_show}"/>
		{/if}
        </header><!-- .entry-header -->
        <div class="pm-article-info">
            <strong>{$lang.articles_published}</strong>: <time class="entry-date" datetime="{$article.html5_datetime}" title="{$article.full_datetime}" pubdate>{$article.date}</time> {$lang.articles_by} <a href="{$article.author_profile_href}" itemprop="author">{$article.name}</a> 
            <strong>{$lang.articles_filedunder}</strong>: 
            {foreach from=$article.pretty_cats key=cat_name item=cat_href}
                <a href="{$cat_href}" title="{$cat_name}">{$cat_name}</a> 
            {/foreach} 
           - <strong>{$article.views_formatted}</strong> {$lang.views}
        </div>

        <div class="entry-post">
        {if $article.restricted == '1' && ! $logged_in}
        	<div class="restricted-video border-radius4">
			    <h2>{$lang.article_restricted_sorry}</h2>
				<p>{$lang.article_restricted_register}</p>
				<div class="restricted-login">
				{include file='user-auth-login-form.tpl'}
			    </div>
			</div>
        {else}
        	<div itemprop="articleBody">{$article.content}</div>
		{/if}
        </div>
        </article>
        {else}
        <article class="post">
        <h1>{$article}</h1>
        </article>
        {/if}

        <div class="row-fluid pm-author-data pm-article-author">       
            <div class="span2">
                <span class="pm-avatar"><a href="{$article.author_profile_href}"><img src="{$article.avatar_url}" height="50" width="50" alt="" class="img-polaroid" border="0"></a></span>
            </div>
            <div class="span10">
                <div class="pm-submit-data"><a href="{$article.author_profile_href}">{$article.name}</a></div>
                <div class="pm-author-about">{smarty_fewchars s=$article.author_about length=200}</div>         
                {if $smarty.const._MOD_SOCIAL && $logged_in == '1' && $article.author != $s_user_id}
                    {include file="user-subscribe-button.tpl" profile_data=$article profile_user_id=$article.author}
                {/if}
            </div>
        </div><!--.pm-author-data-->

        {if $ad_4 != ''}
        <div class="pm-ad-zone" align="center">{$ad_4}</div>
        {/if}

		<div class="clearfix"></div>
        {if !empty($article.tags) }
        <div class="pm-article-info"><strong>{$lang.tags}</strong>: 
            {foreach name=tag_links from=$article.tags key=k item=t}
             {if $smarty.foreach.tag_links.last}
              <a href="{$t.link}" title="{$t.tag}">{$t.tag}</a> 
             {else}
              <a href="{$t.link}" title="{$t.tag}">{$t.tag}</a>, 
             {/if}
            {/foreach}
        </div>
        <hr />
        {/if}

	{include file="comments.tpl" tpl_name="article-read" allow_comments=$article.allow_comments}
		
		</div><!-- #primary -->
        </div><!-- #content -->
        <div class="span4">
		<div id="secondary">
        {if is_array($related_articles) && count($related_articles) > 0}
        <div class="widget">
			<h4>{$lang.articles_related}</h4>
            <ul class="pm-ul-home-articles" id="pm-ul-home-articles">
            {foreach from=$related_articles item=related key=id}
                <li>
                    <article>
                    {if $related.meta._post_thumb_show != ''}
					<div class="pm-article-thumb">
                    <a href="{$related.link}"><img src="{$smarty.const._ARTICLE_ATTACH_DIR}/{$related.meta._post_thumb_show}" align="left" width="55" height="55" border="0" alt="{$related.title}"></a>
					</div>
                    {/if}

                    <h6 class="ellipsis"><a href="{$related.link}" class="pm-title-link">{smarty_fewchars s=$related.title length=92}</a></h6>
                    <div class="pm-article-preview">
                    {if $related.meta._post_thumb_show == ''}
                        <div class="minDesc">{smarty_fewchars s=$related.excerpt length=125}</div>
                    {else}
                        <div class="minDesc">{smarty_fewchars s=$related.excerpt length=125}</div>
                    {/if}
                    </div>
                    </article>
                </li>
			{/foreach}
            </ul>
        </div>
		{/if}
        
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


{include file="footer.tpl" tpl_name="article-read"}