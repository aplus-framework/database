<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\AlterSchema;
use Tests\Database\TestCase;

class AlterSchemaTest extends TestCase
{
	protected AlterSchema $alterSchema;

	protected function setUp() : void
	{
		$this->alterSchema = new AlterSchema(static::$database);
	}

	public function testSchemaWithoutSpecification()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('ALTER SCHEMA must have a specification');
		$this->alterSchema->schema('app')->sql();
	}

	public function testEmptySchema()
	{
		$this->assertEquals(
			"ALTER SCHEMA\n CHARACTER SET = 'utf8'\n",
			$this->alterSchema->charset('utf8')->sql()
		);
	}

	public function testCharset()
	{
		$this->assertEquals(
			"ALTER SCHEMA `app`\n CHARACTER SET = 'utf8'\n",
			$this->alterSchema->schema('app')->charset('utf8')->sql()
		);
	}

	public function testCollate()
	{
		$this->assertEquals(
			"ALTER SCHEMA `app`\n COLLATE = 'utf8_general_ci'\n",
			$this->alterSchema->schema('app')->collate('utf8_general_ci')->sql()
		);
	}

	public function testUpgrade()
	{
		$this->assertEquals(
			"ALTER SCHEMA `#mysql50#app`\n UPGRADE DATA DIRECTORY NAME\n",
			$this->alterSchema->schema('app')->upgrade()->sql()
		);
	}

	public function testUpgradeConflict()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'UPGRADE DATA DIRECTORY NAME can not be used with CHARACTER SET or COLLATE'
		);
		$this->alterSchema->schema('app')->upgrade()->charset('utf8')->sql();
	}

	public function testFullSql()
	{
		$this->assertEquals(
			"ALTER SCHEMA `app`\n CHARACTER SET = 'utf8'\n COLLATE = 'utf8_general_ci'\n",
			$this->alterSchema
				->schema('app')
				->charset('utf8')
				->collate('utf8_general_ci')
				->sql()
		);
	}
}
