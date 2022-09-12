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
 * Class Update.
 *
 * @see https://mariadb.com/kb/en/update/
 *
 * @package database
 */
class Update extends Statement
{
    use Traits\Join;
    use Traits\Set;
    use Traits\Where;
    use Traits\OrderBy;

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
                static::OPT_IGNORE,
                static::OPT_LOW_PRIORITY,
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
     * @param array<string,Closure|string>|Closure|string $reference
     * @param array<string,Closure|string>|Closure|string ...$references
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

    protected function renderTable() : string
    {
        if ( ! isset($this->sql['table'])) {
            throw new LogicException('Table references must be set');
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

    protected function renderSetPart() : string
    {
        if ( ! $this->hasSet()) {
            throw new LogicException('SET statement must be set');
        }
        return $this->renderSet();
    }

    /**
     * Renders the UPDATE statement.
     *
     * @return string
     */
    public function sql() : string
    {
        $sql = 'UPDATE' . \PHP_EOL;
        $part = $this->renderOptions();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->renderTable() . \PHP_EOL;
        $part = $this->renderJoin();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->renderSetPart() . \PHP_EOL;
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
     * Runs the UPDATE statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
