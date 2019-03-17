<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\SpatialKey;
use Tests\Database\TestCase;

class SpatialKeyTest extends TestCase
{
	public function testType()
	{
		$index = new SpatialKey(static::$database, 'id');
		$this->assertEquals(
			' SPATIAL KEY (`id`)',
			$index->sql()
		);
	}
}
