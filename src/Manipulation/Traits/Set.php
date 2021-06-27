<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation\Traits;

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

	/**
	 * Renders the SET clause.
	 *
	 * @return string|null The SET clause null if it was not set
	 */
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

	/**
	 * Tells if the SET clause was set.
	 *
	 * @return bool True if was set, otherwise false
	 */
	protected function hasSet() : bool
	{
		return isset($this->sql['set']);
	}
}
