<?php namespace Tests\Database\Definition\Table\Columns;

use Tests\Database\TestCase;

class ColumnTest extends TestCase
{
	/**
	 * @var ColumnMock
	 */
	protected $column;

	protected function setUp() : void
	{
		$this->column = new ColumnMock(static::$database);
	}

	public function testLength()
	{
		$column = new ColumnMock(static::$database, 25);
		$this->assertEquals(
			' mock(25)',
			$column->sql()
		);
		$column = new ColumnMock(static::$database, "'a'");
		$this->assertEquals(
			" mock('\\'a\\'')",
			$column->sql()
		);
	}

	public function testEmptyType()
	{
		$this->column->type = '';
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Column type is empty');
		$this->column->sql();
	}

	public function testType()
	{
		$this->assertEquals(
			' mock',
			$this->column->sql()
		);
	}

	public function testNull()
	{
		$this->assertEquals(
			' mock NULL',
			$this->column->null()->sql()
		);
		$this->assertEquals(
			' mock NOT NULL',
			$this->column->notNull()->sql()
		);
	}

	public function testDefault()
	{
		$this->assertEquals(
			" mock DEFAULT 'abc'",
			$this->column->default('abc')->sql()
		);
		$this->assertEquals(
			' mock DEFAULT (now())',
			$this->column->default(function () {
				return 'now()';
			})->sql()
		);
	}

	public function testComment()
	{
		$this->assertEquals(
			" mock COMMENT 'abc'",
			$this->column->comment('abc')->sql()
		);
	}

	public function testPrimaryKey()
	{
		$this->assertEquals(
			' mock PRIMARY KEY',
			$this->column->primaryKey()->sql()
		);
	}

	public function testUniqueKey()
	{
		$this->assertEquals(
			' mock UNIQUE KEY',
			$this->column->uniqueKey()->sql()
		);
	}

	public function testFirst()
	{
		$this->assertEquals(
			' mock FIRST',
			$this->column->first()->sql()
		);
	}

	public function testAfter()
	{
		$this->assertEquals(
			' mock AFTER `c1`',
			$this->column->after('c1')->sql()
		);
	}

	public function testFirstConflictsWithAfter()
	{
		$this->column->first()->after('c1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Clauses FIRST and AFTER can not be used together'
		);
		$this->column->sql();
	}

	public function testFull()
	{
		$column = new ColumnMock(static::$database, 10);
		$column->primaryKey()->null()->default(null)->comment('abc')->after('c1');
		$this->assertEquals(
			" mock(10) NULL PRIMARY KEY COMMENT 'abc' AFTER `c1`",
			$column->sql()
		);
	}
}
