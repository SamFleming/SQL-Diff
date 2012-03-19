<?php
namespace SqlDiff;

class Table
{
	private $name;
	private $columns;
	private $keys;

	public function __construct($name, $columns, $keys)
	{
		$this->name = $name;
		$this->columns = $columns;
		$this->keys = $keys;
	}

	public function __toString()
	{
		return $this->name.":\n".implode("\n", $this->columns)."\n".implode("\n", $this->keys).PHP_EOL;
	}
}