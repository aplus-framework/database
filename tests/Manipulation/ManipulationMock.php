<?php namespace Tests\Database\Manipulation;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;

class ManipulationMock extends Manipulation
{
	public function __construct()
	{
		parent::__construct(new Database());
	}
}
