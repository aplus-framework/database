<?php namespace Tests\Database\Manipulation\Statements;

use Framework\Database\Database;
use Framework\Database\Manipulation\Manipulation;
use Framework\Database\Manipulation\Statements\Statement;

class StatementMock extends Statement
{
	public function __construct()
	{
		parent::__construct(new Manipulation(new Database()));
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
	}
}
