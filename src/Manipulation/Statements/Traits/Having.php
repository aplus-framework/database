<?php namespace Framework\Database\Manipulation\Statements\Traits;

/**
 * Trait Having.
 */
trait Having
{
	use Where;

	/**
	 * Adds a HAVING AND condition.
	 *
	 * @param \Closure|string $column
	 * @param string          $operator
	 * @param mixed           $values   Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function having($column, string $operator, ...$values)
	{
		return $this->addHaving('AND', $column, $operator, $values);
	}

	/**
	 * Adds a HAVING OR condition.
	 *
	 * @param \Closure|string $column
	 * @param string          $operator
	 * @param mixed           $values   Each value must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orHaving($column, string $operator, ...$values)
	{
		return $this->addHaving('OR', $column, $operator, $values);
	}

	public function havingEqual($column, $value)
	{
		return $this->having($column, '=', $value);
	}

	public function orHavingEqual($column, $value)
	{
		return $this->orHaving($column, '=', $value);
	}

	public function havingNotEqual($column, $value)
	{
		return $this->having($column, '!=', $value);
	}

	public function orHavingNotEqual($column, $value)
	{
		return $this->orHaving($column, '!=', $value);
	}

	public function havingNullSafeEqual($column, $value)
	{
		return $this->having($column, '<=>', $value);
	}

	public function orHavingNullSafeEqual($column, $value)
	{
		return $this->orHaving($column, '<=>', $value);
	}

	public function havingLessThan($column, $value)
	{
		return $this->having($column, '<', $value);
	}

	public function orHavingLessThan($column, $value)
	{
		return $this->orHaving($column, '<', $value);
	}

	public function havingLessThanOrEqual($column, $value)
	{
		return $this->having($column, '<=', $value);
	}

	public function orHavingLessThanOrEqual($column, $value)
	{
		return $this->orHaving($column, '<=', $value);
	}

	public function havingGreaterThan($column, $value)
	{
		return $this->having($column, '>', $value);
	}

	public function orHavingGreaterThan($column, $value)
	{
		return $this->orHaving($column, '>', $value);
	}

	public function havingGreaterThanOrEqual($column, $value)
	{
		return $this->having($column, '>=', $value);
	}

	public function orHavingGreaterThanOrEqual($column, $value)
	{
		return $this->orHaving($column, '>=', $value);
	}

	public function havingLike($column, $value)
	{
		return $this->having($column, 'LIKE', $value);
	}

	public function orHavingLike($column, $value)
	{
		return $this->orHaving($column, 'LIKE', $value);
	}

	public function havingNotLike($column, $value)
	{
		return $this->having($column, 'NOT LIKE', $value);
	}

	public function orHavingNotLike($column, $value)
	{
		return $this->orHaving($column, 'NOT LIKE', $value);
	}

	public function havingIn($column, ...$values)
	{
		return $this->having($column, 'IN', ...$values);
	}

	public function havingNotIn($column, ...$values)
	{
		return $this->having($column, 'NOT IN', ...$values);
	}

	public function orHavingIn($column, ...$values)
	{
		return $this->orHaving($column, 'IN', ...$values);
	}

	public function orHavingNotIn($column, ...$values)
	{
		return $this->orHaving($column, 'NOT IN', ...$values);
	}

	public function havingBetween($column, ...$values)
	{
		return $this->having($column, 'BETWEEN', ...$values);
	}

	public function havingNotBetween($column, ...$values)
	{
		return $this->having($column, 'NOT BETWEEN', ...$values);
	}

	public function orHavingBetween($column, ...$values)
	{
		return $this->orHaving($column, 'BETWEEN', ...$values);
	}

	public function orHavingNotBetween($column, ...$values)
	{
		return $this->orHaving($column, 'NOT BETWEEN', ...$values);
	}

	public function havingIsNull($column)
	{
		return $this->having($column, 'IS NULL');
	}

	public function orHavingIsNull($column)
	{
		return $this->orHaving($column, 'IS NULL');
	}

	public function havingIsNotNull($column)
	{
		return $this->having($column, 'IS NOT NULL');
	}

	public function orHavingIsNotNull($column)
	{
		return $this->orHaving($column, 'IS NOT NULL');
	}

	private function addHaving(
		string $glue,
		$column,
		string $operator,
		array $values
	) {
		return $this->addWhere($glue, $column, $operator, $values, 'having');
	}

	protected function renderHaving() : ?string
	{
		return $this->renderWhere('having');
	}
}
