<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements;
use Tests\Database\TestCase;

class ManipulationTest extends TestCase
{
	/**
	 * @var Manipulation
	 */
	protected $manipulation;

	public function setup()
	{
		$this->manipulation = new Manipulation($this->database);
	}

	public function testStatementsInstances()
	{
		$this->assertInstanceOf(
			Statements\Insert::class,
			$this->manipulation->insert()
		);
		$this->assertInstanceOf(
			Statements\LoadData::class,
			$this->manipulation->loadData()
		);
		$this->assertInstanceOf(
			Statements\Select::class,
			$this->manipulation->select()
		);
		$this->assertInstanceOf(
			Statements\Update::class,
			$this->manipulation->update()
		);
		$this->assertInstanceOf(
			Statements\With::class,
			$this->manipulation->with()
		);
	}
}
