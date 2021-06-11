<?php namespace Tests\Database\Definition\Table\Columns\Traits;

use Tests\Database\TestCase;

class DecimalLengthTest extends TestCase
{
	public function testLength() : void
	{
		$column = new DecimalLengthMock(static::$database);
		$this->assertSame(
			' mock NOT NULL',
			$column->sql()
		);
		$column = new DecimalLengthMock(static::$database, 12);
		$this->assertSame(
			' mock(12) NOT NULL',
			$column->sql()
		);
		$column = new DecimalLengthMock(static::$database, 16, 4);
		$this->assertSame(
			' mock(16,4) NOT NULL',
			$column->sql()
		);
	}
}
