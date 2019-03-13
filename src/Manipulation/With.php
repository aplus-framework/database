<?php namespace Framework\Database\Manipulation;

use Framework\Database\Driver\Result;

/**
 * Class With.
 *
 * @see https://mariadb.com/kb/en/library/with/
 */
class With extends Statement
{
	/**
	 * @see https://mariadb.com/kb/en/library/recursive-common-table-expressions-overview/
	 */
	public const OPT_RECURSIVE = 'RECURSIVE';

	protected function renderOptions() : ?string
	{
		if ( ! $this->hasOptions()) {
			return null;
		}
		$options = $this->sql['options'];
		foreach ($options as &$option) {
			$input = $option;
			$option = \strtoupper($option);
			if ($option !== static::OPT_RECURSIVE) {
				throw new \InvalidArgumentException("Invalid option: {$input}");
			}
		}
		return \implode(' ', $options);
	}

	/**
	 * Adds a table reference.
	 *
	 * @param \Closure|string $table
	 * @param \Closure        $alias
	 *
	 * @see https://mariadb.com/kb/en/library/non-recursive-common-table-expressions-overview/
	 * @see https://mariadb.com/kb/en/library/recursive-common-table-expressions-overview/
	 *
	 * @return $this
	 */
	public function reference($table, \Closure $alias)
	{
		$this->sql['references'][] = [
			'table' => $table,
			'alias' => $alias,
		];
		return $this;
	}

	protected function renderReference() : string
	{
		if ( ! isset($this->sql['references'])) {
			throw new \LogicException('References must be set');
		}
		$references = [];
		foreach ($this->sql['references'] as $reference) {
			$references[] = $this->renderIdentifier($reference['table'])
				. ' AS ' . $this->renderAsSelect($reference['alias']);
		}
		return \implode(', ', $references);
	}

	private function renderAsSelect(\Closure $subquery) : string
	{
		return '(' . $subquery(new Select($this->database)) . ')';
	}

	/**
	 * Sets the SELECT statement part.
	 *
	 * @param \Closure $select
	 *
	 * @return $this
	 */
	public function select(\Closure $select)
	{
		$this->sql['select'] = $select(new Select($this->database));
		return $this;
	}

	protected function renderSelect() : string
	{
		if ( ! isset($this->sql['select'])) {
			throw new \LogicException('SELECT must be set');
		}
		return $this->sql['select'];
	}

	public function sql() : string
	{
		$sql = 'WITH' . \PHP_EOL;
		if ($part = $this->renderOptions()) {
			$sql .= $part . \PHP_EOL;
		}
		$sql .= $this->renderReference() . \PHP_EOL;
		$sql .= $this->renderSelect();
		return $sql;
	}

	/**
	 * Runs the WITH statement.
	 *
	 * @return Result
	 */
	public function run() : Result
	{
		return $this->database->query($this->sql());
	}
}
