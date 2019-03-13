<?php namespace Tests\Database;

use Framework\Database\Database;

class DatabaseMock extends Database
{
	public function __construct()
	{
		parent::__construct(
			\getenv('DB_USERNAME'),
			\getenv('DB_PASSWORD'),
			\getenv('DB_SCHEMA'),
			\getenv('GITLAB_CI') ? 'mariadb' : \getenv('DB_HOST'),
			\getenv('DB_PORT')
		);
	}
}
