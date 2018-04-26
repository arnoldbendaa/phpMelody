<?php
/**
 * Melodymods.com PHP Melody Mod framework - installer
 * 
 * This file handles the plugin installation. It checks the plugin files for required classes and handles the plugin install steps
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
 * @version 1.3.0 (August 6th 2015)
 * @package com.melodymods.modframework
 */
if(!isset($_POST['plugin']) || !preg_match('/^[a-z0-9_]+$/i',$_POST['plugin'])){
	header('location: mm_plugins.php');
	exit();
}
$showm = '12';
$_page_title = 'PHP Melody Mod framework installer';
include('header.php');
if(!is_admin()) restricted_access();
$frameworkmgr = new frameworkmanager();
?>

<div id="adminPrimary">
<div class="content">
<?php 
if(!file_exists(ABSPATH.'plugins/'.$_POST['plugin'].'.class.php')){
	echo '<div class="alert alert-error">Error: Plugin file <strong>'.$_POST['plugin'].'.class.php</strong> not found in /plugins/ directory!</div>';
	exit();
}
if(class_exists($_POST['plugin'])){
	echo '<div class="alert alert-error">Error: Plugin '.$_POST['plugin'].' already loaded!</div>';
	exit();
}
require_once(ABSPATH.'plugins/'.$_POST['plugin'].'.class.php');
if(!class_exists($_POST['plugin']) || !method_exists($_POST['plugin'],'install')){
	echo '<div class="alert alert-error">Error: Plugin '.$_POST['plugin'].' is not a valid plugin file!</div>';
	exit();
}
$mod = new $_POST['plugin'](false);
//Compare version
if(!version_compare(modframework::version,$mod->framework_minversion,'>=')){
	echo '<div class="alert alert-error">This plugin requires a newer version of the framework (at least '.$mod->framework_minversion.') than you have currently installed ('.modframework::version.').<br />Please update your modframework files via the <a href="http://melodymods.com">Framework Website</a></div>';
	exit();
}
//Check required mods
if(count($mod->requires_mods) !=0){
	$exit = false;
	foreach($mod->requires_mods as $req){
		if(!$modframework->is_installed($req)){
			$exit = true;
			echo '<div class="alert alert-error">This plugin requires the plugin <strong>'.$req.'</strong> to be installed. Please install the plugin <strong>'.$req.'</strong> before attempting to install this plugin.<br />
			For help contact support at support@melodymods.com</div>';
		}
	}
	if($exit) exit();
}
//Check required files
if(count($mod->requires_files) !=0){
	$exit = false;
	foreach($mod->requires_files as $file){
		if(substr($file,0,5) == 'admin' && _ADMIN_FOLDER != 'admin'){
			$file = _ADMIN_FOLDER.substr($file,5);
		}
		if(!file_exists(ABSPATH.$file)){
			$exit = true;
			echo '<div class="alert alert-error">This plugin requires the file <strong>'.$file.'</strong> to be uploaded on your website. Please upload all the files from the plugin package to your website before attempting to install this plugin.<br />
			For help contact support at support@melodymods.com</div>';
		}
	}
	if($exit) exit();
}
if((!isset($_POST['acceptlicence']) || $_POST['acceptlicence'] != 1) && $mod->licence != 'none' && $mod->licence != ''){
	//User has to accept licence first
	echo '<div class="alert alert-info">You have to accept the licence agreement for this plugin before you can install it</div>';
	echo '<form method="post" action="mm_install.php"><input type="hidden" name="plugin" value="'.$_POST['plugin'].'" />';
	echo '<div class="well">';
	switch($mod->licence){
		case 'mm_freeos':
			?>
			<strong>Melodymods.com Open Source licence (for free plugins)</strong><br />
			Our free open source plugins are provided as a gift to the PHP Melody community. There are several terms that apply to these plugins (called hereafter <em>the plugin</em>) to prevent abuse.<br />
			<br />The following terms apply to our free open source plugins:<br /><br />
			You are <strong>allowed</strong> to:
			<ul><li>Install the plugin on as many sites as you want</li>
			<li>Modify this plugin for private use</li>
			<li>Freely publish any modifications to the PHP Sugar forums</li>
			<li>Give away the plugin to any other Verified PHP Sugar customer</li>
			</ul>
			You are <strong>NOT ALLOWED</strong> to:
			<ul><li>Sell the plugin</li>
			<li>Charge people for installing this plugin or offer this as a (paid) service</li>
			<li>Modify the plugin and commercialy distribute this modification</li></ul>
			In short: You have the right to use this plugin for yourself and to give it away to others for free. You are not allowed to try and make money from this plugin, either by selling it, modifying it and selling these modifications or by offering others to install it for a fee.
			<br />For more information about the terms and licences of Melodymods.com plugins, see:<br />
			<a href="http://melodymods.com/support/terms.htm">http://melodymods.com/support/terms.htm</a><br />
			<a href="http://melodymods.com/support/licences.htm">http://melodymods.com/support/licences.htm</a><br />
			<?php 
		break;
		case 'custom':
			echo $mod->licencetext;
		break;
		case 'mm_os':
		default:
			?>
			<strong>Melodymods.com Open Source licence</strong><br />
			<strong>This is NOT a free plugin. You may only install this plugin if you purchased it from Melodymods.com</strong><br />This plugin is distributed in an open source variant.<br />
			This is because we do not want to restrict you to modify this or learn from our work.<br /><br />You are <strong>allowed</strong> to:
			<ul><li>Install this plugin on up to 2 (two) domains that you own</li>
			<li>Modify this plugin for private use</li></ul><br />
			You are <strong>NOT ALLOWED</strong> to:
			<ul><li>Install this plugin on more than 2 domains without permission from Melodymods.com</li>
			<li>Install this plugin when you did not purchase it from Melodymods.com</li>
			<li>Install this plugin on websites which don't belong to you</li>
			<li>Sell or give away this plugin, any part of it, to anyone</li>
			<li>Distribute or sell any modifications of this plugin</li></ul>
			<br />
			In short: You have the right to use this plugin for yourself, do not give it away or sell it. This includes giving third parties open access to your website.<br />
			For more information about the terms and licences of Melodymods.com plugins, see:<br />
			<a href="http://melodymods.com/support/terms.htm">http://melodymods.com/support/terms.htm</a><br />
			<a href="http://melodymods.com/support/licences.htm">http://melodymods.com/support/licences.htm</a><br />
			<?php 	
		break;
	}
	echo '</div>';
	echo '<label for="ckbx">I have read and agree to these terms <input id="ckbx" type="checkbox" name="acceptlicence" value="1" /></label><br />
	<input type="submit" class="btn btn-success" value="Install plugin" /></div><!-- .content -->
</div><!-- .primary -->';
require_once('footer.php');
	exit();
}
if(!isset($_POST['step']) || !is_numeric($_POST['step'])) $step = 1; else $step = (int) $_POST['step'];
/* 
 * Go do install stuff
 * It is up to the mod itself how to handle this. Simple mods might require only simple things, more advanced mods might have multiple steps
 * The next step should always be returned by the install function, or it should return true or false, to indicate that the installation is complete or (permanently) failed.
 */
$msg = '';
$out = $mod->install($step,$msg);
if($out === false){
	echo '<div class="alert alert-error">Install failed: '.$msg.'<br /><br />Please contact the plugin author to resolve this problem if it persists</div>';
	mysql_query("DELETE FROM mm_plugins WHERE plugin = '".$_POST['plugin']."'"); //Remove to be sure
	echo '</div><!-- .content -->
</div><!-- .primary -->';
require_once('footer.php');
	exit();
}
if($out === true){
	echo '<div class="alert alert-success">The plugin has been succesfully installed. Additional information: '.$msg.'<br /><a href="mm_plugins.php">Back to plugin manager</a></div>';
	echo '</div><!-- .content -->
</div><!-- .primary -->';
require_once('footer.php');
	exit();
}
//Else it is the step
echo '<form action="mm_install.php" method="post"><input type="hidden" name="plugin" value="'.$_POST['plugin'].'" /><input id="ckbx" type="hidden" name="acceptlicence" value="1" />
<input type="hidden" name="step" value="'.(int) $out.'" />'.$msg.'<br /><input type="submit" value="Continue" /></form>    </div><!-- .content -->
</div><!-- .primary -->';
require_once('footer.php');
?>