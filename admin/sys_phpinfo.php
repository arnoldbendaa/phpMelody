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

$showm = '7';
$_page_title = 'PHP Configuration';
include('header.php');

function color_php_values($value) {

	$value = strtolower($value);

	if($value == 'enabled') {
		$value = '<span class="label label-success">'.$value.'</span>';
	}
	elseif($value == 'disabled') {
		$value = '<span class="label label-warning">'.$value.'</span>';
	}
	return $value;
}
?>
<div id="adminPrimary">
    <div class="content">
	<h2>PHP Configuration</h2>
<?php echo $info_msg; ?>

<?php
ob_start();
phpinfo();
$contents = ob_get_clean();

$phpinfo = array('phpinfo' => array());

if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $contents, $matches, PREG_SET_ORDER))
{
	foreach($matches as $match)
	{
        if(strlen($match[1]))
		{
            $phpinfo[$match[1]] = array();
		}
        elseif(isset($match[3]))
		{
			$arr_keys = array_keys($phpinfo);
            $phpinfo[end($arr_keys)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
		}
        else
		{
			$arr_keys = array_keys($phpinfo);
			$phpinfo[end($arr_keys)][] = $match[2];
		}
	}
}

function apache_mod_loaded($mod, $default = false) {

	if ( function_exists('apache_get_modules') ) {
		$mods = apache_get_modules();
		if ( in_array($mod, $mods) )
			return true;
	} elseif ( function_exists('phpinfo') ) {
			ob_start();
			phpinfo(8);
			$phpinfo = ob_get_clean();
			if ( false !== strpos($phpinfo, $mod) )
				return true;
	}
	return $default;
}
$phpcore = (array_key_exists('PHP Core', $phpinfo)) ? 'PHP Core' : 'Core';

if ( ! function_exists('phpinfo')) // disabled by host
{
	echo pm_alert_warning('It looks like <code>phpinfo()</code> function has been disabled by your hosting provider. This function is required to retrieve information about your system.');
}

?>
<div class="alert alert-info">Listed below are some of the most important PHP Configuration values. This data is used for debugging problems in PHP Melody.</div>
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables">
 <thead>
  <tr>
   <th width="25%">Directive</th>
   <th>Value</th>
  </tr>
 </thead>
 <tbody>
	<tr>
		<td>PHP Version</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['PHP Version']}");?></td>
	</tr>

	<tr>
		<td>System</td>
		<td><?php echo color_php_values("{$phpinfo['phpinfo']['System']}");?></td>
	</tr>
	<tr>
		<td>Safe Mode</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['safe_mode'][0]}"); ?></td>
	</tr>
	<tr>
		<td>Allow URL fopen</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['allow_url_fopen'][0]}"); ?></td>
	</tr>
	<tr>
		<td>cURL Support</td>
        <td><?php echo color_php_values((in_array('curl', get_loaded_extensions())) ? "enabled" : "disabled"); ?></td>
	</tr>
	<tr>
		<td>Display Errors</td>
		<td><?php  echo color_php_values("{$phpinfo[$phpcore]['display_errors'][0]}"); ?></td>
	</tr>
	<tr>
		<td>Display Startup Errors</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['display_startup_errors'][0]}"); ?></td>
	</tr>
	<tr>
		<td>File Uploads</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['file_uploads'][0]}"); ?></td>
	</tr>
	<tr>
		<td>File Post Size (post_max_size)</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['post_max_size'][0]}"); ?></td>
	</tr>
	<tr>
		<td>Max File Size Upload (upload_max_filesize)</td>
		<td><?php echo color_php_values("{$phpinfo[$phpcore]['upload_max_filesize'][0]}"); ?></td>
	</tr>
	<tr>
		<td>Server Name</td>
		<td><?php echo color_php_values("{$phpinfo['Apache Environment']['SERVER_NAME']}"); ?></td>
	</tr>
	<tr>
		<td>HTTP Accept charset</td>
		<td><?php echo color_php_values("{$phpinfo['Apache Environment']['HTTP_ACCEPT_CHARSET']}"); ?></td>
	</tr>
	<tr>
		<td>GD Library</td>
		<td><?php echo color_php_values("{$phpinfo['gd']['GD Support']}"); ?></td>
	</tr>
	<tr>
		<td>GD Library Version</td>
		<td><?php echo color_php_values("{$phpinfo['gd']['GD Version']}"); ?></td>
	</tr>
	<tr>
		<td>GIF Read Support</td>
		<td><?php echo color_php_values("{$phpinfo['gd']['GIF Read Support']}"); ?></td>
	</tr>
	<tr>
		<td>GIF Create Support</td>
		<td><?php echo color_php_values("{$phpinfo['gd']['GIF Create Support']}"); ?></td>
	</tr>
	<tr>
		<td>JPEG Support</td>
		<td><?php echo color_php_values("{$phpinfo['gd']['JPEG Support']}"); ?></td>
	</tr>
	<tr>
		<td>PNG Support</td>
		<td><?php echo color_php_values("{$phpinfo['gd']['PNG Support']}"); ?></td>
	</tr>
	<tr>
		<td>Session Support</td>
		<td><?php echo color_php_values("{$phpinfo['session']['Session Support']}"); ?></td>
	</tr>
	<tr>
		<td>Apache mod_rewrite</td>
		<td><?php echo color_php_values((apache_mod_loaded('mod_rewrite')) ? "enabled" : "disabled"); ?></td>
	</tr>
	<tr>
		<td>Apache modules</td>
		<td><?php print_r($phpinfo['apache2handler']['Loaded Modules']); ?></td>
	</tr>
 </tbody>
</table>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>