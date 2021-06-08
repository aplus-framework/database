<?php namespace Tests\Database\Definition\Table\Columns\String;

use Tests\Database\TestCase;

class StringDataTypeTest extends TestCase
{
	protected StringDataTypeMock $column;

	protected function setUp() : void
	{
		$this->column = new StringDataTypeMock(static::$database);
	}

	public function testCharset()
	{
		$this->assertSame(
			" mock CHARACTER SET 'utf8' NOT NULL",
			$this->column->charset('utf8')->sql()
		);
	}

	public function testCollate()
	{
		$this->assertSame(
			" mock COLLATE 'utf8_general_ci' NOT NULL",
			$this->column->collate('utf8_general_ci')->sql()
		);
	}

	public function testFull()
	{
		$this->assertSame(
			" mock CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL",
			$this->column->collate('utf8_general_ci')->charset('utf8')->sql()
		);
	}
}
