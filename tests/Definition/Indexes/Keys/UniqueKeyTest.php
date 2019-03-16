<?php namespace Tests\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

class UniqueKeyTest extends TestCase
{
	public function testType()
	{
		$index = new UniqueKey(static::$database, 'id');
		$this->assertEquals(
			' UNIQUE KEY (`id`)',
			$index->sql()
		);
	}
}
