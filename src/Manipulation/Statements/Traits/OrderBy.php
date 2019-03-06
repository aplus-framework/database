<?php namespace Framework\Database\Manipulation\Statements\Traits;

/**
 * Trait OrderBy.
 *
 * @see https://mariadb.com/kb/en/library/order-by/
 */
trait OrderBy
{
	/**
	 * @param mixed $columns Each column must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orderBy(...$columns)
	{
		return $this->addOrderBy($columns, null);
	}

	/**
	 * @param mixed $columns Each column must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orderByAsc(...$columns)
	{
		return $this->addOrderBy($columns, 'ASC');
	}

	/**
	 * @param mixed $columns Each column must be of type: string or \Closure
	 *
	 * @return $this
	 */
	public function orderByDesc(...$columns)
	{
		return $this->addOrderBy($columns, 'DESC');
	}

	private function addOrderBy(array $columns, ?string $direction)
	{
		foreach ($columns as $column) {
			$this->sql['order_by'][] = [
				'column' => $column,
				'direction' => $direction,
			];
		}
		return $this;
	}

	protected function renderOrderBy() : ?string
	{
		if ( ! isset($this->sql['order_by'])) {
			return null;
		}
		$expressions = [];
		foreach ($this->sql['order_by'] as $part) {
			$expression = $this->renderColumn($part['column']);
			if ($part['direction']) {
				$expression .= " {$part['direction']}";
			}
			$expressions[] = $expression;
		}
		$expressions = \implode(', ', $expressions);
		return " ORDER BY {$expressions}";
	}
}
