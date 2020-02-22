<?php namespace Framework\Database\Extra;

use Framework\Database\Database;

/**
 * Class Seeder.
 */
abstract class Seeder
{
	protected Database $database;

	/**
	 * Seeder constructor.
	 *
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	abstract public function run();

	/**
	 * @param array|Seeder|Seeder[]|string $seeds
	 */
	protected function call($seeds) : void
	{
		if (\is_string($seeds)) {
			$seeds = [new $seeds($this->database)];
		} elseif (\is_array($seeds)) {
			foreach ($seeds as &$seed) {
				if (\is_string($seed)) {
					$seed = new $seed($this->database);
				}
			}
			unset($seed);
		}
		$seeds = \is_array($seeds) ? $seeds : [$seeds];
		foreach ($seeds as $seed) {
			$seed->run();
		}
	}
}
