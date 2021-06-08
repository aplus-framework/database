<?php namespace Tests\Database\Definition\Table\Columns\Traits;

use Tests\Database\TestCase;

class ListLengthTest extends TestCase
{
	public function testLength()
	{
		$column = new ListLengthMock(static::$database);
		$this->assertSame(
			' mock NOT NULL',
			$column->sql()
		);
		$column = new ListLengthMock(static::$database, 1);
		$this->assertSame(
			' mock(1) NOT NULL',
			$column->sql()
		);
		$column = new ListLengthMock(static::$database, 'a', 2, 'c');
		$this->assertSame(
			" mock('a', 2, 'c') NOT NULL",
			$column->sql()
		);
	}
}
