{if $allow_registration == '1'}
<form class="form-horizontal" id="register-form" name="register-form" method="post" action="{$smarty.const._URL}/register.php">
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="name">{$lang.your_name}</label>
      <div class="controls"><input type="text" class="input-large" name="name" value="{$inputs.name}"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="username">{$lang.username}</label>
      <div class="controls"><input type="text" class="input-large" name="username" value="{$inputs.username}"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="email">{$lang.your_email}</label>
      <div class="controls"><input type="email" class="input-large" id="email" name="email" value="{$inputs.email}" autocomplete="off"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="pass">{$lang.password}</label>
      <div class="controls"><input type="password" class="input-large" id="pass" name="pass" maxlength="32" autocomplete="off"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="confirm_pass">{$lang.password_retype}</label>
      <div class="controls">
      <input type="password" class="input-large" id="confirm_pass" name="confirm_pass" maxlength="32">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="country">{$lang.country}</label>
      <div class="controls">
		{if $show_countries_list}
		<select name="country" size="1" >
		<option value="-1">{$lang.select}</option>
			{foreach from=$countries_list key=k item=v}
			<option value="{$k}" {if $inputs.country == $k}selected{/if}>{$v}</option>
			{/foreach}
		</select>
		{/if}
		<input type="text" name="website" class="input-large botmenot" maxlength="32">
      </div>
    </div>
{if isset($mm_register_fields_inject)}{$mm_register_fields_inject}{/if}
	{if $spambot_prevention == 'securimage'}
    <div class="control-group">
        <div class="controls">
        	<input type="text" name="imagetext" class="input-large" autocomplete="off" placeholder="{$lang.enter_captcha}">
            <img src="{$smarty.const._URL}/include/securimage_show.php?sid={echo_securimage_sid}" id="image" align="absmiddle" alt="" class="img-rounded">
            <button class="btn btn-link btn-large" onclick="document.getElementById('image').src = '{$smarty.const._URL}/include/securimage_show.php?sid=' + Math.random(); return false">
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
	
    <div class="control-group">
      <div class="controls">
      <label class="checkbox">
      <input type="checkbox" class="checkbox" id="agree" name="agree" {if $inputs.agree == 'on'}checked="checked"{/if}> <span class="help-inline">{$lang.i_agree_with} <a data-toggle="modal" href="#terms" id="element" >{$lang.terms_of_agreement}</a></span>
      </label>
      </div>
    </div>
    
    <div class="">
        <div class="controls">
        <input type="hidden" class="input-large" name="gender" value="male">
        <button type="submit" name="Register" value="{$lang.register}" class="btn btn-blue" data-loading-text="{$lang.register}">{$lang.register}</button>
        </div>
    </div>
  </fieldset>
</form>
{else}
{$lang.registration_closed}
{/if}
