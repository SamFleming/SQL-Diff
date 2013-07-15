<?php
namespace SqlDiff;

class Table
{
	public $name;
	public $columns;
	public $keys;

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

    public function get_column($name)
    {
        foreach ($this->columns as $column)
        {
            if ($name === $column->get_name())
            {
                return $column;
            }
        }
        return new stdClass;
    }
}