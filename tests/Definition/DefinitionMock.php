<?php namespace Tests\Database\Definition;

use Framework\Database\Definition\Definition;
use Tests\Database\DatabaseMock;

class DefinitionMock extends Definition
{
	public function __construct()
	{
		parent::__construct(new DatabaseMock());
	}
}
