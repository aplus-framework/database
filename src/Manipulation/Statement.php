<?php namespace Framework\Database\Manipulation;

use Framework\Database\Database;

/**
 * Class Statement.
 *
 * @see https://mariadb.com/kb/en/library/sql-statements/
 * @see https://mariadb.com/kb/en/library/data-manipulation/
 */
abstract class Statement
{
	/**
	 * @var Database
	 */
	protected $database;
	/**
	 * SQL clauses and parts.
	 *
	 * @var array
	 */
	protected $sql = [];

	/**
	 * Statement constructor.
	 *
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	public function __toString()
	{
		return $this->sql();
	}

	/**
	 * Resets SQL clauses and parts.
	 *
	 * @param string|null $sql A part name or null to reset all
	 *
	 * @see Statement::$sql
	 *
	 * @return $this
	 */
	public function reset(string $sql = null)
	{
		if ($sql === null) {
			unset($this->sql);
			return $this;
		}
		unset($this->sql[$sql]);
		return $this;
	}

	/**
	 * Sets the statement options.
	 *
	 * @param string $option  One of the OPT_* constants
	 * @param mixed  $options Each option value must be one of the OPT_* constants
	 *
	 * @return $this
	 */
	public function options($option, ...$options)
	{
		$this->sql['options'] = [];
		$options = $this->mergeExpressions($option, $options);
		foreach ($options as $option) {
			$this->sql['options'][] = $option;
		}
		return $this;
	}

	protected function hasOptions() : bool
	{
		return isset($this->sql['options']);
	}

	abstract protected function renderOptions() : ?string;

	/**
	 * Renders the SQL statement.
	 *
	 * @return string
	 */
	abstract public function sql() : string;

	/**
	 * Runs the SQL statement.
	 */
	abstract public function run();

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
		return '(' . $subquery($this->database) . ')';
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
	protected function setLimit(int $limit, int $offset = null)
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
	protected function renderIdentifier($column) : string
	{
		return $column instanceof \Closure
			? $this->subquery($column)
			: $this->database->protectIdentifier($column);
	}

	/**
	 * Renders a column part with an optional alias name, AS clause.
	 *
	 * @param array|\Closure|string $column The column name, a subquery or an array where the index
	 *                                      is the alias and the value is the column/subquery
	 *
	 * @return string
	 */
	protected function renderAliasedIdentifier($column) : string
	{
		if (\is_array($column)) {
			if (\count($column) !== 1) {
				throw new \InvalidArgumentException('Aliased column must have only 1 key');
			}
			$alias = \array_key_first($column);
			return $this->renderIdentifier($column[$alias]) . ' AS '
				. $this->database->protectIdentifier($alias);
		}
		return $this->renderIdentifier($column);
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
			: $this->database->quote($value);
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
		return $this->database->protectIdentifier($identifier)
			. ' = ' . $this->renderValue($expression);
	}

	/**
	 * Used when a function requires at least one expression (identifier or value).
	 *
	 * @param mixed         $expression
	 * @param array|mixed[] $expressions
	 *
	 * @return array
	 */
	protected function mergeExpressions($expression, array $expressions) : array
	{
		return $expressions
			? \array_merge([$expression], $expressions)
			: [$expression];
	}
}
