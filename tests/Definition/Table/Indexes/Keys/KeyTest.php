<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\Key;
use Tests\Database\TestCase;

final class KeyTest extends TestCase
{
	public function testType() : void
	{
		$index = new Key(static::$database, null, 'id');
		self::assertSame(
			' KEY (`id`)',
			$index->sql()
		);
	}
}
