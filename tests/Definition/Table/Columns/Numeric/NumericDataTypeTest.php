<?php namespace Tests\Database\Definition\Table\Columns\Numeric;

use Tests\Database\TestCase;

class NumericDataTypeTest extends TestCase
{
	protected NumericDataTypeMock $column;

	protected function setUp() : void
	{
		$this->column = new NumericDataTypeMock(static::$database);
	}

	public function testAutoIncrement() : void
	{
		$this->assertSame(
			' mock AUTO_INCREMENT NOT NULL',
			$this->column->autoIncrement()->sql()
		);
	}

	public function testSigned() : void
	{
		$this->assertSame(
			' mock signed NOT NULL',
			$this->column->signed()->sql()
		);
	}

	public function testUnsigned() : void
	{
		$this->assertSame(
			' mock unsigned NOT NULL',
			$this->column->unsigned()->sql()
		);
	}

	public function testZerofill() : void
	{
		$this->assertSame(
			' mock zerofill NOT NULL',
			$this->column->zerofill()->sql()
		);
	}

	public function testFull() : void
	{
		$this->assertSame(
			' mock unsigned zerofill AUTO_INCREMENT NOT NULL',
			$this->column->unsigned()->zerofill()->autoIncrement()->sql()
		);
	}
}
