<?php namespace Tests\Database\Manipulation;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements;
use PHPUnit\Framework\TestCase;

class ManipulationTest extends TestCase
{
	/**
	 * @var Manipulation
	 */
	protected $manipulation;

	public function setup()
	{
		$this->manipulation = new Manipulation(new Database());
	}

	public function testMagicGet()
	{
		$this->assertInstanceOf(Database::class, $this->manipulation->database);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Undefined property: Framework\Database\Manipulation\Manipulation::$unknown'
		);
		$this->manipulation->unknown;
	}

	public function testStatementsInstances()
	{
		$this->assertInstanceOf(
			Statements\Select::class,
			$this->manipulation->select()
		);
	}
}
