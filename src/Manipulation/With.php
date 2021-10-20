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
use Framework\Database\Result;
use InvalidArgumentException;
use LogicException;

/**
 * Class With.
 *
 * @see https://mariadb.com/kb/en/with/
 *
 * @package database
 */
class With extends Statement
{
    /**
     * @see https://mariadb.com/kb/en/recursive-common-table-expressions-overview/
     *
     * @var string
     */
    public const OPT_RECURSIVE = 'RECURSIVE';

    protected function renderOptions() : ?string
    {
        if ( ! $this->hasOptions()) {
            return null;
        }
        $options = $this->sql['options'];
        foreach ($options as &$option) {
            $input = $option;
            $option = \strtoupper($option);
            if ($option !== static::OPT_RECURSIVE) {
                throw new InvalidArgumentException("Invalid option: {$input}");
            }
        }
        return \implode(' ', $options);
    }

    /**
     * Adds a table reference.
     *
     * @param Closure|string $table
     * @param Closure $alias
     *
     * @see https://mariadb.com/kb/en/non-recursive-common-table-expressions-overview/
     * @see https://mariadb.com/kb/en/recursive-common-table-expressions-overview/
     *
     * @return static
     */
    public function reference(Closure | string $table, Closure $alias) : static
    {
        $this->sql['references'][] = [
            'table' => $table,
            'alias' => $alias,
        ];
        return $this;
    }

    protected function renderReference() : string
    {
        if ( ! isset($this->sql['references'])) {
            throw new LogicException('References must be set');
        }
        $references = [];
        foreach ($this->sql['references'] as $reference) {
            $references[] = $this->renderIdentifier($reference['table'])
                . ' AS ' . $this->renderAsSelect($reference['alias']);
        }
        return \implode(', ', $references);
    }

    private function renderAsSelect(Closure $subquery) : string
    {
        return '(' . $subquery(new Select($this->database)) . ')';
    }

    /**
     * Sets the SELECT statement part.
     *
     * @param Closure $select
     *
     * @return static
     */
    public function select(Closure $select) : static
    {
        $this->sql['select'] = $select(new Select($this->database));
        return $this;
    }

    protected function renderSelect() : string
    {
        if ( ! isset($this->sql['select'])) {
            throw new LogicException('SELECT must be set');
        }
        return $this->sql['select'];
    }

    /**
     * Renders the WITH statement.
     *
     * @return string
     */
    public function sql() : string
    {
        $sql = 'WITH' . \PHP_EOL;
        $part = $this->renderOptions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->renderReference() . \PHP_EOL;
        $sql .= $this->renderSelect();
        return $sql;
    }

    /**
     * Runs the WITH statement.
     *
     * @return Result
     */
    public function run() : Result
    {
        return $this->database->query($this->sql());
    }
}
