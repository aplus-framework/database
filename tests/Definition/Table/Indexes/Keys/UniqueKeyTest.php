<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\UniqueKey;
use Tests\Database\TestCase;

final class UniqueKeyTest extends TestCase
{
	public function testType() : void
	{
		$index = new UniqueKey(static::$database, null, 'id');
		$this->assertSame(
			' UNIQUE KEY (`id`)',
			$index->sql()
		);
	}
}
