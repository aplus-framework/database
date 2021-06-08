<?php namespace Tests\Database\Definition\Table\Columns\Numeric;

use Tests\Database\TestCase;

class NumericDataTypeTest extends TestCase
{
	protected NumericDataTypeMock $column;

	protected function setUp() : void
	{
		$this->column = new NumericDataTypeMock(static::$database);
	}

	public function testAutoIncrement()
	{
		$this->assertSame(
			' mock AUTO_INCREMENT NOT NULL',
			$this->column->autoIncrement()->sql()
		);
	}

	public function testSigned()
	{
		$this->assertSame(
			' mock signed NOT NULL',
			$this->column->signed()->sql()
		);
	}

	public function testUnsigned()
	{
		$this->assertSame(
			' mock unsigned NOT NULL',
			$this->column->unsigned()->sql()
		);
	}

	public function testZerofill()
	{
		$this->assertSame(
			' mock zerofill NOT NULL',
			$this->column->zerofill()->sql()
		);
	}

	public function testFull()
	{
		$this->assertSame(
			' mock unsigned zerofill AUTO_INCREMENT NOT NULL',
			$this->column->unsigned()->zerofill()->autoIncrement()->sql()
		);
	}
}
