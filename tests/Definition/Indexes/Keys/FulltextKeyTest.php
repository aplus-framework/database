<?php namespace Tests\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Keys\FulltextKey;
use Tests\Database\TestCase;

class FulltextKeyTest extends TestCase
{
	public function testType()
	{
		$index = new FulltextKey($this->database, 'id');
		$this->assertEquals(
			' FULLTEXT KEY (`id`)',
			$index->sql()
		);
	}
}
