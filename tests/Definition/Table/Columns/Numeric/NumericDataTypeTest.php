<?php namespace Tests\Database\Definition\Table\Columns\Numeric;

use Tests\Database\TestCase;

class NumericDataTypeTest extends TestCase
{
	/**
	 * @var NumericDataTypeMock
	 */
	protected $column;

	protected function setUp() : void
	{
		$this->column = new NumericDataTypeMock(static::$database);
	}

	public function testAutoIncrement()
	{
		$this->assertEquals(
			' mock AUTO_INCREMENT',
			$this->column->autoIncrement()->sql()
		);
	}

	public function testSigned()
	{
		$this->assertEquals(
			' mock signed',
			$this->column->signed()->sql()
		);
	}

	public function testUnsigned()
	{
		$this->assertEquals(
			' mock unsigned',
			$this->column->unsigned()->sql()
		);
	}

	public function testZerofill()
	{
		$this->assertEquals(
			' mock zerofill',
			$this->column->zerofill()->sql()
		);
	}

	public function testFull()
	{
		$this->assertEquals(
			' mock unsigned zerofill AUTO_INCREMENT',
			$this->column->unsigned()->zerofill()->autoIncrement()->sql()
		);
	}
}
