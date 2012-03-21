<?php
namespace SqlDiff;

class Change
{
	private $type;
	private $attribute;
	private $from;
	private $from_col_key;
	private $to;
	private $to_col_key;

	public function __construct($type, $attribute, $from, $from_col_key, $to, $to_col_key)
	{
		$this->type = $type;
		$this->attribute = $attribute;
		$this->from = $from;
		$this->to = $to;
		$this->from_col_key = $from_col_key;
		$this->to_col_key = $to_col_key;
	}

	public function type()
	{
		return $this->type;
	}

	public function table_name()
	{
		return $this->from->name;
	}

	public function column_name()
	{
		return $this->from->columns[$this->from_col_key]->name;
	}

	public function attribute()
	{
		return $this->attribute;
	}

	public function old_value()
	{
		return $this->from->columns[$this->from_col_key]->{$this->attribute};
	}

	public function new_value()
	{
		return $this->new_column()->{$this->attribute};
	}

	public function new_column()
	{
		return $this->to->columns[$this->to_col_key];
	}
}