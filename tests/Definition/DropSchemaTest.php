<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\DropSchema;
use Tests\Database\TestCase;

class DropSchemaTest extends TestCase
{
	protected DropSchema $dropSchema;

	protected function setUp() : void
	{
		$this->dropSchema = new DropSchema(static::$database);
	}

	public function testEmptySchema()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SCHEMA name must be set');
		$this->dropSchema->sql();
	}

	public function testSchema()
	{
		$this->assertSame(
			"DROP SCHEMA `app`\n",
			$this->dropSchema->schema('app')->sql()
		);
	}

	public function testIfExists()
	{
		$this->assertSame(
			"DROP SCHEMA IF EXISTS `app`\n",
			$this->dropSchema->ifExists()->schema('app')->sql()
		);
	}

	public function testRun()
	{
		static::$database->createSchema('app')->ifNotExists()->run();
		$this->assertSame(
			0,
			$this->dropSchema->schema('app')->run()
		);
	}
}
