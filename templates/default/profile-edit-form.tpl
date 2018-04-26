<form class="form-horizontal" name="register-form" id="register-form" method="post" action="{$form_action}">
  <fieldset>
    <legend>{$lang.about_me}</legend>
    <div class="control-group">
      <label class="control-label" for="name">{$lang.your_name}</label>
      <div class="controls"><input type="text" class="input-large" name="name" {if isset($inputs.name)}value="{$inputs.name}"{else}value="{$userdata.name}"{/if}></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="email">{$lang.your_email}</label>
      <div class="controls">
      <input type="text" class="input-large" name="email" {if isset($inputs.email)}value="{$inputs.email}"{else}value="{$userdata.email}"{/if}>
      <a href="#" rel="tooltip" title="{$lang.safe_email}"><i class="icon-info-sign"></i> </a>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="gender">{$lang.gender}</label>
      <div class="controls">
      <select name="gender">
      <option value="male" {if $inputs.gender == 'male'}selected="selected"{/if}>{$lang.male}</option>
      <option value="female"{if $inputs.gender == 'female'}selected="selected"{/if}>{$lang.female}</option>
      </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="country">{$lang.country}</label>
      <div class="controls">
      {if $show_countries_list}
      <select name="country" size="1" >
      <option value="-1">{$lang.select}</option>
          {foreach from=$countries_list key=k item=v}
          <option value="{$k}" {if $inputs.country == $k}selected{elseif $userdata.country == $k}selected{/if}>{$v}</option>
          {/foreach}
      </select>
      {/if}
        </select>
      </div>
    </div>
    		  {if isset($mm_profile_info_inject)}{$mm_profile_info_inject}{/if}
    
    <div class="control-group">
      <label class="control-label" for="aboutme">{$lang.about_me}</label>
      <div class="controls"><textarea name="aboutme" class="">{if isset($inputs.aboutme)}{$inputs.aboutme}{elseif isset($userdata.about)}{$userdata.about}{/if}</textarea></div>
    </div>
  </fieldset>

  <fieldset>
    <legend>{$lang._social}</legend>
    <div class="control-group">
      <label class="control-label" for="website">{$lang.profile_social_website}</label>
      <div class="controls"><input type="text" class="input-large" name="website" {if isset($inputs.website)}value="{$inputs.website}"{else}value="{$userdata.social_links.website}"{/if} placeholder="http://"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="facebook">{$lang.profile_social_fb}</label>
      <div class="controls"><input type="text" class="input-large" name="facebook" {if isset($inputs.facebook)}value="{$inputs.facebook}"{else}value="{$userdata.social_links.facebook}"{/if} placeholder="https://"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="twitter">{$lang.profile_social_twitter}</label>
      <div class="controls"><input type="text" class="input-large" name="twitter" {if isset($inputs.twitter)}value="{$inputs.twitter}"{else}value="{$userdata.social_links.twitter}"{/if} placeholder="https://"></div>
    </div>
	<div class="control-group">
      <label class="control-label" for="instagram">{$lang.profile_social_instagram}</label>
      <div class="controls"><input type="text" class="input-large" name="instagram" {if isset($inputs.instagram)}value="{$inputs.instagram}"{else}value="{$userdata.social_links.instagram}"{/if} placeholder="https://"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="google_plus">{$lang.profile_social_google_plus}</label>
      <div class="controls"><input type="text" class="input-large" name="google_plus" {if isset($inputs.google_plus)}value="{$inputs.google_plus}"{else}value="{$userdata.social_links.google_plus}"{/if} placeholder="https://"></div>
    </div>
    {if isset($mm_profile_webfields_inject)}{$mm_profile_webfields_inject}{/if}
  </fieldset>

  <fieldset>
    <legend>{$lang.change_pass}</legend>
    <div class="control-group">
      <label class="control-label" for="current_pass">{$lang.existing_pass}</label>
      <div class="controls"><input type="password" class="input-large" name="current_pass" maxlength="32"></div>
    </div>
    <div class="control-group">
      <label class="control-label" for="new_pass">{$lang.new_pass}</label>
      <div class="controls">
      <input type="password" class="input-large" name="new_pass" maxlength="32">
      <p class="help-block"><small>{$lang.ep_msg5}</small></p>
      </div>
    </div>
    
    <div class="">
        <div class="controls">
        <button type="submit" name="save" value="{$lang.submit_save}" class="btn btn-success" data-loading-text="{$lang.submit_save}">{$lang.submit_save}</button>
        </div>
    </div>
  </fieldset>
</form>