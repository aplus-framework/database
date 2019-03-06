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
	protected $manipulation;
	protected $sql = [];

	public function __construct(Manipulation $manipulation)
	{
		$this->manipulation = $manipulation;
	}

	abstract public function sql() : string;

	protected function subquery(\Closure $subquery) : string
	{
		return '(' . $subquery($this->manipulation) . ')';
	}

	/**
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

	protected function renderLimit() : string
	{
		if ( ! isset($this->sql['limit'])) {
			return '';
		}
		if ($this->sql['limit']['limit'] < 1) {
			throw new \InvalidArgumentException('LIMIT must be greater than 0');
		}
		$offset = $this->sql['limit']['offset'];
		if ($offset) {
			if ($offset < 1) {
				throw new \InvalidArgumentException('LIMIT OFFSET must be greater than 0');
			}
			$offset = " OFFSET {$this->sql['limit']['offset']}";
		}
		return " LIMIT {$this->sql['limit']['limit']}{$offset}";
	}

	/**
	 * @param array|\Closure|string $column
	 *
	 * @return string
	 */
	protected function renderColumn($column) : string
	{
		if (\is_array($column)) {
			if (\count($column) !== 1) {
				throw new \InvalidArgumentException('Aliased column must have only 1 key');
			}
			$alias = \array_key_first($column);
			$name = $column[$alias];
			$column = $name instanceof \Closure
					? $this->subquery($name)
					: $this->manipulation->database->protectIdentifier($name);
			return $column . ' AS ' . $this->manipulation->database->protectIdentifier($alias);
		}
		return $column instanceof \Closure
			? $this->subquery($column)
			: $this->manipulation->database->protectIdentifier($column);
	}
}
