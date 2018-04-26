{include file='header.tpl' p="general"} 
<div id="wrapper">
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12 extra-space">
	  <div id="primary">
        <div class="tab-content">
          <div class="tab-pane active" id="contact-form">
          <h1>{$lang.contact_us}</h1>
          <p class="lead"></p>
            {if isset($err_captcha)}
            <div class="alert alert-danger">{$err_captcha}</div>
            {/if}
            {if isset($err_email)}
            <div class="alert alert-danger">{$err_email}</div>
            {/if}
            {if isset($err_msg)}
            <div class="alert alert-danger">{$err_msg}</div>
            {/if}
            {if isset($confirm_send)}
            <div class="alert alert-success">
            {$lang.contact_msg1}
            </div>
            {/if}
          	<hr />
	    {if !isset($confirm_send)}
            <form class="form-horizontal" method="post" action="{$smarty.const._URL}/contact_us.php">
            <fieldset>
              <div class="control-group">
                <label class="control-label" for="your_name">{$lang.your_name}</label>
                <div class="controls"><input type="text" class="input-large" name="your_name" value="{if $logged_in}{$s_name}{else}{$smarty.post.your_name}{/if}"></div>
              </div>
              <div class="control-group">
                <label class="control-label" for="your_email">{$lang.your_email}</label>
                <div class="controls">
                  <input type="email" class="input-large" name="your_email" value="{if $logged_in}{$s_email}{else}{$smarty.post.your_email}{/if}">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="importance">{$lang.importance}</label>
                <div class="controls">
                  <select name="importance">
                    <option value="{$lang.low}">{$lang.low}</option>
                    <option value="{$lang.normal}" selected="selected">{$lang.normal}</option>
                    <option value="{$lang.high}">{$lang.high}</option>
                    <option value="{$lang.urgent}">{$lang.urgent}</option>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="select">{$lang.in_regard}</label>
                <div class="controls">
                  <select name="select">
                    <option selected="selected">{$lang.select}</option>
                    <option>{$lang.bug_report}</option>
                    <option>{$lang.suggestions}</option>
                    <option>{$lang.general_q}</option>
                    <option>{$lang.other}</option>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="msg">{$lang.your_message}</label>
                <div class="controls">
                  <textarea id="msg" name="msg" rows="4" class="input-xxlarge" placeholder="">{$smarty.post.msg}</textarea>
                </div>
              </div>
				{if $logged_in == 0}
				{if $spambot_prevention == 'securimage'}
				<div class="control-group">
					<div class="controls">
						<input type="text" name="imagetext" class="input-large" autocomplete="off" placeholder="{$lang.enter_captcha}">
						<img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="image" align="absmiddle" alt="" class="img-rounded">
						<button class="btn btn-small btn-link" onclick="document.getElementById('image').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false">
						<i class="icon-refresh"></i>
						</button>
					</div>
				</div>
				{/if}
				{if $spambot_prevention == 'recaptcha'}
				<div class="control-group">
					<div class="controls">
						{$recaptcha_html}
					</div>
				</div>
				{/if}
				{/if}
              <div class="">
                <div class="controls">
                  <button type="submit" name="Submit" value="{$lang.submit_send_msg}" class="btn btn-blue" data-loading-text="{$lang.submit_send_msg}">{$lang.submit_send_msg}</button>
                </div>
              </div>
            </fieldset>
            </form>
	  {/if}
          </div>
          <!-- end pm-contact --> 
        </div>
	</div>
        <!-- end tag-content --> 
      </div>
      <!-- #sidebar --> 
    </div>
    <!-- .row-fluid --> 
  </div>
  <!-- .container-fluid -->
{include file='footer.tpl'} 