<?php namespace Tests\Database;

use Framework\Database\Database;

class TestCase extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var Database
	 */
	protected $database;

	public function __construct(...$params)
	{
		$this->setDatabase();
		parent::__construct(...$params);
	}

	protected function setDatabase() : Database
	{
		static $database;
		if ($database === null) {
			$database = new Database([
				'username' => \getenv('DB_USERNAME'),
				'password' => \getenv('DB_PASSWORD'),
				'schema' => \getenv('DB_SCHEMA'),
				'host' => \getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
				'port' => \getenv('DB_PORT'),
			]);
		}
		return $this->database = $database;
	}
}
