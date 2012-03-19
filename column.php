<?php
namespace SqlDiff;

class Column
{
	private $original;
	private $name;
	private $type;
	private $length;
	private $unsigned;
	private $null;

	public function __construct($original, $column_name, $type, $length, $unsigned, $null)
	{
		$this->original = $original;
		$this->name = $column_name;
		$this->type = $type;
		$this->length = $length;
		$this->unsigned = $unsigned;
		$this->null = $null;
	}

	public function __toString()
	{
		return $this->name." ".$this->type."(".$this->length.")";
	}
}