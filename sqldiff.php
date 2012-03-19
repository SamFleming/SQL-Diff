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
	public function __construct(Datasource $base, Datasource $target)
	{
		$base_tables = $base->get_tables();
		/*$target_tables = $target->get_tables();*/

		print_r($base_tables);
		echo "Total Tables: ".count($base_tables);
	}
}