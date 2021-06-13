<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\PrimaryKey;
use Tests\Database\TestCase;

final class PrimaryKeyTest extends TestCase
{
	public function testType() : void
	{
		$index = new PrimaryKey(static::$database, null, 'id');
		self::assertSame(
			' PRIMARY KEY (`id`)',
			$index->sql()
		);
	}
}
