<?php

class CsvImporter 
{ 
	private $fp; 
	private $parse_header; 
	private $header;
	private $delimiter; 
	private $enclosure;
	private $escape;
	private $length; 
	
	function __construct($file_name, $header = null, $length = 8000, $parse_header = false, $delimiter = ',', $enclosure = '"', $escape = '\\') 
	{ 
		$this->fp = fopen($file_name, "r"); 
		$this->parse_header = $parse_header; 
		$this->delimiter = $delimiter;
		$this->enclosure = $enclosure; 
		$this->escape = $escape; 
		$this->length = $length;
		$this->lines = $lines;
		
		if ($header)
		{
			$this->header = $header;
			$this->parse_header = true;
		}
		else if ($this->parse_header) 
		{ 
			$this->header = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure);
		}

	} 
	
	function close_source_file()
	{
		if ($this->fp) 
		{ 
			@fclose($this->fp); 
		}
	}
	
	function __destruct()
	{
		$this->close_source_file(); 
	}
	
	function get($max_lines = 0) //if $max_lines is set to 0, then get all the data 
	{
		$data = array(); 
		
		if ($max_lines > 0) 
		{
			$line_count = 0;
		}
		else
		{
			$line_count = -1; // so loop limit is ignored
		}

		while ($line_count < $max_lines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure)) !== FALSE) 
		{ 
			if ($this->parse_header) 
			{
				foreach ($this->header as $i => $heading_i) 
				{
					$row_new[$heading_i] = $row[$i]; 
				}
				$data[] = $row_new;
			}
			else 
			{
				$data[] = $row; 
			}

			if ($max_lines > 0)
			{
				$line_count++;
			}
		}
		return $data; 
	}
}

