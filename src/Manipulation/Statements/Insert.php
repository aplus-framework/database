<?php namespace Framework\Database\Manipulation\Statements;

/**
 * Class Insert.
 *
 * @see https://mariadb.com/kb/en/library/insert/
 */
class Insert extends Statement
{
	use Traits\Set;
	/**
	 * @see https://mariadb.com/kb/en/library/insert-delayed/
	 */
	public const OPT_DELAYED = 'DELAYED';
	/**
	 * Convert errors to warnings, which will not stop inserts of additional rows.
	 *
	 * @see https://mariadb.com/kb/en/library/insert-ignore/
	 */
	public const OPT_IGNORE = 'IGNORE';
	/**
	 * @see https://mariadb.com/kb/en/library/high_priority-and-low_priority/
	 */
	public const OPT_HIGH_PRIORITY = 'HIGH_PRIORITY';
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
				static::OPT_DELAYED,
				static::OPT_IGNORE,
				static::OPT_LOW_PRIORITY,
				static::OPT_HIGH_PRIORITY,
			], true)) {
				throw new \InvalidArgumentException("Invalid option: {$input}");
			}
		}
		unset($option);
		$intersection = \array_intersect(
			$options,
			[static::OPT_DELAYED, static::OPT_HIGH_PRIORITY, static::OPT_LOW_PRIORITY]
		);
		if (\count($intersection) > 1) {
			throw new \LogicException(
				'Options LOW_PRIORITY, DELAYED or HIGH_PRIORITY can not be used together'
			);
		}
		$options = \implode(' ', $options);
		return " {$options}";
	}

	/**
	 * Sets the INTO table.
	 *
	 * @param string $table Table name
	 *
	 * @return $this
	 */
	public function into(string $table)
	{
		$this->sql['into'] = $table;
		return $this;
	}

	protected function renderInto() : string
	{
		if ( ! isset($this->sql['into'])) {
			throw new \LogicException('INTO table must be set');
		}
		return ' INTO ' . $this->renderIdentifier($this->sql['into']);
	}

	/**
	 * Sets the INTO columns.
	 *
	 * @param string $column
	 * @param mixed  $columns
	 *
	 * @return $this
	 */
	public function columns(string $column, ...$columns)
	{
		$this->sql['columns'] = $this->mergeExpressions($column, $columns);
		return $this;
	}

	protected function renderColumns() : ?string
	{
		if ( ! isset($this->sql['columns'])) {
			return null;
		}
		$columns = [];
		foreach ($this->sql['columns'] as $column) {
			$columns[] = $this->renderIdentifier($column);
		}
		$columns = \implode(', ', $columns);
		return " ({$columns})";
	}

	/**
	 * Adds a row values to the VALUES clause.
	 *
	 * @param \Closure|float|int|string|null $value
	 * @param mixed                          ...$values
	 *
	 * @return $this
	 */
	public function values($value, ...$values)
	{
		$this->sql['values'][] = $this->mergeExpressions($value, $values);
		return $this;
	}

	protected function renderValues() : ?string
	{
		if ( ! isset($this->sql['values'])) {
			return null;
		}
		$values = [];
		foreach ($this->sql['values'] as $value) {
			foreach ($value as &$item) {
				$item = $this->renderValue($item);
			}
			unset($item);
			$values[] = ' (' . \implode(', ', $value) . ')';
		}
		$values = \implode(',' . \PHP_EOL, $values);
		return " VALUES{$values}";
	}

	/**
	 * Sets the SELECT statement part.
	 *
	 * @param \Closure $select
	 *
	 * @see https://mariadb.com/kb/en/library/insert-select/
	 *
	 * @return $this
	 */
	public function select(\Closure $select)
	{
		$this->sql['select'] = $select(new Select($this->database));
		return $this;
	}

	protected function renderSelect() : ?string
	{
		if ( ! isset($this->sql['select'])) {
			return null;
		}
		if (isset($this->sql['values'])) {
			throw new \LogicException('SELECT statement is not allowed when VALUES is set');
		}
		if (isset($this->sql['set'])) {
			throw new \LogicException('SELECT statement is not allowed when SET is set');
		}
		return " {$this->sql['select']}";
	}

	/**
	 * Sets the ON DUPLICATE KEY UPDATE part.
	 *
	 * @param array $columns Column name as index, column value/expression as array value
	 *
	 * @see https://mariadb.com/kb/en/library/insert-on-duplicate-key-update/
	 *
	 * @return $this
	 */
	public function onDuplicateKeyUpdate(array $columns)
	{
		$this->sql['on_duplicate'] = $columns;
		return $this;
	}

	protected function renderOnDuplicateKeyUpdate() : ?string
	{
		if ( ! isset($this->sql['on_duplicate'])) {
			return null;
		}
		$on_duplicate = [];
		foreach ($this->sql['on_duplicate'] as $column => $value) {
			$on_duplicate[] = $this->renderAssignment($column, $value);
		}
		$on_duplicate = \implode(', ', $on_duplicate);
		return " ON DUPLICATE KEY UPDATE {$on_duplicate}";
	}

	protected function renderSetPart() : ?string
	{
		$part = $this->renderSet();
		if ($part) {
			if (isset($this->sql['columns'])) {
				throw new \LogicException('SET statement is not allowed when columns are set');
			}
			if (isset($this->sql['values'])) {
				throw new \LogicException('SET statement is not allowed when VALUES is set');
			}
		}
		return $part;
	}

	protected function checkRowStatementsConflict() : void
	{
		if ( ! isset($this->sql['values'])
			&& ! isset($this->sql['select'])
			&& ! $this->hasSet()
		) {
			throw new \LogicException(
				'The INSERT INTO must be followed by VALUES, SET or SELECT statement'
			);
		}
	}

	public function sql() : string
	{
		$sql = 'INSERT' . \PHP_EOL;
		if ($part = $this->renderOptions()) {
			$sql .= $part . \PHP_EOL;
		}
		$sql .= $this->renderInto() . \PHP_EOL;
		if ($part = $this->renderColumns()) {
			$sql .= $part . \PHP_EOL;
		}
		$this->checkRowStatementsConflict();
		if ($part = $this->renderValues()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderSetPart()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderSelect()) {
			$sql .= $part . \PHP_EOL;
		}
		if ($part = $this->renderOnDuplicateKeyUpdate()) {
			$sql .= $part . \PHP_EOL;
		}
		return $sql;
	}

	public function run()
	{
		return $this->database->pdo->exec($this->sql());
	}
}
