<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | phpSugar, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: phpSugar (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2016 PhpSugar.com. All rights reserved.
// +------------------------------------------------------------------------+
header("Expires: Mon, 1 Jan 1999 01:01:01 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

define('PM_DOING_INSTALL', true);
define('IGNORE_MOBILE', true);

if (file_exists('config.php'))
{
	@include_once('config.php');
}

if ( ! extension_loaded('mysql') && ! function_exists('mysql_connect'))
{
	include_once(ABSPATH .'include/mysql2i.class.php');
}

if ( ! defined('_ADMIN_FOLDER')) 
{
	define('_ADMIN_FOLDER', 'admin');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Installing PHP Melody</title>
<link rel="shortcut icon" type="image/ico" href="<?php echo _ADMIN_FOLDER; ?>/img/favicon.ico" />
<link rel="stylesheet" type="text/css" media="screen" href="templates/default/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="templates/default/css/new-style.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo _ADMIN_FOLDER; ?>/css/admin.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700|Roboto:400,300,500,700' rel='stylesheet' type='text/css' media='all' />
<link href='//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' type='text/css' media='all' />
</head>
<body>
<style type="text/css">
body{font-family:"Roboto",Arial,Helvetica,sans-serif;background-image:none;background-color:#0060a2}
header{margin:0 auto;padding:40px 0;text-align:center}
#container{position:relative;margin:0 auto;padding:40px;background:#f0f7fc;border:3px solid #0e7ac4;width:650px}
#container h1{text-align:left;font-weight:normal;font-size:20px;text-shadow:0 1px 0 #FFF;color:#369ef1;display:block;margin-bottom:20px}
#container h1 strong{font-weight:bold}
#container h2{font-family:"Open Sans",Verdana,Geneva,sans-serif;letter-spacing:-1px;border-bottom:0 none;color:#53a5d6;margin:10px 0;padding:0}
.lead{color:#444;font-size:1.2em}
.lead a{color:#53a5d6}
ul{list-style-type:none;margin:0}
ul li{position:relative;border-bottom:1px solid #d9e7f3;padding:5px 0;margin:2px 0;color:#555;text-shadow:0 1px 0 #FFF;-moz-text-shadow:0 1px 0 #FFF}
ul li .label{position:absolute;right:0;top:2px;padding:6px;border-radius:100px}
ul.file-check{color:#888;font-size:11px;margin:5px;position:relative}
ul.file-check li{border-bottom:0 none;margin:0;padding:1px 0}
.fa-file-o,.icon-folder-open{opacity:.5}
.alert{font-size:14px;font-family:Arial,Helvetica,sans-serif;padding:15px 20px}
.warn{color:#c76e34;font-family:Arial,Helvetica,sans-serif;font-size:11px;display:block}
.error{background-color:#ffd5d5;border:1px solid #ffacad;color:#cf3738}
.allright,.warning,.error{font-size:12px;line-height:19px;margin:6px 10px;display:block}
#footer{font-size:11px;color:#999;padding:15px 0 0;border-top:1px dotted #EEE;text-align:center;margin-top:20px}
#footer a:link,#footer a:visited{color:#777;text-decoration:none}
#footer a:hover{color:#333;text-decoration:none}
#footer p{margin-top:10px;font-size:11px}
</style>

<header>
<img src="<?php echo _ADMIN_FOLDER; ?>/img/install-logo.png" width="179" height="34" align="PHP Melody" />
</header>

<div id="container" class="border-radius5">
<?php
$step = $_GET['step'];

if ( empty($step) ) {
	$step = 1;
}

switch($step){
default:
case 1:
		$error = 0;
		echo "<h1><strong>Installation</strong>: Checking your setup...</h1>";
		echo "<ul class=''>";
		echo "<li>Checking the PHP version ... ";
		if (version_compare(PHP_VERSION, '5.3', '<')) 
		{
			$error = 1;
			echo "<span class=\"warn\">PHP version 5.3 or later is required. Please consider updating your PHP version before installing PHP Melody.</span> <span class=\"label label-warning pull-right\"><i class='fa fa-exclamation-triangle'></i></span>";
		}
		else
		{
			echo "<span class=\"label label-success pull-right\"><i class='fa fa-check'></i></span>";
		}
		echo "<li>Checking if <strong>config.php</strong> exists ... ";
		if( ! file_exists('config.php')) { 
			$error = 1;
			echo "not found. <span class=\"label label-warning pull-right\"><i class='fa fa-exclamation-triangle'></i></span>";
		}
		else {
			echo "<span class=\"label label-success pull-right\"><i class='fa fa-check'></i></span>";
		}
		echo "</li>";
		
		
		require_once('include/functions.php');
		
		echo "<li>Checking if <strong>phpmelody_sql.sql</strong> exists ... ";
		if( ! file_exists('phpmelody_sql.sql')) { 
			$error = 1;
			echo "not found. <span class=\"label label-warning pull-right\"><i class='fa fa-exclamation-triangle'></i></span>";
		}
		else {
			echo "<span class=\"label label-success pull-right\"><i class='fa fa-check'></i></span>";
		}
		echo "</li>";
		
		echo "<li>Checking the database connection ... ";
		$connection = @mysql_connect($db_host, $db_user, $db_pass);
		if( ! $connection )  {
			$error = 1;
			echo "<span class=\"warn\">ERROR: ".mysql_error()."</span> <span class=\"label label-warning pull-right\"><i class='fa fa-exclamation-triangle'></i></span>";
			}
			else {
				echo "<span class=\"label label-success pull-right\"><i class='fa fa-check'></i></span>";
			}
			echo "</li>";
		
		echo "<li>Setting the database collation ... ";
		$connection = @mysql_connect($db_host, $db_user, $db_pass);
			if( ! $connection )  {
				$error = 1;
				echo "<span class=\"warn\">ERROR: ".mysql_error()."</span> <span class=\"label label-warning pull-right\"><i class='fa fa-exclamation-triangle'></i></span>";
			}
			else {
				@mysql_query(" ALTER DATABASE `".$db_name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ");
				echo "<span class=\"label label-success pull-right\"><i class='fa fa-check'></i></span>";
			}			
			echo "</li>";
		
		echo "<li>Checking the MySQL database ... ";
		$db = @mysql_select_db($db_name);
		if( !$db ) {
			$error = 1;
			echo "<span class=\"warn\">ERROR: ".mysql_error()."</span> <span class=\"label label-warning pull-right\"><i class='fa fa-exclamation-triangle'></i></span>";
		}
		else {
			echo "<span class=\"label label-success pull-right\"><i class='fa fa-check'></i></span>";
		}
		echo "</li>";


function pm_file_writtable($path) {

	if ( ! is_writable(ABSPATH . $path) ) {
		$icon = 'fa fa-file-o';
		$tmp_parts = explode('.', $path);
		$ext = array_pop($tmp_parts);
		$ext = strtolower($ext);
		if (strlen($ext) > 4)
		{
			$icon = 'fa fa-folder-open-o';
		}
		
		echo '<li><i class="'. $icon .'"></i> 	<strong>'.$path.'</strong> must be writable. Set CHMOD 0777.</li>';
	}
}
				
			echo "<ul class=\"file-check\">";
					pm_file_writtable("blacklist.txt");
					pm_file_writtable("censor_words.txt");
					pm_file_writtable("sitemap-index.xml");
					pm_file_writtable("video-sitemap-index.xml");
					pm_file_writtable("players/jwplayer5/jwembed.xml");
					pm_file_writtable(_ADMIN_FOLDER ."/tmp.pm");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/embedparams.xml");
					pm_file_writtable(_ADMIN_FOLDER ."/temp/");
					pm_file_writtable("uploads/");					
					pm_file_writtable("uploads/articles/");
					pm_file_writtable("uploads/avatars/");
					pm_file_writtable("uploads/covers/");
					pm_file_writtable("uploads/thumbs/");
					pm_file_writtable("uploads/videos/");
					pm_file_writtable("Smarty/templates_c/");
			echo "</ul>";	
		
		
			$sql = "SELECT id FROM pm_config WHERE id = '1'";
			$result = @mysql_query($sql);
			
		if( $result ) {	
			$error = 1;
			echo "<p class=\"alert alert-warning border-radius4\">The installation cannot begin because MySQL tables from a previous PHP Melody installation were detected in the <strong><em>".$db_name."</em></strong> database. To re-install PHP Melody empty the <strong><em>".$db_name."</em></strong> MySQL database first and click '<strong>Retry</strong>'.<br /> Backup any existing MySQL data before deleting anything!</p>";
		}
		if ( _CUSTOMER_ID == '' || _CUSTOMER_ID == 'YOUR_CUSTOMER_ID' ){
			$error = 1;
			echo "<p class=\"alert alert-warning border-radius4\">The installation cannot begin because your <strong>Customer ID</strong> is missing from <strong><em>config.php</em></strong>. <br /> Add your Customer ID in <strong><em>config.php</em></strong> as described in the Installation Manual by updating the following line:<br /><br /> <code>define('_CUSTOMER_ID', 'YOUR_CUSTOMER_ID');</code></p>";
		}
		if( !$error ){
			echo "<p></p>";
			echo "<p class=\"alert alert-success\"><strong>Excellent</strong>! Everything is in order. Click <strong>Continue</strong> to build your MySQL database.</p><br />";
			echo "<div align=\"center\"><a href=\"install.php?step=2&start=1&pointer=0\" class=\"btn btn-blue\"><strong>Continue &rarr;</strong></a></div>";
		}
		else {
			echo "<br /><center><p><a href=\"install.php\" class=\"btn btn-danger\"><strong>Retry</strong></a></p></center>";
		
		}

break;

//Step 2 - start importing the database;
case 2:
		require_once('config.php');
		require_once('include/functions.php');

		function get_percentage($full, $part){
			$percent = ceil(($part * 100)/$full);
			return $percent;
		}

		$connection = db_connect();

		ini_set("auto_detect_line_endings", true);
	
		$filename 	 =	"phpmelody_sql.sql";
		$linesize 	 =	65536;
		$querybatch	 =	300;
		$linesbatch  = 	2000;
		$sleep 		 = 	500;

		
		$comment[0]="#";
		$comment[1]="-- ";
		
		?>
  <p class="alert alert-warning border-radius4" id="please_wait"><img src="<?php echo _ADMIN_FOLDER; ?>/img/ico-loading.gif" width="16" height="16" border="0" align="absmiddle" /> Please wait. Your PHP Melody site is being installed...</p>
  <?php
		
		$error = 0;

		if (!$error && isset($filename)){ 
		//open the .sql file
		  if ( !$file = @fopen($filename,"rt")){
			echo "<p class=\"alert alert-warning border-radius4\">Cannot open <strong>".$filename."</strong>";
			if( !file_exists($filename) )
				echo "<br /> It seems that the file does not exist! Please check that you've uploaded all the files.";
			echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
			$error = 1;
		  }
			
		  elseif ( fseek($file, 0, SEEK_END)==0) {
			$filesize = ftell($file);
		  }

		  else{
			echo "<p class=\"alert alert-warning border-radius4\">Cannot get the filesize of ".$filename;
			echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
			$error = 1;
		  }
		}
		  if (!$error && isset($_REQUEST["start"]) && isset($_REQUEST["pointer"])){

		  if ($_REQUEST["pointer"] > $filesize){
//			If the script ended here, it means that the file pointer is somewhere after the end of file. 
			$error = 1;
			echo "<p class=\"alert alert-warning\">The file pointer is out of bounds.";
			echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
		  }
		
		  if (!$error && (fseek($file, $_REQUEST["pointer"]) != 0) ){
//			If the script ended here, it means that the file pointer could not be set at $_REQUEST["pointer"];
			$error = 1;
			echo "<br /><p class=\"alert alert-warning\">The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
		  }
		
		  if (!$error){
		    $query = "";
			$queries = 0;
			$linenumber = $_REQUEST["start"];
			$querylines = 0;
			$inparents = false;

			while (($linenumber < $_REQUEST["start"] + $linesbatch || $query!="") 
			   && ($dumpline=fgets($file, $linesize)) ){ 

			  $dumpline = str_replace("\r\n", "\n", $dumpline);
			  $dumpline = str_replace("\r", "\n", $dumpline);
			  
			  if ( !$inparents ){ 
			  
				$skipline = false;
				reset($comment);
				foreach ($comment as $comment_value)
				{ if (!$inparents && (trim($dumpline)=="" || strpos ($dumpline, $comment_value) === 0))
				  { $skipline=true;
					break;
				  }
				}
				if( $skipline ){
				  $linenumber++;
				  continue; 
				}
			  }
		
			  $dumpline_deslashed = str_replace ("\\\\","",$dumpline);
		
			  $parents = substr_count($dumpline_deslashed, "'")-substr_count ($dumpline_deslashed, "\\'");
			  
			  if( $parents%2 != 0 )
				$inparents=!$inparents;
			  $query .= $dumpline;

			  if (!$inparents)
				$querylines++;
			  
			  if ( $querylines > $querybatch)
			  {
//				If the script ended here, it means that the current query includes more than $querybatch dump lines. Possible cause: missing ";" (semicolon) after every dump line.
//				This shouldn't ever happen, but is better to be safe than sorry
				echo "<br /><p class=\"alert alert-warning\">The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
				$error = 1;
				break;
			  }
		
			  if (preg_match("/;$/",trim($dumpline)) && !$inparents)
			  { if(!mysql_query(trim($query), $connection))
				{ 
				  ?>
  <script type="text/javascript">
					$(document).ready(function(){
									$('#please_wait').slideUp(300);
					});
					</script>
  <?php
				  echo "<p class=\"alert alert-warning border-radius4\">There was a problem during the installation process. The reported MySQL error is: <br />";
				  echo mysql_error();
				  echo "<br />The installation was stopped at this line: <strong>".__LINE__."</strong></p>";
				  $error = 1;
				  break;
				}
				$queries++;
				$query="";
				$querylines=0;
			  }
			  $linenumber++;
			}
		  }
		
		  if (!$error){
			  $pointer = ftell($file);
			if (!$pointer){
//			If the script ended here, it means that it cannot read the file pointer offset.		
			  $error = 1;
			  $line_of_end = __LINE__;
			}
		  }
		
		  if ( !$error ){
			if ($linenumber < $_REQUEST["start"]+ $linesbatch){
			?>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#please_wait').slideUp(200);
			});
			</script>
  <?php
			  echo "<h1>PHP Melody is installed and ready to go!</h1>";
				if((!@unlink($filename)) || (!@unlink("install.php"))){
				
				  echo "<p class=\"alert alert-warning border-radius4\">Before anything else, please remove the following files:<br />
						<i class=\"fa fa-file-o\"></i>  <strong>install.php</strong><br />
						<i class=\"fa fa-file-o\"></i>  <strong>".$filename."</strong></p>";
				  echo "</p>";
				}
			  echo "<p class=\"lead\">You can now <a href=\""._URL."/". _ADMIN_FOLDER ."/login.php\" target=\"_blank\">login</a> as an Administrator with username '<strong>admin</strong>' and password '<strong>admin</strong>'.";
			  echo "</p>";
			  echo "<br /><div align=\"center\"><a href=\""._URL."/". _ADMIN_FOLDER ."/login.php\" target=\"_blank\" class=\"btn btn-blue\">Take me to the admin panel &rarr;</a></div>";
			  echo "";
//			  $error = 1;
			}
			else
			{ 			
			  echo "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"install.php?step=2&start=$linenumber&pointer=$pointer\";',500+$sleep);</script>\n";
			  echo "<noscript>\n";
			  echo "<p>Click <a href=\"install.php?step=2&start=$linenumber&pointer=$pointer\">continue &rarr;</a> (Please enable JavaScript to do this automatically)</p>\n";
			  echo "</noscript>\n";
			}
		  }
		}
		
		if ($error)
			echo "<p class=\"alert alert-warning\">There was an error. Please drop the old tables before restarting!<br /></p> <br /><a href=\"install.php\" class=\"btn\">&larr; Go back</a>";
		// close both the connection and file;
		if ($connection) mysql_close();
		if ($file) fclose($file);

		?>
  <?php
break;
}
// end switch;
?>
  <div id="footer">
  <a href="http://www.phpsugar.com/" title="Powered by PHPSUGAR.com" target="_blank"><img src="//www.phpsugar.com/updates/phpsugar.gif&license=<?php echo _CUSTOMER_ID;?>" border="0" alt="Powered by PHPSUGAR.com" /></a><br />
  <p>Copyright &copy; <?php echo date('Y'); ?><br />
  Need help? <a href="https://www.phpsugar.com/support.html" target="_blank">Click here</a>
  </p>
  </div>
</div>
</body>
</html>