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
		return $this->setLimit($limit, $offset);
	}

	public function renderLimit() : ?string
	{
		return parent::renderLimit();
	}

	public function renderIdentifier($column) : string
	{
		return parent::renderIdentifier($column);
	}

	public function renderAliasedIdentifier($column) : string
	{
		return parent::renderAliasedIdentifier($column);
	}

	protected function renderOptions() : ?string
	{
	}

	public function sql() : string
	{
		return 'SQL';
	}

	public function run()
	{
	}
}
