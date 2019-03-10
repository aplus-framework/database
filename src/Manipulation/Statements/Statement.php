<?php namespace Framework\Database\Manipulation\Statements;

use Framework\Database\Manipulation\Manipulation;

/**
 * Class Statement.
 *
 * @see https://mariadb.com/kb/en/library/sql-statements/
 * @see https://mariadb.com/kb/en/library/data-manipulation/
 */
abstract class Statement
{
	/**
	 * @var Manipulation
	 */
	protected $manipulation;
	/**
	 * SQL clauses and parts.
	 *
	 * @var array
	 */
	protected $sql = [];

	/**
	 * Statement constructor.
	 *
	 * @param Manipulation $manipulation
	 */
	public function __construct(Manipulation $manipulation)
	{
		$this->manipulation = $manipulation;
	}

	public function __toString()
	{
		return $this->sql();
	}

	/**
	 * Renders the statement.
	 *
	 * @return string
	 */
	abstract public function sql() : string;

	/**
	 * Returns a SQL part between parentheses.
	 *
	 * @param \Closure $subquery A \Closure having the current Manipulation instance as first
	 *                           argument. The returned value must be scalar
	 *
	 * @see https://mariadb.com/kb/en/library/subqueries/
	 * @see https://mariadb.com/kb/en/library/built-in-functions/
	 *
	 * @return string
	 */
	protected function subquery(\Closure $subquery) : string
	{
		return '(' . $subquery($this->manipulation) . ')';
	}

	/**
	 * Sets the LIMIT clause.
	 *
	 * @param int      $limit
	 * @param int|null $offset
	 *
	 * @see https://mariadb.com/kb/en/library/limit/
	 *
	 * @return $this
	 */
	protected function limit(int $limit, int $offset = null)
	{
		$this->sql['limit'] = [
			'limit' => $limit,
			'offset' => $offset,
		];
		return $this;
	}

	/**
	 * Renders the LIMIT clause.
	 *
	 * @return string|null
	 */
	protected function renderLimit() : ?string
	{
		if ( ! isset($this->sql['limit'])) {
			return null;
		}
		if ($this->sql['limit']['limit'] < 1) {
			throw new \InvalidArgumentException('LIMIT must be greater than 0');
		}
		$offset = $this->sql['limit']['offset'];
		if ($offset !== null) {
			if ($offset < 1) {
				throw new \InvalidArgumentException('LIMIT OFFSET must be greater than 0');
			}
			$offset = " OFFSET {$this->sql['limit']['offset']}";
		}
		return " LIMIT {$this->sql['limit']['limit']}{$offset}";
	}

	/**
	 * Renders a column part.
	 *
	 * @param \Closure|string $column The column name or a subquery
	 *
	 * @return string
	 */
	protected function renderColumn($column) : string
	{
		return $column instanceof \Closure
			? $this->subquery($column)
			: $this->manipulation->database->protectIdentifier($column);
	}

	/**
	 * Renders a column part with an optional alias name, AS clause.
	 *
	 * @param array|\Closure|string $column The column name, a subquery or an array where the index
	 *                                      is the alias and the value is the column/subquery
	 *
	 * @return string
	 */
	protected function renderAliasedColumn($column) : string
	{
		if (\is_array($column)) {
			if (\count($column) !== 1) {
				throw new \InvalidArgumentException('Aliased column must have only 1 key');
			}
			$alias = \array_key_first($column);
			return $this->renderColumn($column[$alias]) . ' AS '
				. $this->manipulation->database->protectIdentifier($alias);
		}
		return $this->renderColumn($column);
	}

	/**
	 * Renders a subquery or quote a value.
	 *
	 * @param \Closure|float|int|string|null $value \Closure for subquery, other types to quote
	 *
	 * @return string
	 */
	protected function renderValue($value) : string
	{
		return $value instanceof \Closure
			? $this->subquery($value)
			: $this->manipulation->database->quote($value);
	}

	/**
	 * Renders an assignment part.
	 *
	 * @param string                         $identifier Identifier/column name
	 * @param \Closure|float|int|string|null $expression Expression/value
	 *
	 * @see renderValue
	 * @see https://mariadb.com/kb/en/library/assignment-operators-assignment-operator/
	 *
	 * @return string
	 */
	protected function renderAssignment(string $identifier, $expression) : string
	{
		return $this->manipulation->database->protectIdentifier($identifier)
			. ' = ' . $this->renderValue($expression);
	}
}
