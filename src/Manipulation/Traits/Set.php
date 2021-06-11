<?php namespace Framework\Database\Manipulation\Traits;

use Closure;

/**
 * Trait Set.
 */
trait Set
{
	/**
	 * Sets the SET clause.
	 *
	 * @param array<string,Closure|float|int|string|null> $columns Array of columns => values
	 *
	 * @return $this
	 */
	public function set(array $columns)
	{
		$this->sql['set'] = $columns;
		return $this;
	}

	protected function renderSet() : ?string
	{
		if ( ! $this->hasSet()) {
			return null;
		}
		$set = [];
		foreach ($this->sql['set'] as $column => $value) {
			$set[] = $this->renderAssignment($column, $value);
		}
		$set = \implode(', ', $set);
		return " SET {$set}";
	}

	protected function hasSet() : bool
	{
		return isset($this->sql['set']);
	}
}
