<?php namespace Tests\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Keys\PrimaryKey;
use Tests\Database\TestCase;

class PrimaryKeyTest extends TestCase
{
	public function testType()
	{
		$index = new PrimaryKey($this->database, 'id');
		$this->assertEquals(
			' PRIMARY KEY (`id`)',
			$index->sql()
		);
	}
}
