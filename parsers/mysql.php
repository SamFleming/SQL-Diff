<?php
namespace SqlDiff\Parsers;
use \SqlDiff as S;

class Mysql implements S\Parser
{
	private $identifier = "`?([a-zA-Z0-9$\_,]+[^`\s])`?";
	private $columns = "([a-zA-Z0-9\s\(\)`,\_'\.:-]+)";
	private $table_options = "[\sa-zA-Z0-9=_]+";

	private $data_types = array(
		"(BIT)\s?\(([0-9]+)\)", 
		"(TINYINT)\s?(?:\(([0-9]+)\))?", 
		"(SMALLINT)\s?(?:\(([0-9]+)\))?", 
		"(MEDIUMINT)\s?(?:\(([0-9]+)\))?", 
		"(INT)\s?(?:\(([0-9]+)\))?", 
		"(INTEGER)\s?(?:\(([0-9]+)\))?", 
		"(BIGINT)\s?(?:\(([0-9]+)\))?", 
		"(REAL)\s?\(([0-9]+(?:,[0-9]+))\)",
		"(DOUBLE)\s?\(([0-9]+(?:,[0-9]+))\)",
		"(FLOAT)\s?\(([0-9]+(?:,[0-9]+))\)", 
		"(DECIMAL)\s?\(([0-9]+(?:,[0-9]+)?)\)", 
		"(NUMERIC)\s?\(([0-9]+(?:,[0-9]+)?)\)", 
		"(DATE)", 
		"(TIME)", 
		"(TIMESTAMP)", 
		"(DATETIME)", 
		"(YEAR)", 
		"(CHAR)\s?\(([0-9]+)\)",
		"(VARCHAR)\s?\(([0-9]+)\)", 
		"(BINARY)\s?\(([0-9]+)\)", 
		"(VARBINARY)\s?\(([0-9]+)\)", 
		"(TINYBLOB)", 
		"(BLOB)", 
		"(MEDIUMBLOB)", 
		"(LONGBLOB)", 
		"(TINYTEXT)",
		"(TEXT)", 
		"(MEDIUMTEXT)", 
		"(LONGTEXT)", 
		"(ENUM)\((?:'[a-zA-Z0-9_$%£\s-,]+,?')\)", 
		"(SET)\((?:'[a-zA-Z0-9_$%£\s-,]+,?')\)"
	);

	public function get_tables($content, $all = false)
	{
		preg_match_all("|CREATE TABLE (?:IF NOT EXISTS )?".$this->identifier."\s?\(".$this->columns."\)\s?".$this->table_options.";|i", $content, $matches);
		return $all ? $matches : $matches[0];
	}

	public function parse_create_table($table) 
	{
		$columns = $keys = array();

		$matches = $this->get_tables($table, true);

		// Use preg split instead of explode to allow "decimal(5,2)" in column definitions
		$raw_columns = preg_split("/,\s*$/m", trim($matches[2][0]));
		foreach($raw_columns as $column)
		{
			$columnObj = $this->parse_column($column);
			$class = get_class($columnObj);
			switch($class)
			{
				case "SqlDiff\\Column":
					$columns[] = $columnObj;
					break;
				case "SqlDiff\\Key":
					$keys[] = $columnObj;
					break;
			}
		}

		return new S\Table($matches[1][0], $columns, $keys);
	}

	private function parse_column($column)
	{
		$column = strtolower(trim($column));
		if($this->is_key($column))
		{
			return $this->parse_key($column);
		}

		$column_name = $this->get_column_name($column);

		/*$type = preg_replace("/(?:.*)(".implode("|", $this->data_types).")(?:.*)/i", "$1", $column);
		$length = preg_replace("/(?:.*)(?:".implode("|", $this->data_types).")\(([0-9,]+)\)(?:.*)/i", "$1", $column);*/

		$type = $length = "";
		foreach($this->data_types as $regex)
		{
			preg_match("/.*\s".$regex.".*/i", $column, $matches);

			if(count($matches) > 1)
			{
				$type = $matches[1];
				$length = isset($matches[2]) ? $matches[2] : null;
			}
		}

		$isUnsigned = strpos($column, "unsigned") !== false ? true : false;
		$null = strpos($column, "not null") !== false ? false : true;

		return new S\Column($column, $column_name, $type, $length, $isUnsigned, $null);
	}

	/**
	 * Find out if the column is an actual column or if it is a key
	 * @param string $column A column string from a CREATE TABLE clause
	 * @return boolean TRUE if it is a key, FALSE if a column
	 */
	private function is_key($column)
	{
		return preg_match("/^(primary key|fulltext key|key|index|constraint)\s/i", $column, $key_match) !== 0;
	}

	private function parse_key($key)
	{
		return new S\Key($key);
	}

	/**
	 * From a column string - get the column name (column string like "`first_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,")
	 * @param string $column A column string from a CREATE TABLE clause
	 * @return string Name of column
	 */
	private function get_column_name($column)
	{
		return preg_replace("|^".$this->identifier."(.+)|", "$1", $column);
	}
}