<?php namespace Tests\Database\Definition\Table\Columns\Traits;

use Tests\Database\TestCase;

final class ListLengthTest extends TestCase
{
	public function testLength() : void
	{
		$column = new ListLengthMock(static::$database);
		self::assertSame(
			' mock NOT NULL',
			$column->sql()
		);
		$column = new ListLengthMock(static::$database, 1);
		self::assertSame(
			' mock(1) NOT NULL',
			$column->sql()
		);
		$column = new ListLengthMock(static::$database, 'a', 2, 'c');
		self::assertSame(
			" mock('a', 2, 'c') NOT NULL",
			$column->sql()
		);
	}
}
