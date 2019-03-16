<?php namespace Framework\Database\Definition\Columns\Numeric;

class TinyintColumn extends IntColumn
{
	protected $type = 'tinyint';
	protected $maxLength = 127;
}
