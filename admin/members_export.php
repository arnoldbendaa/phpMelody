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

session_start();
require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

if (is_moderator())
{
		restricted_access();
}

if ( ! is_user_logged_in() || ! is_admin())
{
	header("Location: login.php");
	exit();
}

@header("Content-type: application/octet-stream");
@header("Content-Disposition: attachment; filename=\"pm_mailinglist_".date('Y-m-d').".csv\"");

		$get_members = mysql_query("SELECT DISTINCT id, username, name, email FROM pm_users");

		$data = "Title,First Name,Middle Name,Last Name,Suffix,Company,Department,Job Title,Business Street,Business Street 2,Business Street 3,Business City,Business State,Business Postal Code,Business Country/Region,Home Street,Home Street 2,Home Street 3,Home City,Home State,Home Postal Code,Home Country/Region,Other Street,Other Street 2,Other Street 3,Other City,Other State,Other Postal Code,Other Country/Region,Assistant's Phone,Business Fax,Business Phone,Business Phone 2,Callback,Car Phone,Company Main Phone,Home Fax,Home Phone,Home Phone 2,ISDN,Mobile Phone,Other Fax,Other Phone,Pager,Primary Phone,Radio Phone,TTY/TDD Phone,Telex,Account,Anniversary,Assistant's Name,Billing Information,Birthday,Business Address PO Box,Categories,Children,Directory Server,E-mail Address,E-mail Type,E-mail Display Name,E-mail 2 Address,E-mail 2 Type,E-mail 2 Display Name,E-mail 3 Address,E-mail 3 Type,E-mail 3 Display Name,Gender,Government ID Number,Hobby,Home Address PO Box,Initials,Internet Free Busy,Keywords,Language,Location,Manager's Name,Mileage,Notes,Office Location,Organizational ID Number,Other Address PO Box,Priority,Private,Profession,Referred By,Sensitivity,Spouse,User 1,User 2,User 3,User 4,Web Page\n";
		
while ($r = mysql_fetch_assoc($get_members)) {
		$name = str_replace(",", " ", $r['name']);
		$data .= ",".$name.",,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,0/0/00,,,0/0/00,,,,,".$r['email'].",SMTP,".$r['email'].",".$r['email'].",SMTP,".$r['email'].",,,,Unspecified,,,,,,,,,,,User ID: ".$r['id'].",,,,Low,,,,Normal,,,,,,\n";
}
echo $data;

?>