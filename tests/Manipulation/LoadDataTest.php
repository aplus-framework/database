<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\LoadData;
use Tests\Database\TestCase;

class LoadDataTest extends TestCase
{
	protected LoadData $loadData;

	protected function setUp() : void
	{
		$this->loadData = new LoadData(static::$database);
	}

	public function testOptions()
	{
		$this->assertSame(
			"LOAD DATA\nCONCURRENT INFILE '/tmp/foo'\n INTO TABLE `Users`",
			$this->loadData->options($this->loadData::OPT_CONCURRENT)
				->infile('/tmp/foo')
				->intoTable('Users')
				->sql()
		);
	}

	public function testInvalidOption()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid option: foo');
		$this->loadData->options('foo')->sql();
	}

	public function testInvalidIntersection()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Options LOW_PRIORITY and CONCURRENT can not be used together'
		);
		$this->loadData->options(
			$this->loadData::OPT_CONCURRENT,
			$this->loadData::OPT_LOW_PRIORITY
		)->sql();
	}

	public function testCharset()
	{
		$this->assertSame(
			"LOAD DATA\n INFILE '/tmp/foo'\n INTO TABLE `users`\n CHARACTER SET utf8",
			$this->loadData->infile('/tmp/foo')
				->intoTable('users')
				->charset('utf8')
				->sql()
		);
	}

	public function testColumns()
	{
		$this->assertSame(
			"LOAD DATA\n INFILE '/tmp/foo'\n INTO TABLE `users`\n"
			. " COLUMNS\n  TERMINATED BY ','\n  OPTIONALLY ENCLOSED BY '\\\"'\n  ESCAPED BY '\\\\'",
			$this->loadData->infile('/tmp/foo')
				->intoTable('users')
				->columnsTerminatedBy(',')
				->columnsEnclosedBy('"', true)
				->columnsEscapedBy('\\')
				->sql()
		);
	}

	public function testLines()
	{
		$this->assertSame(
			"LOAD DATA\n INFILE '/tmp/foo'\n INTO TABLE `users`\n"
			. " LINES\n  STARTING BY '-'\n  TERMINATED BY '\\\\n'",
			$this->loadData->infile('/tmp/foo')
				->intoTable('users')
				->linesStartingBy('-')
				->linesTerminatedBy('\n')
				->sql()
		);
	}

	public function testWithoutInfile()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('INFILE statement is required');
		$this->loadData->intoTable('users')->sql();
	}

	public function testWithoutIntoTable()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Table is required');
		$this->loadData->infile('/tmp/foo')->sql();
	}

	public function testIgnoreLines()
	{
		$this->assertSame(
			"LOAD DATA\n INFILE '/tmp/foo'\n INTO TABLE `users`\n IGNORE 25 LINES",
			$this->loadData->intoTable('users')
				->infile('/tmp/foo')
				->ignoreLines(25)
				->sql()
		);
	}

	public function todo_testRun()
	{
		static::$database->exec(
			<<<SQL
CREATE OR REPLACE TABLE `Users` (
    `id` INT,
    `name` VARCHAR(64),
    `birthday` DATE
)
SQL
		);
		$this->loadData
			->options($this->loadData::OPT_LOCAL)
			->infile(__DIR__ . '/LoadDataTest.csv')
			->intoTable('Users')
			->run();
	}
}
