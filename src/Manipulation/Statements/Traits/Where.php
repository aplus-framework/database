<?php namespace Framework\Database\Manipulation\Statements\Traits;

/**
 * Trait Where.
 */
trait Where
{
	/**
	 * Adds a WHERE AND condition.
	 *
	 * @param \Closure|string $column
	 * @param string          $operator
	 * @param mixed           $values Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function where($column, string $operator, ...$values)
	{
		return $this->addWhere('AND', $column, $operator, $values);
	}

	/**
	 * Adds a WHERE OR condition.
	 *
	 * @param \Closure|string $column
	 * @param string          $operator
	 * @param mixed           $values Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orWhere($column, string $operator, ...$values)
	{
		return $this->addWhere('OR', $column, $operator, $values);
	}

	public function whereEqual($column, $value)
	{
		return $this->where($column, '=', $value);
	}

	public function orWhereEqual($column, $value)
	{
		return $this->orWhere($column, '=', $value);
	}

	public function whereNotEqual($column, $value)
	{
		return $this->where($column, '!=', $value);
	}

	public function orWhereNotEqual($column, $value)
	{
		return $this->orWhere($column, '!=', $value);
	}

	public function whereNullSafeEqual($column, $value)
	{
		return $this->where($column, '<=>', $value);
	}

	public function orWhereNullSafeEqual($column, $value)
	{
		return $this->orWhere($column, '<=>', $value);
	}

	public function whereLessThan($column, $value)
	{
		return $this->where($column, '<', $value);
	}

	public function orWhereLessThan($column, $value)
	{
		return $this->orWhere($column, '<', $value);
	}

	public function whereLessThanOrEqual($column, $value)
	{
		return $this->where($column, '<=', $value);
	}

	public function orWhereLessThanOrEqual($column, $value)
	{
		return $this->orWhere($column, '<=', $value);
	}

	public function whereGreaterThan($column, $value)
	{
		return $this->where($column, '>', $value);
	}

	public function orWhereGreaterThan($column, $value)
	{
		return $this->orWhere($column, '>', $value);
	}

	public function whereGreaterThanOrEqual($column, $value)
	{
		return $this->where($column, '>=', $value);
	}

	public function orWhereGreaterThanOrEqual($column, $value)
	{
		return $this->orWhere($column, '>=', $value);
	}

	public function whereLike($column, $value)
	{
		return $this->where($column, 'LIKE', $value);
	}

	public function orWhereLike($column, $value)
	{
		return $this->orWhere($column, 'LIKE', $value);
	}

	public function whereNotLike($column, $value)
	{
		return $this->where($column, 'NOT LIKE', $value);
	}

	public function orWhereNotLike($column, $value)
	{
		return $this->orWhere($column, 'NOT LIKE', $value);
	}

	public function whereIn($column, ...$values)
	{
		return $this->where($column, 'IN', ...$values);
	}

	public function whereNotIn($column, ...$values)
	{
		return $this->where($column, 'NOT IN', ...$values);
	}

	public function orWhereIn($column, ...$values)
	{
		return $this->orWhere($column, 'IN', ...$values);
	}

	public function orWhereNotIn($column, ...$values)
	{
		return $this->orWhere($column, 'NOT IN', ...$values);
	}

	public function whereBetween($column, ...$values)
	{
		return $this->where($column, 'BETWEEN', ...$values);
	}

	public function whereNotBetween($column, ...$values)
	{
		return $this->where($column, 'NOT BETWEEN', ...$values);
	}

	public function orWhereBetween($column, ...$values)
	{
		return $this->orWhere($column, 'BETWEEN', ...$values);
	}

	public function orWhereNotBetween($column, ...$values)
	{
		return $this->orWhere($column, 'NOT BETWEEN', ...$values);
	}

	public function whereIsNull($column)
	{
		return $this->where($column, 'IS NULL');
	}

	public function orWhereIsNull($column)
	{
		return $this->orWhere($column, 'IS NULL');
	}

	public function whereIsNotNull($column)
	{
		return $this->where($column, 'IS NOT NULL');
	}

	public function orWhereIsNotNull($column)
	{
		return $this->orWhere($column, 'IS NOT NULL');
	}

	private function addWhere(
		string $glue,
		$column,
		string $operator,
		array $values,
		string $clause = 'where'
	) {
		$this->sql[$clause][] = [
			'glue' => $glue,
			'column' => $column,
			'operator' => $operator,
			'values' => $values,
		];
		return $this;
	}

	protected function renderWhere(string $clause = 'where') : ?string
	{
		if ( ! isset($this->sql[$clause])) {
			return null;
		}
		$parts = $this->sql[$clause];
		foreach ($parts as &$part) {
			$part['column'] = $this->renderColumn($part['column']);
			$part['operator'] = $this->renderWhereOperator($part['operator']);
			$part['values'] = $this->renderWhereValues($part['operator'], $part['values']);
		}
		unset($part);
		$condition = "{$parts[0]['column']} {$parts[0]['operator']}";
		$condition .= $parts[0]['values'] === null ? '' : " {$parts[0]['values']}";
		unset($parts[0]);
		foreach ($parts as $part) {
			$condition .= " {$part['glue']} {$part['column']} {$part['operator']}";
			$condition .= $part['values'] === null ? '' : " {$part['values']}";
		}
		$clause = \strtoupper($clause);
		return " {$clause} {$condition}";
	}

	private function renderWhereOperator(string $operator) : string
	{
		$result = \strtoupper($operator);
		if (\in_array($result, [
			'=',
			'<=>',
			'!=',
			'<>',
			'>',
			'>=',
			'<',
			'<=',
			'LIKE',
			'NOT LIKE',
			'IN',
			'NOT IN',
			'BETWEEN',
			'NOT BETWEEN',
			'IS NULL',
			'IS NOT NULL',
		], true)) {
			return $result;
		}
		throw new \InvalidArgumentException("Invalid comparison operator: {$operator}");
	}

	private function renderWhereValues(string $operator, array $values) : ?string
	{
		$values = \array_values($values);
		$values = $this->prepareWhereValues($values);
		if (\in_array($operator, [
			'=',
			'<=>',
			'!=',
			'<>',
			'>',
			'>=',
			'<',
			'<=',
			'LIKE',
			'NOT LIKE',
		], true)) {
			return $this->renderWhereValuesPartComparator($operator, $values);
		}
		if (\in_array($operator, [
			'IN',
			'NOT IN',
		], true)) {
			return $this->renderWhereValuesPartIn($operator, $values);
		}
		if (\in_array($operator, [
			'BETWEEN',
			'NOT BETWEEN',
		], true)) {
			return $this->renderWhereValuesPartBetween($operator, $values);
		}
		if (\in_array($operator, [
			'IS NULL',
			'IS NOT NULL',
		], true)) {
			return $this->renderWhereValuesPartIsNull($operator, $values);
		}
	}

	private function prepareWhereValues(array $values) : array
	{
		foreach ($values as &$value) {
			$value = $value instanceof \Closure
				? $this->subquery($value)
				: $this->manipulation->database->quote($value);
		}
		return $values;
	}

	private function renderWhereValuesPartComparator(string $operator, array $values) : string
	{
		if (isset($values[1])) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive only 1 parameter"
			);
		}
		if ( ! isset($values[0])) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive 1 parameter"
			);
		}
		return $values[0];
	}

	private function renderWhereValuesPartIn(string $operator, array $values) : string
	{
		if (empty($values)) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive at least 1 parameter"
			);
		}
		return '(' . \implode(', ', $values) . ')';
	}

	private function renderWhereValuesPartBetween(string $operator, array $values) : string
	{
		if (isset($values[2]) || ! isset($values[0], $values[1])) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must receive exactly 2 parameters"
			);
		}
		return "{$values[0]} AND {$values[1]}";
	}

	private function renderWhereValuesPartIsNull(string $operator, array $values)
	{
		if ( ! empty($values)) {
			throw new \InvalidArgumentException(
				"Operator {$operator} must not receive parameters"
			);
		}
		return null;
	}
}
