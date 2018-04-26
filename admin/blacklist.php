<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '4';
$_page_title = 'Comment abuse prevention';
include('header.php');

$list = ''; 
$content = '';
$words = '';
$words_list = array();

$list = $_GET['list'];

if($list == ''){
	$list = 'censor_words';
}

switch($list){ 
	default:
	case 'censored':
		$file = '../censor_words.txt';
		$title = 'Censored words';
	break;
	
	case 'blacklist':		
		$file = '../blacklist.txt';
		$title = 'Blacklist';
	break;
}//	end switch

if($_POST['Submit'] == "Save"){
	$words = $_POST['words'];
	
	$temp_arr = explode("\n", $words);
	
	for($i = 0; $i < count($temp_arr); $i++){
		if(trim($temp_arr[$i]) != '' && strlen($temp_arr[$i]) > 1) 
			$words_list[] = $temp_arr[$i];
	}	
	$fp = fopen($file, "w");
	if(!$fp) { 
		echo '<div class="alert">Sorry, file <strong>'.$file.'</strong> cannot be opened. Check if <strong>'.$file.'</strong> was uploaded and if it\'s writable (CHMOD 0777)</div>';
		include('footer.php');
		exit();
	}
	$line = '';
	for($i = 0; $i < count($words_list); $i++){
		if($i != count($words_list)-1)
			$line = $words_list[$i]."\n";
		else
			$line = $words_list[$i];
		fwrite($fp, $line, strlen($line));
	}
	fclose($fp);
	$info_msg = pm_alert_success('The list was updated successfully.');
}
else{
	$fp = @fopen($file, "r");
	if ( ! $fp) 
	{ 
		echo pm_alert_error('Sorry, file <code>'.$file.'</code> cannot be opened. Check if the file exists and if it\'s writable (CHMOD 0777).');
		include('footer.php');
		exit;
	}
	while ( ! feof($fp))
	{
		$content .= fread($fp, 4096);
	}
	fclose($fp);
	
	if (empty($content))
	{
		$info_msg = pm_alert_info('The list is currently empty. Add one word per line (i.e. a list) without any punctuation marks.');
	}
}

function read_censored_words($filename) 
{
		$fp = @fopen($filename, "r");
		$content = '';
		if ( ! $fp) 
		{ 
			return pm_alert_error('Could not open file <code>'.$file.'</code>. Make sure the file exists and if it\'s writable (CHMOD 0777).');
		}
		while ( ! feof($fp))
		{
			$content .= fread($fp, 4096);
		}
		fclose($fp);

		return $content;
}
?>
<div id="adminPrimary">
    <div class="content">
	<h2>Abuse Prevention</h2>
<?php
if($info_msg) {
echo $info_msg; 
}
?>
	<?php echo pm_alert_info('Keep your site clean and your SEO rankings in good standing by filtering in any obscene or unwanted words from video and article comments.'); ?>
    <h2 class="sub-heading">Blacklisted words</h2>
    <div class="help-block">Comment containing any of the blacklisted words will be removed automatically.</div>
    <form name="form" method="post" action="blacklist.php?list=blacklist" class="form">
        <textarea name="words" class="span4" rows="5"><?php echo read_censored_words("../blacklist.txt"); ?></textarea>
        <input type="submit" name="Submit" value="Save" class="btn btn-success" />
    </form>
	<hr />
    <h2 class="sub-heading">Censored words</h2>
    <div class="help-block">Censored words will be replaced with '***' but the rest of the comment will still be posted.</div>
    <form name="form" method="post" action="blacklist.php?list=censored" class="form">
        <textarea name="words" class="span4" rows="5"><?php echo read_censored_words("../censor_words.txt"); ?></textarea>
        <input type="submit" name="Submit" value="Save" class="btn btn-success" />
    </form>

    



    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>