<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\Definition;
use Framework\Database\Definition\Statements;
use Tests\Database\TestCase;

class DefinitionTest extends TestCase
{
	/**
	 * @var Definition
	 */
	protected $definition;

	public function setup()
	{
		$this->definition = new Definition($this->database);
	}

	public function testStatementsInstances()
	{
		$this->assertInstanceOf(
			Statements\CreateSchema::class,
			$this->definition->createSchema()
		);
		$this->assertInstanceOf(
			Statements\DropSchema::class,
			$this->definition->dropSchema()
		);
	}
}
