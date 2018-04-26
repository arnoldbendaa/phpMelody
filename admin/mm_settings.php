<?php
/**
 * Melodymods.com PHP Melody Mod framework - plugin settings
 * 
 * Parses plugin settings and allows modification of these settings
 * 
 * This framework is provided free of charge for people who want to create
 * modifications for non-commercial purposes. If you do want to commercially
 * distribute plugins built on or relying on this framework, please contact us
 * at info@melodymods.com for licencing.
 * 
 * You not allowed to sell this framework or any of its code, whether in 
 * the original, modified or extended state without prior written permission
 * from Melodymods.com / Sano Webdevelopment registered under number 27373275 
 * at the Chamber of Commerce in The Netherlands. 
 * 
 * Additionally you are not allowed to use this framework to create
 * and distribute any mods which mimic the functionality of any mods
 * sold by melodymods.com
 * 
 * @author Dirk-jan Mollema - Melodymods.com
 * @license All rights reserved
 * @version 1.1.0 (14  Nov 2012)
 * @package com.melodymods.modframework
 */
$showm = '12';
$_page_title = 'PHP Melody Mod framework - plugin settings';
include('header.php');
$frameworkmgr = new frameworkmanager();
/*
 * Start real page
 */
?>
<div id="adminPrimary">
<div class="content">
<?php 
if(isset($_POST['mod'])) $_GET['mod'] = $_POST['mod'];
//Get plugin
if(!isset($_GET['mod'])){
	echo 'Direct access denied. Please select a plugin.';
	exit();
}
$sql = "SELECT plugin FROM mm_plugins WHERE plugin = '".secure_sql($_GET['mod'])."' LIMIT 1";
$res = mysql_query($sql);
if(mysql_num_rows($res) != 1){
	exit('This plugin is not installed. Please install this plugin before trying to configure it.');
}
list($plugin) = mysql_fetch_row($res);
/*
 * Process submitted settings if any
 */
if(isset($_POST['update'])){
	$sql = "SELECT setting, `value`, `description`, valid FROM mm_settings WHERE plugin = '".$plugin."' AND editable = 1";
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0){
		while(list($setting,$value,$description,$valid) = mysql_fetch_row($res)){
			if(!isset($_POST[$setting])) continue;
			$sql = "UPDATE mm_settings SET value = ";
			$valid = explode('|',$valid);
			switch($valid[0]){
				case 'string':
				default:
					$sql.= "'".secure_sql($_POST[$setting])."'";
				break;
				case 'bool':
					$_POST[$setting] = (bool) $_POST[$setting];
				case 'int':
					$sql.= (int) $_POST[$setting];
				break;
			}
			$sql.=" WHERE plugin = '".$plugin."' AND setting = '".$setting."' LIMIT 1";
			mysql_query($sql);
		}
	}
}
$sql = "SELECT setting, value, description, valid FROM mm_settings WHERE plugin = '".$plugin."' AND editable = 1";
$res = mysql_query($sql);
echo mysql_error();
if(mysql_num_rows($res) > 0){
	?>
    
<h2 class="sub-head-settings">Plugin Settings</h2>
<form action="mm_settings.php" method="post">
<input type="hidden" name="mod" value="<?php echo $plugin;?>" />
<input type="hidden" name="update" value="1" />
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables pm-tables-settings">
<tr><th width="10%">Setting</th><th width="20%">Value</th><th>Description</th>
<?php 
while(list($setting,$value,$description,$valid) = mysql_fetch_row($res)){
	$valid = explode('|',$valid);
	switch($valid[0]){
		case 'string':
		case 'int':
		default:
			echo '<tr><td>'.$setting.'</td><td><input type="text" value="'.htmlentities($value,null,'UTF-8').'" ';
			if(isset($valid[1]) && is_numeric($valid[1])){
				echo 'maxlength="'.(int) $valid[1].'"';
				if($valid[1] < 10) echo 'size="7"';
			}
			echo 'name="'.$setting.'" /></td><td>'.$description.'</td></tr>';
		break;
		case 'bool':
			echo '<tr><td>'.$setting.'</td><td><input type="radio" name="'.$setting.'" value="0" '.((((int) (bool) $value) == 0)? 'checked="checked" ':'')."> No ";
			echo '<input type="radio" name="'.$setting.'" value="1" '.((((int) (bool) $value) == 1)? 'checked="checked" ':'')."> Yes ";
			echo '<td>'.$description.'</td></td></tr>';
		break;
		/* Rest to follow */
	}
}
?>
</table>
<div class="clearfix"></div>
<div id="stack-controls" class="list-controls">
<div class="pull-left">
<a href="mm_plugins.php" class='btn btn-small'>&larr; Return to Plugins page</a>
</div>

<input class='btn btn-small btn-success' type="submit" value="Save" />
</div><!-- #list-controls -->
</form>
	<?php 
}else{
	echo('This plugin does not have any configurable settings.<a href="mm_plugins.php">Back</a>');
}
?>
</div></div>
<?php
include('footer.php');
?>