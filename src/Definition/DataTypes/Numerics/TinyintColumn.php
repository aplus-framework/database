<?php namespace Framework\Database\Definition\DataTypes\Numerics;

class TinyintColumn extends IntColumn
{
	protected $type = 'tinyint';
	protected $maxLength = 127;
}
