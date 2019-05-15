<?php namespace Framework\Database\Extra;

use Framework\Database\Database;

/**
 * Class Migration.
 */
abstract class Migration
{
	/**
	 * @var Database
	 */
	protected $database;

	/**
	 * Migration constructor.
	 *
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	abstract public function up();

	abstract public function down();
}
