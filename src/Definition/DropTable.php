<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition;

use Framework\Database\Statement;
use InvalidArgumentException;
use LogicException;

/**
 * Class DropTable.
 *
 * @see https://mariadb.com/kb/en/drop-table/
 *
 * @package database
 */
class DropTable extends Statement
{
    /**
     * @return static
     */
    public function temporary() : static
    {
        $this->sql['temporary'] = true;
        return $this;
    }

    protected function renderTemporary() : ?string
    {
        if ( ! isset($this->sql['temporary'])) {
            return null;
        }
        return ' TEMPORARY';
    }

    /**
     * @return static
     */
    public function ifExists() : static
    {
        $this->sql['if_exists'] = true;
        return $this;
    }

    protected function renderIfExists() : ?string
    {
        if ( ! isset($this->sql['if_exists'])) {
            return null;
        }
        return ' IF EXISTS';
    }

    /**
     * @param string $comment
     *
     * @return static
     */
    public function commentToSave(string $comment) : static
    {
        $this->sql['comment'] = $comment;
        return $this;
    }

    protected function renderCommentToSave() : ?string
    {
        if ( ! isset($this->sql['comment'])) {
            return null;
        }
        $comment = \strtr($this->sql['comment'], ['*/' => '* /']);
        return " /* {$comment} */";
    }

    /**
     * @param string $table
     * @param string ...$tables
     *
     * @return static
     */
    public function table(string $table, string ...$tables) : static
    {
        $this->sql['tables'] = $tables ? \array_merge([$table], $tables) : [$table];
        return $this;
    }

    protected function renderTables() : string
    {
        if ( ! isset($this->sql['tables'])) {
            throw new LogicException('Table names can not be empty');
        }
        $tables = $this->sql['tables'];
        foreach ($tables as &$table) {
            $table = $this->database->protectIdentifier($table);
        }
        unset($table);
        $tables = \implode(', ', $tables);
        return " {$tables}";
    }

    /**
     * @param int $seconds
     *
     * @return static
     */
    public function wait(int $seconds) : static
    {
        $this->sql['wait'] = $seconds;
        return $this;
    }

    public function renderWait() : ?string
    {
        if ( ! isset($this->sql['wait'])) {
            return null;
        }
        if ($this->sql['wait'] < 0) {
            throw new InvalidArgumentException(
                "Invalid WAIT value: {$this->sql['wait']}"
            );
        }
        return " WAIT {$this->sql['wait']}";
    }

    public function sql() : string
    {
        $sql = 'DROP' . $this->renderTemporary();
        $sql .= ' TABLE' . $this->renderIfExists();
        $sql .= $this->renderCommentToSave();
        $sql .= $this->renderTables() . \PHP_EOL;
        $part = $this->renderWait();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        return $sql;
    }

    /**
     * Runs the DROP TABLE statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
