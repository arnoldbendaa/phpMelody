<?php
/**
 * Melodymods.com PHP Melody Mod framework - uninstall 
 * 
 * This file handles uninstalling plugins. It checks the plugin files for added tables and settings and removes those
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
if(!isset($_POST['plugin']) && isset($_GET['mod'])) $_POST['plugin'] = $_GET['mod'];
if(!isset($_POST['plugin']) || !preg_match('/^[a-z0-9_]+$/i',$_POST['plugin'])){
	header('location: mm_plugins.php');
	exit();
}
$showm = '12';
$_page_title = 'PHP Melody - Uninstall plugin';
include('header.php');
if(!is_admin()) restricted_access();
$frameworkmgr = new frameworkmanager();
?>

<div id="adminPrimary">
<div class="content">
	<h2>Uninstall plugin</h2>

<?php 
if(!$modframework->is_installed($_POST['plugin'])){
	echo '<div class="alert alert-error">This plugin is not currently installed. Uninstall is not possible.</div>';
	exit();
}
if(!file_exists(ABSPATH.'plugins/'.$_POST['plugin'].'.class.php')){
	$pluginexists = false;
	//echo '<div class="alert alert-warning">Error: Plugin file <strong>'.$_POST['plugin'].'.class.php</strong> not found in /plugins/ directory!</div>';
}else{
	require_once(ABSPATH.'plugins/'.$_POST['plugin'].'.class.php');
	if(!class_exists($_POST['plugin']) || !method_exists($_POST['plugin'],'install')){
		$pluginexists = false;
		//echo '<div class="alert alert-warning">Error: Plugin '.$_POST['plugin'].' is not a valid plugin file!</div>';
		//exit();
	}else{
		$mod = new $_POST['plugin'](false);
		$pluginexists = true;
	}
}

if(isset($_POST['uninstall'])){
	if(!csrfguard_check_referer('_uninstall_'.$_POST['plugin'])){
		echo '<div class="alert alert-error">CSRF validation failed, please go back and submit the form again.</div>';
		exit();
	}
	if($pluginexists){
		$mod->uninstall();
	}
	$error = '';
	$out = $frameworkmgr->uninstall($_POST['plugin'], $error);
	if($out){
		echo '<div class="alert alert-success">The plugin has been succesfully uninstalled.<br /><a href="mm_plugins.php">Back to plugin manager</a></div>';
	}else{
		echo '<div class="alert alert-error">Plugin uninstall failed! Error: '.$error.'. <br /><a href="mm_plugins.php">Back to plugin manager</a></div>';
	}
	echo '</div><!-- .content -->
		</div><!-- .primary -->';
}else{
	if(!$pluginexists){
		echo '<div class="alert alert-warning">Plugin file <strong>'.$_POST['plugin'].'.class.php</strong> could not be found or does not contain a valid plugin. Uninstall is still possible, but some parts of the plugin may not be removed correctly.</div>';
	}
	echo '<form action="mm_uninstall.php" method="post">';
	echo csrfguard_form('_uninstall_'.$_POST['plugin']);
	echo '<input type="hidden" name="plugin" value="'.$_POST['plugin'].'" />Are you sure you want to uninstall this plugin? This will remove all the plugin preferences and added data.<br /><br /><input type="submit" name="uninstall" class="btn btn-danger" value="Uninstall" /></form>    </div><!-- .content -->
</div><!-- .primary -->';
}

require_once('footer.php');
?>