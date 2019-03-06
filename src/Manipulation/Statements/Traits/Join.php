<?php namespace Framework\Database\Manipulation\Statements\Traits;

/**
 * Trait Join.
 *
 * @see https://mariadb.com/kb/en/library/joins/
 */
trait Join
{
	/**
	 * Sets the FROM clause.
	 *
	 * @param mixed $expressions Table references. Each reference must be of type: string or \Closure
	 *
	 * @see https://mariadb.com/kb/en/library/join-syntax/
	 *
	 * @return $this
	 */
	public function from(...$references)
	{
		foreach ($references as $reference) {
			$this->sql['from'][] = $reference;
		}
		return $this;
	}

	protected function renderFrom() : ?string
	{
		if ( ! isset($this->sql['from'])) {
			return null;
		}
		$tables = [];
		foreach ($this->sql['from'] as $table) {
			$tables[] = $this->renderColumn($table);
		}
		return ' FROM ' . \implode(', ', $tables);
	}

	protected function checkFrom(string $clause)
	{
		if ( ! isset($this->sql['from'])) {
			throw new \RuntimeException("Clause {$clause} only works with FROM");
		}
	}

	public function join(string $type, $table, string $condition, $conditional)
	{
		return $this->addJoin($type, $table, $condition, $conditional);
	}

	public function joinUsing($table, ...$columns)
	{
		return $this->addJoin('', $table, 'USING', $columns);
	}

	public function innerJoin($table, Closure $condition)
	{
		return $this->addJoin('INNER', $table, 'ON', $condition);
	}

	public function innerJoinUsing($table, ...$columns)
	{
		return $this->addJoin('INNER', $table, 'USING', $columns);
	}

	public function crossJoin($table, Closure $condition)
	{
		return $this->addJoin('CROSS', $table, 'ON', $condition);
	}

	public function crossJoinUsing($table, ...$columns)
	{
		return $this->addJoin('CROSS', $table, 'USING', $columns);
	}

	public function leftJoin($table, Closure $condition)
	{
		return $this->addJoin('LEFT', $table, 'ON', $condition);
	}

	public function leftJoinUsing($table, ...$columns)
	{
		return $this->addJoin('LEFT', $table, 'USING', $columns);
	}

	public function leftOuterJoin($table, Closure $condition)
	{
		return $this->addJoin('LEFT OUTER', $table, 'ON', $condition);
	}

	public function rightJoin($table, Closure $condition)
	{
		return $this->addJoin('RIGHT', $table, 'ON', $condition);
	}

	public function rightJoinUsing($table, ...$columns)
	{
		return $this->addJoin('RIGHT', $table, 'USING', $columns);
	}

	public function rightOuterJoin($table, Closure $condition)
	{
		return $this->addJoin('RIGHT OUTER', $table, 'ON', $condition);
	}

	public function naturalJoin($table, Closure $condition)
	{
		return $this->addJoin('NATURAL', $table, 'ON', $condition);
	}

	public function naturalLeftJoin($table, Closure $condition)
	{
		return $this->addJoin('NATURAL LEFT', $table, 'ON', $condition);
	}

	public function naturalLeftJoinUsing($table, ...$columns)
	{
		return $this->addJoin('NATURAL LEFT', $table, 'USING', $columns);
	}

	public function naturalLeftOuterJoin($table, Closure $condition)
	{
		return $this->addJoin('NATURAL LEFT OUTER', $table, 'ON', $condition);
	}

	public function naturalRightJoin($table, Closure $condition)
	{
		return $this->addJoin('NATURAL RIGHT', $table, 'ON', $condition);
	}

	public function naturalRightJoinUsing($table, ...$columns)
	{
		return $this->addJoin('NATURAL RIGHT', $table, 'USING', $columns);
	}

	public function naturalRightOuterJoin($table, Closure $condition)
	{
		return $this->addJoin('NATURAL RIGHT OUTER', $table, 'ON', $condition);
	}

	private function addJoin(string $type, $table, string $condition_type, $condition)
	{
		$this->sql['join'] = [
			'type' => $type,
			'table' => $table,
			'condition_type' => $condition_type,
			'condition' => $condition,
		];
		return $this;
	}

	protected function renderJoin() : ?string
	{
		if ( ! isset($this->sql['join'])) {
			return null;
		}
		$type = $this->renderJoinType($this->sql['join']['type']);
		$table = $this->renderColumn($this->sql['join']['table']);
		$condition_type = $this->renderJoinConditionType($this->sql['join']['condition_type']);
		$condition = $this->renderJoinCondition($condition_type, $this->sql['join']['condition']);
		if ($type) {
			$type .= ' ';
		}
		return "{$type}JOIN {$table} {$condition_type} {$condition}";
	}

	private function renderJoinType(string $type) : string
	{
		$result = \strtoupper($type);
		if (\in_array($result, [
			'',
			'INNER',
			'CROSS',
			'LEFT',
			'LEFT OUTER',
			'NATURAL RIGHT',
			'NATURAL RIGHT OUTER',
			'NATURAL',
			'NATURAL LEFT',
			'NATURAL LEFT OUTER',
			'NATURAL NATURAL RIGHT',
			'NATURAL NATURAL RIGHT OUTER',
		], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid JOIN type: {$type}");
	}

	private function renderJoinConditionType(string $type) : string
	{
		$result = \strtoupper($type);
		if (\in_array($result, [
			'ON',
			'USING',
		], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid JOIN condition type: {$type}");
	}

	private function renderJoinCondition(string $type, $condition) : string
	{
		if ($type === 'ON') {
			return $this->subquery($condition);
		}
	}
}
