<?php namespace Tests\Database\Manipulation\Statements;

use Framework\Database\Manipulation\Statements\Statement;
use Tests\Database\Manipulation\ManipulationMock;

class StatementMock extends Statement
{
	public function __construct()
	{
		parent::__construct(new ManipulationMock());
	}

	public function subquery(\Closure $subquery) : string
	{
		return parent::subquery($subquery);
	}

	public function limit(int $limit, int $offset = null)
	{
		return parent::limit($limit, $offset);
	}

	public function renderLimit() : ?string
	{
		return parent::renderLimit();
	}

	public function renderColumn($column) : string
	{
		return parent::renderColumn($column);
	}

	public function renderAliasedColumn($column) : string
	{
		return parent::renderAliasedColumn($column);
	}

	public function sql() : string
	{
		return 'SQL';
	}
}
