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
		return $this->addWhere('AND', $column, $operator, $values, 'having');
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
		return $this->addWhere('OR', $column, $operator, $values, 'having');
	}

	protected function renderHaving() : ?string
	{
		return $this->renderWhere('having');
	}
}
