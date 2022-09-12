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

/**
 * Class Delete.
 *
 * @see https://mariadb.com/kb/en/delete/
 *
 * @package database
 */
class Delete extends Statement
{
    use Traits\Join;
    use Traits\OrderBy;
    use Traits\Where;

    /**
     * @var string
     */
    public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';
    /**
     * @var string
     */
    public const OPT_QUICK = 'QUICK';
    /**
     * @var string
     */
    public const OPT_IGNORE = 'IGNORE';

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
                static::OPT_LOW_PRIORITY,
                static::OPT_QUICK,
                static::OPT_IGNORE,
            ], true)) {
                throw new InvalidArgumentException("Invalid option: {$input}");
            }
        }
        unset($option);
        $options = \implode(' ', $options);
        return " {$options}";
    }

    /**
     * Sets the table references.
     *
     * @param array<string,Closure|string>|Closure|string $reference The table
     * name as string, a subquery as Closure or an array for aliased table where
     * the key is the alias name and the value is the table name or a subquery
     * @param array<string,Closure|string>|Closure|string ...$references Extra
     * references. Same values as $reference
     *
     * @return static
     */
    public function table(
        array | Closure | string $reference,
        array | Closure | string ...$references
    ) : static {
        $this->sql['table'] = [];
        foreach ([$reference, ...$references] as $reference) {
            $this->sql['table'][] = $reference;
        }
        return $this;
    }

    /**
     * Renders the table references.
     *
     * @return string|null The table references or null if none was set
     */
    protected function renderTable() : ?string
    {
        if ( ! isset($this->sql['table'])) {
            return null;
        }
        $tables = [];
        foreach ($this->sql['table'] as $table) {
            $tables[] = $this->renderAliasedIdentifier($table);
        }
        return ' ' . \implode(', ', $tables);
    }

    /**
     * Sets the LIMIT clause.
     *
     * @param int $limit
     *
     * @see https://mariadb.com/kb/en/limit/
     *
     * @return static
     */
    public function limit(int $limit) : static
    {
        return $this->setLimit($limit);
    }

    /**
     * Renders de DELETE statement.
     *
     * @return string
     */
    public function sql() : string
    {
        $sql = 'DELETE' . \PHP_EOL;
        $part = $this->renderOptions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderTable();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderFrom();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderJoin();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderWhere();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderOrderBy();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderLimit();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        return $sql;
    }

    /**
     * Runs the DELETE statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
