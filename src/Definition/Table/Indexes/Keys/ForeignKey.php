<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Indexes\Keys;

use InvalidArgumentException;
use LogicException;

/**
 * Class ForeignKey.
 *
 * @see https://mariadb.com/kb/en/foreign-keys/
 *
 * @package database
 */
final class ForeignKey extends ConstraintKey
{
    /**
     * The change is allowed and propagates on the child table.
     * For example, if a parent row is deleted, the child row is also deleted;
     * if a parent row's ID changes, the child row's ID will also change.
     *
     * @var string
     */
    public const OPT_CASCADE = 'CASCADE';
    /**
     * Synonym for RESTRICT.
     *
     * @see ForeignKey::OPT_RESTRICT
     *
     * @var string
     */
    public const OPT_NO_ACTION = 'NO ACTION';
    /**
     * The change on the parent table is prevented.
     * The statement terminates with a 1451 error (SQLSTATE '2300').
     * This is the default behavior for both ON DELETE and ON UPDATE.
     *
     * @var string
     */
    public const OPT_RESTRICT = 'RESTRICT';
    /**
     * The change is allowed, and the child row's foreign key columns are set
     * to NULL.
     *
     * @var string
     */
    public const OPT_SET_NULL = 'SET NULL';
    protected string $type = 'FOREIGN KEY';
    protected ?string $referenceTable = null;
    /**
     * @var array<string>
     */
    protected array $referenceColumns = [];
    protected ?string $onDelete = null;
    protected ?string $onUpdate = null;

    /**
     * @param string $table
     * @param string $column
     * @param string ...$columns
     *
     * @return static
     */
    public function references(string $table, string $column, string ...$columns) : static
    {
        $this->referenceTable = $table;
        $this->referenceColumns = $columns ? \array_merge([$column], $columns) : [$column];
        return $this;
    }

    protected function renderReferences() : string
    {
        if ($this->referenceTable === null) {
            throw new LogicException('REFERENCES clause was not set');
        }
        $table = $this->database->protectIdentifier($this->referenceTable);
        $columns = [];
        foreach ($this->referenceColumns as $column) {
            $columns[] = $this->database->protectIdentifier($column);
        }
        $columns = \implode(', ', $columns);
        return " REFERENCES {$table} ({$columns})";
    }

    /**
     * @param string $option
     *
     * @return static
     */
    public function onDelete(string $option) : static
    {
        $this->onDelete = $option;
        return $this;
    }

    protected function renderOnDelete() : ?string
    {
        if ($this->onDelete === null) {
            return null;
        }
        $reference = $this->makeReferenceOption($this->onDelete);
        return " ON DELETE {$reference}";
    }

    /**
     * @param string $option
     *
     * @return static
     */
    public function onUpdate(string $option) : static
    {
        $this->onUpdate = $option;
        return $this;
    }

    protected function renderOnUpdate() : ?string
    {
        if ($this->onUpdate === null) {
            return null;
        }
        $reference = $this->makeReferenceOption($this->onUpdate);
        return " ON UPDATE {$reference}";
    }

    private function makeReferenceOption(string $option) : string
    {
        $result = \strtoupper($option);
        if (\in_array($result, ['RESTRICT', 'CASCADE', 'SET NULL', 'NO ACTION'], true)) {
            return $result;
        }
        throw new InvalidArgumentException("Invalid reference option: {$option}");
    }

    protected function renderTypeAttributes() : ?string
    {
        return $this->renderReferences() . $this->renderOnDelete() . $this->renderOnUpdate();
    }
}
