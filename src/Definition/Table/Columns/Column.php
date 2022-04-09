<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns;

use Closure;
use Framework\Database\Database;
use Framework\Database\Definition\Table\DefinitionPart;
use LogicException;

/**
 * Class Column.
 *
 * @package database
 */
abstract class Column extends DefinitionPart
{
    protected Database $database;
    protected string $type;
    /**
     * @var array<scalar|null>
     */
    protected array $length;
    protected bool $null = false;
    protected bool $uniqueKey = false;
    protected bool $primaryKey = false;
    protected bool | Closure | float | int | string | null $default;
    protected Closure $check;
    protected ?string $comment;
    protected bool $first = false;
    protected ?string $after;

    /**
     * Column constructor.
     *
     * @param Database $database
     * @param bool|float|int|string|null ...$length
     */
    public function __construct(Database $database, bool | float | int | string | null ...$length)
    {
        $this->database = $database;
        $this->length = $length;
    }

    protected function renderType() : string
    {
        if (empty($this->type)) {
            throw new LogicException('Column type is empty');
        }
        return ' ' . $this->type;
    }

    protected function renderLength() : ?string
    {
        if ( ! isset($this->length[0])) {
            return null;
        }
        $length = $this->database->quote($this->length[0]);
        return "({$length})";
    }

    /**
     * @param Closure $expression
     *
     * @return static
     */
    public function check(Closure $expression) : static
    {
        $this->check = $expression;
        return $this;
    }

    protected function renderCheck() : ?string
    {
        if ( ! isset($this->check)) {
            return null;
        }
        return ' CHECK (' . ($this->check)($this->database) . ')';
    }

    /**
     * @return static
     */
    public function null() : static
    {
        $this->null = true;
        return $this;
    }

    /**
     * @return static
     */
    public function notNull() : static
    {
        $this->null = false;
        return $this;
    }

    protected function renderNull() : ?string
    {
        return $this->null ? ' NULL' : ' NOT NULL';
    }

    /**
     * @param bool|Closure|float|int|string|null $default
     *
     * @return static
     */
    public function default(bool | Closure | float | int | string | null $default) : static
    {
        $this->default = $default;
        return $this;
    }

    protected function renderDefault() : ?string
    {
        if ( ! isset($this->default)) {
            return null;
        }
        $default = $this->default instanceof Closure
            ? '(' . ($this->default)($this->database) . ')'
            : $this->database->quote($this->default);
        return ' DEFAULT ' . $default;
    }

    /**
     * @param string $comment
     *
     * @return static
     */
    public function comment(string $comment) : static
    {
        $this->comment = $comment;
        return $this;
    }

    protected function renderComment() : ?string
    {
        if ( ! isset($this->comment)) {
            return null;
        }
        return ' COMMENT ' . $this->database->quote($this->comment);
    }

    /**
     * @return static
     */
    public function primaryKey() : static
    {
        $this->primaryKey = true;
        return $this;
    }

    protected function renderPrimaryKey() : ?string
    {
        if ( ! $this->primaryKey) {
            return null;
        }
        return ' PRIMARY KEY';
    }

    /**
     * @return static
     */
    public function uniqueKey() : static
    {
        $this->uniqueKey = true;
        return $this;
    }

    protected function renderUniqueKey() : ?string
    {
        if ( ! $this->uniqueKey) {
            return null;
        }
        return ' UNIQUE KEY';
    }

    /**
     * @return static
     */
    public function first() : static
    {
        $this->first = true;
        return $this;
    }

    protected function renderFirst() : ?string
    {
        if ( ! $this->first) {
            return null;
        }
        return ' FIRST';
    }

    /**
     * @param string $column
     *
     * @return static
     */
    public function after(string $column) : static
    {
        $this->after = $column;
        return $this;
    }

    protected function renderAfter() : ?string
    {
        if ( ! isset($this->after)) {
            return null;
        }
        if ($this->first) {
            throw new LogicException('Clauses FIRST and AFTER can not be used together');
        }
        return ' AFTER ' . $this->database->protectIdentifier($this->after);
    }

    protected function renderTypeAttributes() : ?string
    {
        return null;
    }

    protected function sql() : string
    {
        $sql = $this->renderType();
        $sql .= $this->renderLength();
        $sql .= $this->renderTypeAttributes();
        $sql .= $this->renderNull();
        $sql .= $this->renderDefault();
        $sql .= $this->renderUniqueKey();
        $sql .= $this->renderPrimaryKey();
        $sql .= $this->renderComment();
        $sql .= $this->renderFirst();
        $sql .= $this->renderAfter();
        $sql .= $this->renderCheck();
        return $sql;
    }
}
