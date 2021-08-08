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

use Framework\Database\Definition\Table\Indexes\Index;
use InvalidArgumentException;
use LogicException;

/**
 * Class ForeignKey.
 *
 * @see https://mariadb.com/kb/en/library/foreign-keys/
 *
 * @package database
 */
final class ForeignKey extends Index
{
    use Traits\Constraint;

    protected string $type = 'FOREIGN KEY';
    protected ?string $referenceTable = null;
    /**
     * @var array<int,string>
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
