<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\CreateSchema;
use Tests\Database\TestCase;

class CreateSchemaTest extends TestCase
{
	protected CreateSchema $createSchema;

	protected function setUp() : void
	{
		$this->createSchema = new CreateSchema(static::$database);
	}

	public function testEmptySchema()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SCHEMA name must be set');
		$this->createSchema->sql();
	}

	public function testSchema()
	{
		$this->assertEquals(
			"CREATE SCHEMA `app`\n",
			$this->createSchema->schema('app')->sql()
		);
	}

	public function testOrReplace()
	{
		$this->assertEquals(
			"CREATE OR REPLACE SCHEMA `app`\n",
			$this->createSchema->orReplace()->schema('app')->sql()
		);
	}

	public function testIfNotExists()
	{
		$this->assertEquals(
			"CREATE SCHEMA IF NOT EXISTS `app`\n",
			$this->createSchema->ifNotExists()->schema('app')->sql()
		);
	}

	public function testOrReplaceWithIfNotExists()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Clauses OR REPLACE and IF NOT EXISTS can not be used together');
		$this->createSchema->orReplace()->ifNotExists()->schema('app')->sql();
	}

	public function testCharset()
	{
		$this->assertEquals(
			"CREATE SCHEMA `app`\n CHARACTER SET = 'utf8'\n",
			$this->createSchema->schema('app')->charset('utf8')->sql()
		);
	}

	public function testCollate()
	{
		$this->assertEquals(
			"CREATE SCHEMA `app`\n COLLATE = 'utf8_general_ci'\n",
			$this->createSchema->schema('app')->collate('utf8_general_ci')->sql()
		);
	}

	public function testFullSql()
	{
		$this->assertEquals(
			"CREATE SCHEMA IF NOT EXISTS `app`\n CHARACTER SET = 'utf8'\n COLLATE = 'utf8_general_ci'\n",
			$this->createSchema->ifNotExists()
				->schema('app')
				->charset('utf8')
				->collate('utf8_general_ci')
				->sql()
		);
	}

	public function testRun()
	{
		static::$database->dropSchema('app')->ifExists()->run();
		$this->assertEquals(
			1,
			$this->createSchema->schema('app')->run()
		);
	}
}
