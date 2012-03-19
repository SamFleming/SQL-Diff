<?php
namespace SqlDiff;

interface Parser
{
	public function get_tables($contents);
	public function parse_create_table($table);
}