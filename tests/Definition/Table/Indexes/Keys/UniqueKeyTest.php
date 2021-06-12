<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

class UniqueKeyTest extends TestCase
{
	public function testType() : void
	{
		$index = new UniqueKey(static::$database, null, 'id');
		$this->assertEquals(
			' UNIQUE KEY (`id`)',
			$index->sql()
		);
	}
}
