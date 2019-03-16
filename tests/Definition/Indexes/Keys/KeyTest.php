<?php namespace Tests\Database\Definition\Indexes\Keys;

use Framework\Database\Definition\Indexes\Keys\Key;
use Tests\Database\TestCase;

class KeyTest extends TestCase
{
	public function testType()
	{
		$index = new Key($this->database, 'id');
		$this->assertEquals(
			' KEY (`id`)',
			$index->sql()
		);
	}
}
