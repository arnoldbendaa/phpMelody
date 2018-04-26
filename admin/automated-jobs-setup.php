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
// | Copyright: (c) 2004-2016 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = 'cron';

$_page_title = 'Automated Jobs Setup';
include('header.php');
include_once(ABSPATH . 'include/cron_functions.php');

// generate and save the secret key before outputting instructions 
$generated_key = false;
if (empty($config['cron_secret_key']))
{
	$generated_key = true;
	update_config('cron_secret_key', generate_cron_key(), true);
}
?>

<div id="adminPrimary">
	<div class="content">
	<h2>Automated Jobs Setup</h2>
	
	<?php if ($generated_key) :
		echo pm_alert_success('<h5>Your Secret Key was created. You can now create the cron job.</h5>');
	endif; ?>

	<div class="alert alert-help">

		<h5>Creating the Cron Job</h5>
		<p>The <strong>Automated Jobs</strong> feature require a cron job. Cron will execute <em>cron.php</em> on a regular basis to ensure all the automated jobs are executed in the background.</p>
		<p>Cron jobs can be usually created from your hosting panel (cPanel, Plesk, etc.). If you need step-by-step help with setting up a cron job please refer to this <a href="http://help.phpmelody.com/how-to-create-a-cron-job/" target="_blank">support document</a>.</p>
		<p>Your cron job will have the following properties:</p>
		<ol class="list-unstyled">
			<li><strong>Command</strong>:<br />
				<pre>wget -q -O /dev/null "<?php echo _URL; ?>/cron.php?cron-key=<?php echo $config['cron_secret_key']; ?>"</pre>
			</li>
			<li><strong>Run interval</strong>:<br />
				Every 5 minutes (*/5 * * * *)
			</li>
		</ol>
		<br />

		<p><strong>To confirm the cron job is working correctly</strong>, wait 5 to 10 minutes after creating it, then check your <a href="automated-jobs.php">automated jobs</a>.<br /> The <strong>Last Performed</strong> date should change from "<em>Never</em>" to a specific time (e.g. 4 seconds ago).</p>
	</div>
	</div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');