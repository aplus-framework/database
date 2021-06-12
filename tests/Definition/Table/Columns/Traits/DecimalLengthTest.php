<?php namespace Tests\Database\Definition\Table\Columns\Traits;

use Tests\Database\TestCase;

final class DecimalLengthTest extends TestCase
{
	public function testLength() : void
	{
		$column = new DecimalLengthMock(static::$database);
		$this->assertEquals(
			' mock NOT NULL',
			$column->sql()
		);
		$column = new DecimalLengthMock(static::$database, 12);
		$this->assertEquals(
			' mock(12) NOT NULL',
			$column->sql()
		);
		$column = new DecimalLengthMock(static::$database, 16, 4);
		$this->assertEquals(
			' mock(16,4) NOT NULL',
			$column->sql()
		);
	}
}
