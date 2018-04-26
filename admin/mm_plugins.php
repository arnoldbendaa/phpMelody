<?php
/**
 * Melodymods.com PHP Melody Mod framework - admin panel
 * 
 * Page for managing plugins and (de)activating them
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
$showm = '12';
$_page_title = 'PHP Melody Mod framework - admin panel';
include('header.php');
include('syndicate_news.php');
if(!is_admin()) restricted_access();
$frameworkmgr = new frameworkmanager();
/*
 * Some slightly modified function files from PHP Melody
 */
function get_mod_news($limit = 5) {
	$rss = new lastRSS; 
	$rssurl = "http://melodymods.com/newsrss.php";
	
	$nowTime = strtotime(date('F jS, Y'));
	if ($rs = $rss->get($rssurl)) { 
		for( $i = 0; $i < $limit; $i++){
			$lastTime = strtotime($rs['items'][$i]['pubDate']);
			if(($nowTime-$lastTime) < 1330000) {
				$ret .= "<li class='news-recent'>\n";
			} else {
				$ret .= "<li>\n";
			}
			$ret .= "<a href=\"".$rs['items'][$i]['link']."\" target=\"_blank\">\n"; //<span class=\"news-tag border-radius3\">UNREAD</span>\n";
			$ret .= "<h4>".$rs['items'][$i]['title']."</h4></a>\n";
			$ret .= "<p>";
			$ret .= unhtmlspecialchars($rs['items'][$i]['description'])."</p>\n";
			$ret .= "\n";
			$ret .= "</li>";
		}
	} 
	else { 
		$ret = "RSS feed not found. Check out http://melodymods.com for the latest plugin news."; 
	} 
	return $ret;
}
function mod_cache_this($type, $signature) {
	global $frameworkmgr;
	$cacheFile = './temp/'. md5($signature) .'-'. date('Ym');
	$cacheTime = 48 * 3600;
	$now = time();
	$last_update = 0;
	if ($file_exists = file_exists($cacheFile))
	{
		$last_update = filemtime($cacheFile);
	}
	
	// Serve the cached content if present
	if ($file_exists &&  ($now - $cacheTime) < $last_update) 
	{
		return file_get_contents($cacheFile);
	}

	$date = getdate();
	$last_mo = mktime(0, 0, 0, $date['mon']-1, 1, $date['year']);
	
	$prev_cache = './temp/'. md5($signature) .'-'. date('Ym', $last_mo);
	if (file_exists($prev_cache))
	{
		unlink($prev_cache);
	}

	// Cache the contents to a file
	$cached = @fopen($cacheFile, 'w');
	if ($type == 'read_fwversion')
	{
		$content = $frameworkmgr->getversion();
	}
	else if ($type == 'get_mod_news')
	{
		$content = get_mod_news(6);
	}
	@fwrite($cached, $content, strlen($content));
	@fclose($cached);
	return $content;
}
/*
 * Start real page
 */
?>
<div id="adminPrimary">
<div class="content">
	<h2>Plugins</h2>

<?php
if(isset($_GET['installframework'])){
	$error='';
	if($frameworkmgr->install($error)){
		echo '<div class="alert alert-success">PHP Melody Mod Framework was activated successfully. Powered by Melodymods.com.</div>';
	}else{
		echo '<div class="alert alert-error">Installation failed. Error message from MySQL: '.$error.'<br />If this error persists, please contact the Melodymods.com support at info@melodymods.com</div>';
	}
}
if(isset($_GET['do'])){
	switch ($_GET['do']){
		case 'deactivate':			
			//$_POST['plugin'] = $_GET['mod'];			
			if(!isset($_POST['plugin']) || !preg_match('/^[a-z0-9_]+$/i',$_POST['plugin'])){
				break;
			}
			$modframework->change_state($_POST['plugin'], 0);	
		break;
		case 'activate':
			//$_POST['plugin'] = $_GET['mod'];
			if(!isset($_POST['plugin']) || !preg_match('/^[a-z0-9_]+$/i',$_POST['plugin'])){
				break;
			}
			if(!file_exists(ABSPATH.'plugins/'.$_POST['plugin'].'.class.php')){
				echo '<div class="alert alert-error">Error: Plugin file <strong>'.$_POST['plugin'].'.class.php</strong> not found in <strong>/plugins/</strong> directory!</div>';
				break;
			}
			if(class_exists($_POST['plugin'])){
				echo '<div class="alert alert-info">Plugin '.$_POST['plugin'].' is already active</div>';
				break;
			}
			require_once(ABSPATH.'plugins/'.$_POST['plugin'].'.class.php');
			if(!class_exists($_POST['plugin']) || !method_exists($_POST['plugin'],'install')){
				echo '<div class="alert alert-error">Error: Plugin '.$_POST['plugin'].' is not a valid plugin file!</div>';
				break;
			}
			$modframework->change_state($_POST['plugin'],1);
		break;
	}
}
?>
<script language="javascript">
function actplugin(plugin){
	$('form.'+plugin+'_act').submit();
	return false;
}
</script>

<div class="tablename">
<h6>PHP Melody Plugins</h6>
<div class="qsFilter">
<div class="btn-group input-prepend">
</div><!-- .btn-group -->
</div><!-- .qsFilter -->
</div>
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
   <tr>
	<th align="center" style="text-align:center" width="3%"></th>
	<th></th>
    <th></th>
   </tr>
  </thead>
  <tbody>
	<?php 
    $installable_plugins = $frameworkmgr->getinstallable();
    $active_plugins = $frameworkmgr->getactiveplugins();
    
    if($active_plugins === false){ 
      //Framework install required
      echo '</tbody></table></div></div>';
      include('footer.php');
      exit;
      
    }elseif($active_plugins===0 && $installable_plugins === false){
      echo '<tr><td colspan="3">No plugins installed yet. '; 
        if($installable_plugins === false){
          echo ' If you uploaded any plugins, make sure the <strong>*.class.php</strong> file belonging to that plugin is in your <strong>/plugins</strong> directory and not in any other subfolder.';
        }
      echo '</td></tr>';
    }	  
    ?>
  </tbody>
 </table>
 <div class="clearfix"></div>
 
<div class="pull-right" align="right">
<small><em>Powered by <strong>PHP Melody Mod Framework <?php echo modframework::version; ?></strong><br />
 <?php
 /*
  * We don't use this at the moment
 if(version_compare(modframework::version,mod_cache_this('read_fwversion', 'mf_version'),'<')) {
		echo 'Update available! <a href="http://melodymods.com/framework/">Click here to update</a>'; 
 }
 */
?>
</em></small>
</div>

    </div><!-- .content -->
</div><!-- .primary -->
<style>

span.spacing {
    display: inline-block;
    width: 30px;
}
</style>
<?php
include('footer.php');
?>