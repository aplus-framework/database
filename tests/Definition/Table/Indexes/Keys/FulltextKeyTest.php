<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Tests\Database\TestCase;

class FulltextKeyTest extends TestCase
{
	public function testType()
	{
		$index = new FulltextKey(static::$database, 'id');
		$this->assertEquals(
			' FULLTEXT KEY (`id`)',
			$index->sql()
		);
	}
}
