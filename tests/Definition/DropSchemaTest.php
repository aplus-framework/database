<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\DropSchema;
use Tests\Database\TestCase;

class DropSchemaTest extends TestCase
{
	/**
	 * @var DropSchema
	 */
	protected $dropSchema;

	protected function setUp()
	{
		$this->dropSchema = new DropSchema($this->database);
	}

	public function testEmptySchema()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SCHEMA name must be set');
		$this->dropSchema->sql();
	}

	public function testSchema()
	{
		$this->assertEquals(
			"DROP SCHEMA `app`\n",
			$this->dropSchema->schema('app')->sql()
		);
	}

	public function testIfExists()
	{
		$this->assertEquals(
			"DROP SCHEMA IF EXISTS `app`\n",
			$this->dropSchema->ifExists()->schema('app')->sql()
		);
	}
}
