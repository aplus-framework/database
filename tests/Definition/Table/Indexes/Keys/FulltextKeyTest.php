<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\FulltextKey;
use Tests\Database\TestCase;

final class FulltextKeyTest extends TestCase
{
	public function testType() : void
	{
		$index = new FulltextKey(static::$database, null, 'id');
		self::assertSame(
			' FULLTEXT KEY (`id`)',
			$index->sql()
		);
	}
}
