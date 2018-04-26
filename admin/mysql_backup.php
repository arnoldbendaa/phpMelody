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

define('MSB_VERSION', '1.0.0');
define('MSB_NL', "\r\n");

class BackupMySQL
{
var $server = '';
var $port = 3306;
var $username = '';
var $password = '';
var $database = '';
var $link_id = -1;
var $connected = false;
var $tables = array();
var $drop_tables = true;
var $struct_only = false;
var $comments = true;
var $backup_dir = '';
var $fname_format = 'd_m_y__H_i_s';
var $error = '';

function Execute($file_name,$database,$table_name,$start,$end,$max_rows)
{
	$this->_Connect();
	if (!($sql = $this->_Retrieve($database,$table_name,$start,$end,$max_rows)))return false;    
	return $this->_SaveToFile($file_name, $sql);
}

function _Connect()
{
	$value = false;
	if (!$this->connected)
	{
		$host = $this->server . ':' . $this->port;
		$this->link_id = mysql_connect($host, $this->username, $this->password);
	}
	if ($this->link_id)
	{
		if (empty($this->database))
		{
			$value = true;
		}
		elseif ($this->link_id !== -1)
		{
			$value = mysql_select_db($this->database, $this->link_id);
		}
		else
		{
			$value = mysql_select_db($this->database);
		}
		
		@mysql_query('SET NAMES utf8');
		@mysql_query('SET CHARACTER SET utf8');
		@mysql_query('SET COLLATION_CONNECTION="utf8_general_ci"');
		@mysql_query("SET @@global.sql_mode='MYSQL40'");
	}
	if (!$value)
	{
		$this->error = mysql_error();
	}
	return $value;
}

function _Query($sql)
{
	if ($this->link_id !== -1)
	{
		$result = mysql_query($sql, $this->link_id);
	}
	else
	{
		$result = mysql_query($sql);
	}
	if (!$result)
	{
		$this->error = mysql_error();
	}
	return $result;
}	

function _GetTables($database)
{
	$this->_Connect();
	$value = array();
	if (!($result = $this->_Query('SHOW TABLES')))
	{
		return false;
	}
	while ($row = mysql_fetch_row($result))
	{
		if (!($result1 = $this->_Query('SELECT count(*) FROM `'.$row[0].'`')))
		{
			return false;
		}
		$row1 = mysql_fetch_row($result1);
		
		$value[] = $row[0].':'.$row1[0];
	}
	if (!sizeof($value))
	{
		$this->error = 'No tables found in database.';
		return false;
	}
	return $value;
}

function GetTables($database)
{
	return $this->_GetTables($database);
}

function _DumpTable($database,$table,$start,$end,$row_count)
{
	$value = '';
	
	if($start==0)
	{
		if ($this->comments)
		{
			$value .= '#' . MSB_NL;
			$value .= '# Table structure for table `'.$table. '`'. MSB_NL;
			$value .= '#' . MSB_NL . MSB_NL;
		}
		if ($this->drop_tables)
		{
			$value .= 'DROP TABLE IF EXISTS `'.$table. '`;'. MSB_NL;
		}
		if (!($result = $this->_Query('SHOW CREATE TABLE `'.$table.'`')))
		{
			return false;
		}
		$row = mysql_fetch_assoc($result);
		$row['Create Table'] = str_replace("TYPE=", "ENGINE=", $row['Create Table']);
		$value .= str_replace("\n", MSB_NL, $row['Create Table']) . ';';
		$value .= MSB_NL . MSB_NL;
	}
	
	if (!$this->struct_only)
	{
		if ($start==0 && $this->comments)
		{
			$value .= '#' . MSB_NL;
			$value .= '# Dumping data for table `' . $table . '`' . MSB_NL;
			$value .= '#' . MSB_NL . MSB_NL;
		}
		$value .= $this->_GetInserts($database,$table,$start,$end);
	}
	
	if($end>=$row_count)$value .= MSB_NL . MSB_NL;
	
	return $value;
}	

function _GetInserts($database,$table,$start,$end)
{
	$value = '';
	if (!($result = $this->_Query('SELECT * FROM `'.$table.'` LIMIT '.$start.','.$end)))
	{
		return false;
	}
	while ($row = mysql_fetch_row($result))
	{
		$values = '';
		foreach ($row as $data)
		{
			$values .= '\'' . addslashes($data) . '\', ';
		}
		$values = substr($values, 0, -2);
		$value .= 'INSERT INTO `'.$table . '` VALUES (' . $values . ');' . MSB_NL;
	}
	return $value;
}

	function _Retrieve($database,$table_name,$start,$end,$row_count)
	{
		$value = '';
		if (!$this->_Connect())
		{
			return false;
		}
		
		if (!($table_dump = $this->_DumpTable($database,$table_name,$start,$end,$row_count)))
		{
			$this->error = mysql_error();
			return false;
		}
		$value .= $table_dump;
		
		return $value;
	}
	
	function _SaveToFile($fname, $sql)
	{
		if (!($f = fopen($fname, 'a')))
		{
			$this->error = 'Can\'t create the output file.';
			return false;
		}
		fwrite($f, $sql);
		fclose($f);
		return true;
	}
	
}

?>