<form class="form-vertical" name="forgot-pass" id="reset-form" method="post" action="{$smarty.const._URL}/login.php?do=forgot_pass">
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="input01">{$lang.your_username_or_email}</label>
      <div class="controls"><input type="text" class="input-large" name="username_email" placeholder="" value="{$inputs.username_email}"></div>
    </div>
    <div class="">
        <div class="controls">
        <button type="submit" name="Send" value="{$lang.submit_send}" class="btn btn-blue" data-loading-text="{$lang.submit_send}">{$lang.submit_send}</button>
        </div>
    </div>
  </fieldset>
</form>