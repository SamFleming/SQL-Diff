<?php
namespace SqlDiff;
use \SqlDiff\Datasource as Datasource;

spl_autoload_register('\SqlDiff\Autoloader::autoload');

class Autoloader
{
	static public function autoload($name)
	{ 
		if(strpos($name, "SqlDiff") !== 0) return;

		// Strip "SqlDiff" from beginning of namespace
		$name = substr($name, 7);
		// Prefix uppercase characters with an underscore
		$name = preg_replace("/([A-Z]{1})/", "_$1", $name);
		// Remove any underscores directly after a \
		$name = str_replace("\\_", "\\", $name);
		// Prepend our main namespace
		$name = "sqldiff".strtolower($name);

		$base = dirname(dirname(__FILE__))."/";
		$file = str_replace("\\", "/", $name).".php";

		include_once $base.$file;
	}
}

class SqlDiff
{
	private $from;
	private $to;

	public function __construct(Datasource $from, Datasource $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	public function compare()
	{
		$changes = array();

		$from_tables = $this->from->get_tables();
		$to_tables = $this->to->get_tables();

		foreach($from_tables as $from_table)
		{
			foreach($to_tables as $to_table)
			{
				if($from_table->name == $to_table->name)
				{
					$these_changes = $this->compare_tables($from_table, $to_table);
					$changes = array_merge($changes, $these_changes);
					break;
				}
			}
		}

		return $changes;
	}

	private function compare_tables($from, $to)
	{
		$changes = array();
		$no_match = array();
		// For each of the columns in $from check to see if they exist in $to
		foreach($from->columns as $from_key => $from_column)
		{
			foreach($to->columns as $to_key => $to_column)
			{
				if($from_column->name == $to_column->name)
				{
					$these_changes = $this->compare_columns($from, $to, $from_key, $to_key);
					$changes = array_merge($changes, $these_changes);
					unset($no_match[$from_column->name]);
					break;
				}
				else
				{
					$no_match[$from_column->name] = array($from, $from_key);
				}
			}
		}

		if(!empty($no_match))
		{
			foreach($no_match as $name => $details)
			{
				$changes[] = new \SqlDiff\Change("add", null, $details[0], $details[1], null, null);
			}
		}
		return $changes;
	}

	private function compare_columns($from, $to, $from_key, $to_key)
	{
		$changes = array();
		$from_col = $from->columns[$from_key];
		$to_col = $to->columns[$to_key];

		// Check the column type
		if($from_col->type != $to_col->type)
		{
			$changes[] = new \SqlDiff\Change("edit", "type", $from, $from_key, $to, $to_key);
		}

		// Check the column length
		if($from_col->length != $to_col->length)
		{
			$changes[] = new \SqlDiff\Change("edit", "length", $from, $from_key, $to, $to_key);
		}

		return $changes;
	}
}