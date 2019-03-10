<?php namespace Framework\Database\Manipulation\Statements;

/**
 * Class Insert.
 *
 * @see https://mariadb.com/kb/en/library/insert/
 */
class Insert extends Statement
{
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

	public function options(...$options)
	{
		foreach ($options as $option) {
			$this->sql['options'][] = $option;
		}
		return $this;
	}

	public function renderOptions() : ?string
	{
		if ( ! isset($this->sql['options'])) {
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
		return ' ' . \implode(' ', $options);
	}

	public function into(string $table)
	{
		$this->sql['into'] = $table;
		return $this;
	}

	protected function renderInto() : ?string
	{
		if ( ! isset($this->sql['into'])) {
			throw new \LogicException('INTO table must be set');
		}
		return ' INTO ' . $this->renderColumn($this->sql['into']);
	}

	public function columns(string $column, ...$columns)
	{
		$this->sql['columns'] = $columns
			? \array_merge([$column], $columns)
			: [$column];
		return $this;
	}

	protected function renderColumns() : ?string
	{
		if ( ! isset($this->sql['columns'])) {
			return null;
		}
		$columns = [];
		foreach ($this->sql['columns'] as $column) {
			$columns[] = $this->renderColumn($column);
		}
		return ' (' . \implode(', ', $columns) . ')';
	}

	public function value($value, ...$values)
	{
		$this->sql['values'] = [];
		return $this->values($value, ...$values);
	}

	public function values($value, ...$values)
	{
		$this->sql['values'][] = $values
			? \array_merge([$value], $values)
			: [$value];
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

	private function renderValue($value) : string
	{
		return $value instanceof \Closure
			? $this->subquery($value)
			: $this->manipulation->database->quote($value);
	}

	public function select(\Closure $select)
	{
		$this->sql['select'] = $select(new Select($this->manipulation));
		return $this;
	}

	protected function renderSelect() : ?string
	{
		if ( ! isset($this->sql['select'])) {
			return null;
		}
		return ' ' . $this->sql['select'];
	}

	/**
	 * @param array $column_expression      Column name as index, column value/expression as array
	 *                                      value
	 * @param mixed ...$columns_expressions Each column must be an array
	 *
	 * @return $this
	 */
	public function onDuplicateKeyUpdate(array $column_expression, ...$columns_expressions)
	{
		$columns_expressions = $columns_expressions
			? \array_merge([$column_expression], $columns_expressions)
			: [$column_expression];
		$on_duplicate = [];
		foreach ($columns_expressions as $column_expression) {
			$name = \array_key_first($column_expression);
			$on_duplicate[] = [
				'column' => $name,
				'value' => $column_expression[$name],
			];
		}
		$this->sql['on_duplicate'] = $on_duplicate;
		return $this;
	}

	protected function renderOnDuplicateKeyUpdate() : ?string
	{
		if ( ! isset($this->sql['on_duplicate'])) {
			return null;
		}
		$values = [];
		foreach ($this->sql['on_duplicate'] as $column) {
			$column['value'] = $column['value'] instanceof \Closure
				? $this->subquery($column['value'])
				: $this->manipulation->database->quote($column['value']);
			$values[] = $this->manipulation->database->protectIdentifier($column['column'])
				. ' = ' . $column['value'];
		}
		$values = \implode(', ', $values);
		return " ON DUPLICATE KEY UPDATE {$values}";
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
		if ($part = $this->renderValues()) {
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
		return $this->manipulation->database->pdo->exec($this->sql());
	}
}
