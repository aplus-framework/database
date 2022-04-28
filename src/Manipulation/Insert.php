<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation;

use Closure;
use InvalidArgumentException;
use LogicException;

/**
 * Class Insert.
 *
 * @see https://mariadb.com/kb/en/insert/
 *
 * @package database
 */
class Insert extends Statement
{
    use Traits\Select;
    use Traits\Set;
    use Traits\Values;

    /**
     * @see https://mariadb.com/kb/en/insert-delayed/
     *
     * @var string
     */
    public const OPT_DELAYED = 'DELAYED';
    /**
     * Convert errors to warnings, which will not stop inserts of additional rows.
     *
     * @see https://mariadb.com/kb/en/insert-ignore/
     *
     * @var string
     */
    public const OPT_IGNORE = 'IGNORE';
    /**
     * @see https://mariadb.com/kb/en/high_priority-and-low_priority/
     *
     * @var string
     */
    public const OPT_HIGH_PRIORITY = 'HIGH_PRIORITY';
    /**
     * @see https://mariadb.com/kb/en/high_priority-and-low_priority/
     *
     * @var string
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
                throw new InvalidArgumentException("Invalid option: {$input}");
            }
        }
        unset($option);
        $intersection = \array_intersect(
            $options,
            [static::OPT_DELAYED, static::OPT_HIGH_PRIORITY, static::OPT_LOW_PRIORITY]
        );
        if (\count($intersection) > 1) {
            throw new LogicException(
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
     * @return static
     */
    public function into(string $table) : static
    {
        $this->sql['into'] = $table;
        return $this;
    }

    /**
     * Renders the "INTO $table" clause.
     *
     * @throws LogicException if INTO was not set
     *
     * @return string
     */
    protected function renderInto() : string
    {
        if ( ! isset($this->sql['into'])) {
            throw new LogicException('INTO table must be set');
        }
        return ' INTO ' . $this->renderIdentifier($this->sql['into']);
    }

    /**
     * Sets the INTO columns.
     *
     * @param string $column Column name
     * @param string ...$columns Extra column names
     *
     * @return static
     */
    public function columns(string $column, string ...$columns) : static
    {
        $this->sql['columns'] = [$column, ...$columns];
        return $this;
    }

    /**
     * Renders the INTO $table "(...$columns)" part.
     *
     * @return string|null The imploded columns or null if none was set
     */
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
     * Sets the ON DUPLICATE KEY UPDATE part.
     *
     * @param array<string,Closure|float|int|string|null>|object $columns Column name
     * as key/property, column value/expression as value
     *
     * @see https://mariadb.com/kb/en/insert-on-duplicate-key-update/
     *
     * @return static
     */
    public function onDuplicateKeyUpdate(array | object $columns) : static
    {
        $this->sql['on_duplicate'] = (array) $columns;
        return $this;
    }

    /**
     * Renders the ON DUPLICATE KEY UPDATE part.
     *
     * @return string|null The part or null if it was not set
     */
    protected function renderOnDuplicateKeyUpdate() : ?string
    {
        if ( ! isset($this->sql['on_duplicate'])) {
            return null;
        }
        $onDuplicate = [];
        foreach ($this->sql['on_duplicate'] as $column => $value) {
            $onDuplicate[] = $this->renderAssignment($column, $value);
        }
        $onDuplicate = \implode(', ', $onDuplicate);
        return " ON DUPLICATE KEY UPDATE {$onDuplicate}";
    }

    /**
     * Check for conflicts in the INSERT statement.
     *
     * @throws LogicException if it has conflicts
     */
    protected function checkRowStatementsConflict() : void
    {
        if ( ! isset($this->sql['values'])
            && ! isset($this->sql['select'])
            && ! $this->hasSet()
        ) {
            throw new LogicException(
                'The INSERT INTO must be followed by VALUES, SET or SELECT statement'
            );
        }
    }

    /**
     * Renders the INSERT statement.
     *
     * @return string
     */
    public function sql() : string
    {
        $sql = 'INSERT' . \PHP_EOL;
        $part = $this->renderOptions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->renderInto() . \PHP_EOL;
        $part = $this->renderColumns();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $this->checkRowStatementsConflict();
        $part = $this->renderValues();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderSetCheckingConflicts();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderSelect();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderOnDuplicateKeyUpdate();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        return $sql;
    }

    /**
     * Runs the INSERT statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
