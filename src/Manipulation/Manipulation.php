<?php namespace Framework\Database\Manipulation;

use Framework\Database\Database;
use Framework\Database\Manipulation\Statements\Insert;
use Framework\Database\Manipulation\Statements\Select;
use Framework\Database\Manipulation\Statements\Update;
use Framework\Database\Manipulation\Statements\With;

/**
 * Class Manipulation.
 *
 * @see https://mariadb.com/kb/en/library/data-manipulation/
 *
 * @property-read Database $database
 */
class Manipulation
{
	protected $database;

	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function __get($property)
	{
		if ($property === 'database') {
			return $this->database;
		}
		throw new \LogicException('Undefined property: ' . __CLASS__ . '::$' . $property);
	}

	public function insert() : Insert
	{
		return new Insert($this->database);
	}

	public function select() : Select
	{
		return new Select($this->database);
	}

	public function update() : Update
	{
		return new Update($this->database);
	}

	public function with() : With
	{
		return new With($this->database);
	}
}
