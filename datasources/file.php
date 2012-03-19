<?php
namespace SqlDiff\Datasources;
use \SqlDiff as S;

class File implements S\Datasource
{
	private $file;
	private $file_content;
	private $tables;

	public function __construct($file)
	{
		if(!is_readable($file))
		{
			throw new S\SdException("Unable to read datasource file: ".$file);
		}

		$this->file = $file;
	}

	private function parse_sql_file()
	{
		$parser_factory = new S\ParserFactory();
		$parser = $parser_factory->getParser();

		$tables = array();

		$content = file_get_contents($this->file);

		// Get all the create table statements
		$tables = $parser->get_tables($content);

		// Loop through each of our tables and parse them
		foreach($tables as $table_str)
		{
			$this->tables[] = $parser->parse_create_table($table_str);
		}
	}

	public function get_tables()
	{
		$this->parse_sql_file();
		return $this->tables;
	}
}