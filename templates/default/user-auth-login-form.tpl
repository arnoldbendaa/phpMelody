<form class="form-vertical" name="login_form" id="login-form" method="post" action="{$smarty.const._URL}/login.php">
  <fieldset>
  <div class="control-group">
    <label class="control-label" for="username">{$lang.your_username_or_email}</label>
    <div class="controls"><input type="text" class="hocusfocus input-large" id="hocusfocus" name="username" value="{$inputs.username}"></div>
  </div>
  <div class="control-group">
    <label class="control-label" for="pass">{$lang.password}</label>
    <div class="controls"><input type="password" class="input-large" id="pass" name="pass" maxlength="32" autocomplete="off"></div>
  </div>


    <div class="">
        <div class="controls">
        <button type="submit" name="Login" value="{$lang.login}" class="btn btn-blue" data-loading-text="{$lang.login}">{$lang.login}</button>
        <span class="signup"><small><a href="{$smarty.const._URL}/login.{$smarty.const._FEXT}?do=forgot_pass">{$lang.forgot_pass}</a></small></span>
        </div>
    </div>
  </fieldset>
</form>