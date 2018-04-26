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
// | Copyright: (c) 2004-2017 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

// Not sure how to configure? Please read the Installation Manual PDF
// +------------------------------------------------------------------------+

//-- MySQL Settings --//
/** MySQL database name */
$db_name = 'database_name';

/** MySQL database username */
$db_user = 'database_username';

/** MySQL database password */
$db_pass = 'password';

/** MySQL hostname */
$db_host = 'localhost';

// Full URL without any trailing slash (e.g http://www.example.com)
define('_URL', 'http://www.site.com');	

//-- Customer ID --//
// Replace "YOUR_CUSTOMER_ID" below with the assigned "Customer ID".
// The "Customer ID" is found in the order confirmation emails or in your Customer Account
// If you don't know your "Customer ID" email support at support@phpsugar.com
define('_CUSTOMER_ID', 'YOUR_CUSTOMER_ID');	

define('_ADMIN_FOLDER', 'admin');

error_reporting(E_ALL & ~E_NOTICE &  ~E_STRICT  & ~E_DEPRECATED); // Production
//error_reporting(E_ALL & ~E_NOTICE); // Development

// ========================================================= //
//-- MySQL Backup Directory --//
define('BKUP_DIR', 'temp');	//	WITHOUT any trailing slash
define('_POWEREDBY', 0);

@header('CONTENT-TYPE: text/html; charset=utf-8');
define('ABSPATH', dirname(__FILE__).'/'); 
if ( ! file_exists('install.php') ) 
{
	require_once( ABSPATH.'include/settings.php');
}
else
{
	if ( ! defined('PM_DOING_INSTALL'))
	{
		die('<div align="center" style="font-family: Arial, sans-serif; color: #555; margin: 60px 0; line-height: 1.6em;"><img src="'. _ADMIN_FOLDER .'/img/login-logo.png"> <br /> <br /> <br />If you haven\'t installed PHP Melody yet, <a href="install.php">start the installation process</a> now. <br> Otherwise, remove <strong>install.php</strong> from your server.');
	}
}

define('_URL_MOBI', _URL .'/mobile');
define('ABSPATH_MOBI', ABSPATH .'mobile/');

if (file_exists(ABSPATH_MOBI) && ! defined('IGNORE_MOBILE') && strpos($_SERVER['SCRIPT_NAME'], '/'. _ADMIN_FOLDER .'/') === false)
{
	define('MOBILE_MELODY', true);
	include(ABSPATH_MOBI .'mobile_detect.php');
	
	$mobile_detector = new Mobile_Detect;
	
	define('USER_DEVICE', ($mobile_detector->isMobile()) ? 'mobile' : 'desktop');
	
	include(ABSPATH_MOBI .'detect.php');
}
else
{
	define('MOBILE_MELODY', false);
	define('USER_DEVICE', 'desktop');
}