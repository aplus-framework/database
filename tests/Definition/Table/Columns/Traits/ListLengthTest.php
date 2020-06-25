<?php namespace Tests\Database\Definition\Table\Columns\Traits;

use Tests\Database\TestCase;

class ListLengthTest extends TestCase
{
	public function testLength()
	{
		$column = new ListLengthMock(static::$database);
		$this->assertEquals(
			' mock NOT NULL',
			$column->sql()
		);
		$column = new ListLengthMock(static::$database, 1);
		$this->assertEquals(
			' mock(1) NOT NULL',
			$column->sql()
		);
		$column = new ListLengthMock(static::$database, 'a', 2, 'c');
		$this->assertEquals(
			" mock('a', 2, 'c') NOT NULL",
			$column->sql()
		);
	}
}
