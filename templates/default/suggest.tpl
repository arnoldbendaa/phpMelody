{include file='header.tpl' no_index='1' p="general"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary" class="extra-space">
        
        <h1>{$lang.suggest}</h1>
        <hr />
        
        {if $success == 3}
            <div class="alert alert-info">
            {$lang.suggest_msg1}
            </div>
        {/if}
        {if $success == 4}
            <div class="alert alert-info">
            {$lang.suggest_msg2}
            </div>
        {/if}
        {if $success == 5}
            <div class="alert alert-danger">
            {$lang.suggest_msg3}
            </div>
        {/if}
        {if $success == 1}
            <div class="alert alert-success">
            {$lang.suggest_msg4}
            <a href="suggest.{$smarty.const._FEXT}">{$lang.add_another_one}</a> | <a href="index.{$smarty.const._FEXT}">{$lang.return_home}</a>
            </div>
        {else}
        
        {if count($errors) > 0}
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="subtle-list">
            {foreach from=$errors item=v}
            	<li>{$v}</li>
            {/foreach}
            </ul>
        </div>
        {/if}

        <form class="form-horizontal" id="suggest-form" name="suggest-form" method="post" action="{$form_action}">
          <fieldset>
            <div class="control-group">
              <label class="control-label" for="pm_sources">{$lang._videourl}</label>
              <div class="controls">
                <input type="text" class="span8" name="yt_id" value="{$smarty.post.yt_id}" placeholder="http://"> <span class="hide" id="loading-gif-top"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="" height="" alt=""></span>
              </div>
            </div>
            <div class="hide" id="suggest-video-extra">
                <div class="control-group">
                  <label class="control-label" for="video_title">{$lang.video}</label>
                  <div class="controls">
                  <input type="text" class="span5" name="video_title" value="{$smarty.post.video_title}">
                  <div id="video-thumb-placeholder"></div>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="category">{$lang.category}</label>
                  <div class="controls">
                    {$categories_dropdown}
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="description">{$lang.description}</label>
                  <div class="controls">
                    <textarea name="description" class="span5" rows="5">{if $smarty.post.description}{$smarty.post.description}{/if}</textarea>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="tags">{$lang.tags}</label>
                  <div class="controls">
                    <div class="tagsinput">
                      <input id="tags_suggest" type="text" class="tags" name="tags" value="{$smarty.post.tags}">  <span class="help-inline"><a href="#" rel="tooltip" title="{$lang.suggest_ex}"><i class="icon-info-sign"></i></a></span>
                    </div>
                  </div>
                </div>
                <div class="">
                  <div class="controls">
                    <button class="btn btn-success" name="Submit" id="Submit" value="{$lang.submit_submit}" type="submit">{$lang.submit_submit}</button> <span class="hide" id="loading-gif-bottom"><img src="{$smarty.const._URL}/templates/{$template_dir}/img/ajax-loading.gif" width="" height="" alt=""></span>
                  </div>
                </div>
            </div><!-- #suggest-video-extra -->
            <div class="alert alert-warning hide" id="ajax-error-placeholder"></div>
            <div class="alert alert-success hide" id="ajax-success-placeholder"></div>
			<input type="hidden" name="source_id" value="-1" />
			<input type="hidden" name="p" value="suggest" />
			<input type="hidden" name="do" value="submitvideo" />
          </fieldset>
        </form>
        {/if}
		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
{include file='footer.tpl'} 