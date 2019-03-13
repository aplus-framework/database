<?php namespace Tests\Database\Manipulation\Statements;

use Framework\Database\Manipulation\Statements\Statement;

class StatementMock extends Statement
{
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

	public function renderAssignment(string $identifier, $expression) : string
	{
		return parent::renderAssignment($identifier, $expression);
	}

	public function mergeExpressions($expression, array $expressions) : array
	{
		return parent::mergeExpressions($expression, $expressions);
	}

	public function renderOptions() : ?string
	{
		if ( ! $this->hasOptions()) {
			return null;
		}
		return \implode(' ', $this->sql['options']);
	}

	public function sql() : string
	{
		return 'SQL';
	}

	public function run()
	{
	}
}
