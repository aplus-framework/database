<?php namespace Framework\Database\Manipulation;

/**
 * Class Update.
 *
 * @see https://mariadb.com/kb/en/library/update/
 */
class Update extends Statement
{
	use Traits\Set;
	use Traits\Where;
	use Traits\OrderBy;
	/**
	 * Convert errors to warnings, which will not stop inserts of additional rows.
	 *
	 * @see https://mariadb.com/kb/en/library/insert-ignore/
	 */
	public const OPT_IGNORE = 'IGNORE';
	/**
	 * @see https://mariadb.com/kb/en/library/high_priority-and-low_priority/
	 */
	public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';

	protected function renderOptions() : ?string
	{
		if ( ! $this->hasOptions()) {
			return null;
		}
		$options = $this->sql['options'];
		foreach ($options as &$option) {
			$input = $option;
			$option = \strtoupper($option);
			if ( ! \in_array($option, [
				static::OPT_IGNORE,
				static::OPT_LOW_PRIORITY,
			], true)) {
				throw new \InvalidArgumentException("Invalid option: {$input}");
			}
		}
		unset($option);
		$options = \implode(' ', $options);
		return " {$options}";
	}

	/**
	 * Sets the table references.
	 *
	 * @param array|\Closure|string $reference
	 * @param mixed                 ...$references
	 *
	 * @return $this
	 */
	public function table($reference, ...$references)
	{
		$this->sql['table'] = [];
		$references = $this->mergeExpressions($reference, $references);
		foreach ($references as $reference) {
			$this->sql['table'][] = $reference;
		}
		return $this;
	}

	protected function renderTable() : string
	{
		if ( ! isset($this->sql['table'])) {
			throw new \LogicException('Table references must be set');
		}
		$tables = [];
		foreach ($this->sql['table'] as $table) {
			$tables[] = $this->renderAliasedIdentifier($table);
		}
		return ' ' . \implode(', ', $tables);
	}

	/**
	 * Sets the LIMIT clause.
	 *
	 * @param int $limit
	 *
	 * @see https://mariadb.com/kb/en/library/limit/
	 *
	 * @return $this
	 */
	public function limit(int $limit)
	{
		return $this->setLimit($limit);
	}

	protected function renderSetPart() : string
	{
		if ( ! $this->hasSet()) {
			throw new \LogicException('SET statement must be set');
		}
		return $this->renderSet();
	}

	public function sql() : string
	{
		$sql = 'UPDATE' . \PHP_EOL;
		if ($part = $this->renderOptions()) {
			$sql .= $part . \PHP_EOL;
		}
		$sql .= $this->renderTable() . \PHP_EOL;
		$sql .= $this->renderSetPart() . \PHP_EOL;
		if ($part = $this->renderWhere()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderOrderBy()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderLimit()) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	/**
	 * Runs the UPDATE statement.
	 *
	 * @return int The number of affected rows
	 */
	public function run() : int
	{
		return $this->database->exec($this->sql());
	}
}
