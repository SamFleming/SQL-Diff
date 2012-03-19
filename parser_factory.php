<?php
namespace SqlDiff;
use \SqlDiff\Parsers as SP;

class ParserFactory
{
	public function getParser($type = "Mysql")
	{
		switch($type)
		{
			case "Mysql":
				return new SP\Mysql;
		}
		return null;
	}
}