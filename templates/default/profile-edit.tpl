{include file='header.tpl' p="general"} 
<div id="wrapper">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span12 extra-space">
		<div id="primary">
        
		<h1>{$lang.edit_profile}</h1>
        <hr />
		{if $success == 1}
		<div class="alert alert-success">{$lang.ep_msg1}</div>
            {if $changed_pass == 1}
            <div class="alert alert-success">{$lang.ep_msg2}</div>
            <meta http-equiv="refresh" content="5;URL={$smarty.const._URL}">
            {/if}
		{include file='profile-edit-form.tpl'}
        {else}
		 	{if $errors.failure != ''}
		 		{$errors.failure}
			{/if}
        
        {if $nr_errors > 0}
        <div class="alert alert-danger">
            <ul class="subtle-list">
            {foreach from=$errors item=error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        </div>
        {/if} 
        {include file='profile-edit-form.tpl'}
		{/if}

		</div><!-- #primary -->
    </div><!-- #content -->
  </div><!-- .row-fluid -->
</div><!-- .container-fluid -->     
        
{include file='footer.tpl'} 