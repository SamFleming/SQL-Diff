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
		// Append our main namespace
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

		$this->compare();
	}

	private function compare()
	{
		$from_tables = $this->from->get_tables();
		$to_tables = $this->to->get_tables();

		foreach($from_tables as $from_table)
		{
			foreach($to_tables as $to_table)
			{
				if($from_table->name == $to_table->name)
				{
					$this->compare_tables($from_table, $to_table);
					break;
				}
			}
		}
	}

	private function compare_tables($from, $to)
	{
		$no_match = array();
		// For each of the columns in $from check to see if they exist in $to
		foreach($from->columns as $from_column)
		{
			foreach($to->columns as $to_column)
			{
				if($from_column->name == $to_column->name)
				{
					$this->compare_columns($from_column, $to_column);
					unset($no_match[$from_column->name]);
					break;
				}
				else
				{
					$no_match[$from_column->name] = true;
				}
			}
		}

		if(!empty($no_match))
		{
			/**
			 * @todo Deal with new columns that are in $from but not $to
			 */
		}
	}

	private function compare_columns($from, $to)
	{
		// Check the column type
		if($from->type != $to->type)
		{
			echo $from->name." type: ".$from->type." -> ".$to->type.PHP_EOL;
		}

		// Check the column length
		if($from->length != $to->length)
		{
			echo $from->name." length: ".$from->length." -> ".$to->length.PHP_EOL;
		}
	}
}