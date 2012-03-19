<?php
namespace SqlDiff;

class Column
{
	public $original;
	public $name;
	public $type;
	public $length;
	public $unsigned;
	public $null;

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