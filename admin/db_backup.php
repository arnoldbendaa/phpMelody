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

if ( ! is_user_logged_in() || ( ! is_admin()))
{
	header("Location: "._URL. "/". _ADMIN_FOLDER ."/login.php");
	exit();
}

// if ( ! csrfguard_check_referer('_admin_backupdb'))
// {
// 	echo 'Invalid token provided. Please refresh the page and try again.';
// 	exit();
// }

require_once('mysql_backup.php');

$restart = (int) $_GET['restart'];

$file_name = "backup_details.csv";
$max_rows = 500000;
$large_tables = array("");

$folder_name = ABSPATH ."/". _ADMIN_FOLDER ."/". BKUP_DIR ."/";

if(!is_dir($folder_name))
	mkdir($folder_name,0777);
else 
	@chmod($folder_name,0777);

@chmod($folder_name,0755);

$path = $folder_name;

$backup_obj = new BackupMySQL();

$database = trim($database);

$backup_obj->server = $db_host;
$backup_obj->port = 3306;
$backup_obj->username = $db_user;
$backup_obj->password = $db_pass;
$backup_obj->database = $db_name;

//-------------------- OPTIONAL PREFERENCE VARIABLES ---------------------

//Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file.
$backup_obj->drop_tables = true;

//Only structure of the tables will be backed up if true.
$backup_obj->struct_only = false;

//Include comments in backup file if true.
$backup_obj->comments = true;

$uniq_sql = generate_activation_key(6);
$filename = date('d-m-Y').'_'.$backup_obj->database.'_'.$uniq_sql.'.sql';

//	Get busy
if($restart == 1)
{
	$table_details = $backup_obj->GetTables($database);
	
	$fp = fopen($path . $file_name, 'w');
	
	for($i = 0;$i < count($table_details);$i++)
	{					
		fwrite($fp, $table_details[$i].":0\r\n");
	}
	fclose($fp);
	
	$fp = fopen($path . $filename, 'w');
	fclose($fp);
	
	$file_contents = file($path . $file_name);
	
	$total_rows = 0;
	$table_count = count($file_contents);
	for($i = 0;$i < $table_count; $i++)
	{
		list($table_name,$row_count,$start) = explode(':',str_replace("\r","",str_replace("\n","",$file_contents[$i])));
		
		if(in_array($table_name,$large_tables))
			$max_rows = 500000;
		else 
			$max_rows = 500000;
		
		if($start < $row_count || $row_count == 0)
		{
			if(($start + $max_rows) > $row_count)
				$end = ($row_count-$start);
			else
				$end = $max_rows;
			
			$str = $table_name.":".$row_count.":".($start+$end)."\r\n";
			$file_contents[$i] = $str;
			
			if (!$backup_obj->Execute($path . $filename,$database,$table_name,$start,$end,$row_count))
			{
				$output = $backup_obj->error;
				$log_error = "Error backing up table: ".$table_name.". Details: ".$output;
				echo $log_error;
				@log_error($log_error, 'backup', 1);
			}
			else
			{
				$total_rows += $end;			
				$fp = fopen($path . $file_name, 'w');
				$count = count($file_contents);
				for($j = 0;$j < $count; $j++)
				{		
					fwrite($fp, $file_contents[$j]);
				}
				fclose($fp);														
			}
			if($total_rows >= $max_rows)
				break;
		}		
	}

	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename = $filename");

	$mysql_db = @fopen($path . $filename, "r");
	if ($mysql_db) 
	{
		while(!feof($mysql_db)) 
		{
			$buffer = @fgets($mysql_db, 4096);
			echo $buffer;
		}
		@fclose($mysql_db);
	}
	@unlink($path . $file_name);
	@unlink($path . $filename);
}
?>