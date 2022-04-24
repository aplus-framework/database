<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation\Traits;

use Closure;
use LogicException;

/**
 * Trait Set.
 *
 * @package database
 */
trait Set
{
    /**
     * Sets the SET clause.
     *
     * @param array<string,Closure|float|int|string|null>|object $columns Array
     * of columns => values or an object to be cast to array
     *
     * @return static
     */
    public function set(array | object $columns) : static
    {
        $this->sql['set'] = (array) $columns;
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
     * Renders the SET clause checking conflicts.
     *
     * @throws LogicException if SET was set with columns or with the VALUES clause
     *
     * @return string|null The SET part or null if it was not set
     */
    protected function renderSetCheckingConflicts() : ?string
    {
        $part = $this->renderSet();
        if ($part === null) {
            return null;
        }
        if (isset($this->sql['columns'])) {
            throw new LogicException('SET clause is not allowed when columns are set');
        }
        if (isset($this->sql['values'])) {
            throw new LogicException('SET clause is not allowed when VALUES is set');
        }
        return $part;
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
