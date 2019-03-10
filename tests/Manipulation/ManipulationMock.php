<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Manipulation;
use Tests\Database\DatabaseMock;

class ManipulationMock extends Manipulation
{
	public function __construct()
	{
		parent::__construct(new DatabaseMock());
	}
}
