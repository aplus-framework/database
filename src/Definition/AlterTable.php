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

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Definition\Table\TableStatement;
use InvalidArgumentException;
use LogicException;

/**
 * Class AlterTable.
 *
 * @see https://mariadb.com/kb/en/alter-table/
 *
 * @package database
 */
class AlterTable extends TableStatement
{
    /**
     * @var string
     */
    public const ALGO_COPY = 'COPY';
    /**
     * @var string
     */
    public const ALGO_DEFAULT = 'DEFAULT';
    /**
     * @var string
     */
    public const ALGO_INPLACE = 'INPLACE';
    /**
     * @var string
     */
    public const ALGO_INSTANT = 'INSTANT';
    /**
     * @var string
     */
    public const ALGO_NOCOPY = 'NOCOPY';
    /**
     * @var string
     */
    public const LOCK_DEFAULT = 'DEFAULT';
    /**
     * @var string
     */
    public const LOCK_EXCLUSIVE = 'EXCLUSIVE';
    /**
     * @var string
     */
    public const LOCK_NONE = 'NONE';
    /**
     * @var string
     */
    public const LOCK_SHARED = 'SHARED';

    /**
     * @return static
     */
    public function online() : static
    {
        $this->sql['online'] = true;
        return $this;
    }

    protected function renderOnline() : ?string
    {
        if ( ! isset($this->sql['online'])) {
            return null;
        }
        return ' ONLINE';
    }

    /**
     * @return static
     */
    public function ignore() : static
    {
        $this->sql['ignore'] = true;
        return $this;
    }

    protected function renderIgnore() : ?string
    {
        if ( ! isset($this->sql['ignore'])) {
            return null;
        }
        return ' IGNORE';
    }

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
     * @param string $tableName
     *
     * @return static
     */
    public function table(string $tableName) : static
    {
        $this->sql['table'] = $tableName;
        return $this;
    }

    protected function renderTable() : string
    {
        if (isset($this->sql['table'])) {
            return ' ' . $this->database->protectIdentifier($this->sql['table']);
        }
        throw new LogicException('TABLE name must be set');
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

    protected function renderWait() : ?string
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

    public function noWait() : static
    {
        $this->sql['no_wait'] = true;
        return $this;
    }

    protected function renderNoWait() : ?string
    {
        if ( ! isset($this->sql['no_wait'])) {
            return null;
        }
        if (isset($this->sql['wait'])) {
            throw new LogicException('WAIT and NOWAIT can not be used together');
        }
        return ' NOWAIT';
    }

    /**
     * @param callable $definition
     * @param bool $ifNotExists
     *
     * @return static
     */
    public function add(callable $definition, bool $ifNotExists = false) : static
    {
        $this->sql['add'][] = [
            'definition' => $definition,
            'if_not_exists' => $ifNotExists,
        ];
        return $this;
    }

    /**
     * @param callable $definition
     *
     * @return static
     */
    public function addIfNotExists(callable $definition) : static
    {
        $this->sql['add'][] = [
            'definition' => $definition,
            'if_not_exists' => true,
        ];
        return $this;
    }

    protected function renderAdd() : ?string
    {
        if ( ! isset($this->sql['add'])) {
            return null;
        }
        $parts = [];
        foreach ($this->sql['add'] as $add) {
            $definition = new TableDefinition(
                $this->database,
                $add['if_not_exists'] ? 'IF NOT EXISTS' : null
            );
            $add['definition']($definition);
            $part = $definition->sql('ADD');
            if ($part) {
                $parts[] = $part;
            }
        }
        return $parts ? \implode(',' . \PHP_EOL, $parts) : null;
    }

    /**
     * @param callable $definition
     * @param bool $ifExists
     *
     * @return static
     */
    public function change(callable $definition, bool $ifExists = false) : static
    {
        $this->sql['change'][] = [
            'definition' => $definition,
            'if_exists' => $ifExists,
        ];
        return $this;
    }

    public function changeIfExists(callable $definition) : static
    {
        $this->sql['change'][] = [
            'definition' => $definition,
            'if_exists' => true,
        ];
        return $this;
    }

    protected function renderChange() : ?string
    {
        if ( ! isset($this->sql['change'])) {
            return null;
        }
        $parts = [];
        foreach ($this->sql['change'] as $change) {
            $definition = new TableDefinition(
                $this->database,
                $change['if_exists'] ? 'IF EXISTS' : null
            );
            $change['definition']($definition);
            $part = $definition->sql('CHANGE');
            if ($part) {
                $parts[] = $part;
            }
        }
        return $parts ? \implode(',' . \PHP_EOL, $parts) : null;
    }

    /**
     * @param callable $definition
     * @param bool $ifExists
     *
     * @return static
     */
    public function modify(callable $definition, bool $ifExists = false) : static
    {
        $this->sql['modify'][] = [
            'definition' => $definition,
            'if_exists' => $ifExists,
        ];
        return $this;
    }

    public function modifyIfExists(callable $definition) : static
    {
        $this->sql['modify'][] = [
            'definition' => $definition,
            'if_exists' => true,
        ];
        return $this;
    }

    protected function renderModify() : ?string
    {
        if ( ! isset($this->sql['modify'])) {
            return null;
        }
        $parts = [];
        foreach ($this->sql['modify'] as $modify) {
            $definition = new TableDefinition(
                $this->database,
                $modify['if_exists'] ? 'IF EXISTS' : null
            );
            $modify['definition']($definition);
            $part = $definition->sql('MODIFY');
            if ($part) {
                $parts[] = $part;
            }
        }
        return $parts ? \implode(',' . \PHP_EOL, $parts) : null;
    }

    public function dropColumn(string $name, bool $ifExists = false) : static
    {
        $this->sql['drop_columns'][$name] = $ifExists;
        return $this;
    }

    public function dropColumnIfExists(string $name) : static
    {
        $this->sql['drop_columns'][$name] = true;
        return $this;
    }

    protected function renderDropColumns() : ?string
    {
        if ( ! isset($this->sql['drop_columns'])) {
            return null;
        }
        $drops = [];
        foreach ($this->sql['drop_columns'] as $name => $ifExists) {
            $name = $this->database->protectIdentifier($name);
            $ifExists = $ifExists ? 'IF EXISTS ' : '';
            $drops[] = ' DROP COLUMN ' . $ifExists . $name;
        }
        return \implode(',' . \PHP_EOL, $drops);
    }

    public function dropPrimaryKey() : static
    {
        $this->sql['drop_primary_key'] = true;
        return $this;
    }

    protected function renderDropPrimaryKey() : ?string
    {
        if ( ! isset($this->sql['drop_primary_key'])) {
            return null;
        }
        return ' DROP PRIMARY KEY';
    }

    public function dropKey(string $name, bool $ifExists = false) : static
    {
        $this->sql['drop_keys'][$name] = $ifExists;
        return $this;
    }

    public function dropKeyIfExists(string $name) : static
    {
        $this->sql['drop_keys'][$name] = true;
        return $this;
    }

    protected function renderDropKeys() : ?string
    {
        if ( ! isset($this->sql['drop_keys'])) {
            return null;
        }
        $drops = [];
        foreach ($this->sql['drop_keys'] as $name => $ifExists) {
            $name = $this->database->protectIdentifier($name);
            $ifExists = $ifExists ? 'IF EXISTS ' : '';
            $drops[] = ' DROP KEY ' . $ifExists . $name;
        }
        return \implode(',' . \PHP_EOL, $drops);
    }

    public function dropForeignKey(string $name, bool $ifExists = false) : static
    {
        $this->sql['drop_foreign_keys'][$name] = $ifExists;
        return $this;
    }

    public function dropForeignKeyIfExists(string $name) : static
    {
        $this->sql['drop_foreign_keys'][$name] = true;
        return $this;
    }

    protected function renderDropForeignKeys() : ?string
    {
        if ( ! isset($this->sql['drop_foreign_keys'])) {
            return null;
        }
        $drops = [];
        foreach ($this->sql['drop_foreign_keys'] as $name => $ifExists) {
            $name = $this->database->protectIdentifier($name);
            $ifExists = $ifExists ? 'IF EXISTS ' : '';
            $drops[] = ' DROP FOREIGN KEY ' . $ifExists . $name;
        }
        return \implode(',' . \PHP_EOL, $drops);
    }

    public function dropConstraint(string $name, bool $ifExists = false) : static
    {
        $this->sql['drop_constraints'][$name] = $ifExists;
        return $this;
    }

    public function dropConstraintIfExists(string $name) : static
    {
        $this->sql['drop_constraints'][$name] = true;
        return $this;
    }

    protected function renderDropConstraints() : ?string
    {
        if ( ! isset($this->sql['drop_constraints'])) {
            return null;
        }
        $drops = [];
        foreach ($this->sql['drop_constraints'] as $name => $ifExists) {
            $name = $this->database->protectIdentifier($name);
            $ifExists = $ifExists ? 'IF EXISTS ' : '';
            $drops[] = ' DROP CONSTRAINT ' . $ifExists . $name;
        }
        return \implode(',' . \PHP_EOL, $drops);
    }

    public function disableKeys() : static
    {
        $this->sql['disable_keys'] = true;
        return $this;
    }

    protected function renderDisableKeys() : ?string
    {
        if ( ! isset($this->sql['disable_keys'])) {
            return null;
        }
        return ' DISABLE KEYS';
    }

    public function enableKeys() : static
    {
        $this->sql['enable_keys'] = true;
        return $this;
    }

    protected function renderEnableKeys() : ?string
    {
        if ( ! isset($this->sql['enable_keys'])) {
            return null;
        }
        return ' ENABLE KEYS';
    }

    public function renameTo(string $newTableName) : static
    {
        $this->sql['rename_to'] = $newTableName;
        return $this;
    }

    protected function renderRenameTo() : ?string
    {
        if ( ! isset($this->sql['rename_to'])) {
            return null;
        }
        return ' RENAME TO ' . $this->database->protectIdentifier($this->sql['rename_to']);
    }

    public function orderBy(string $column, string ...$columns) : static
    {
        foreach ([$column, ...$columns] as $col) {
            $this->sql['order_by'][] = $col;
        }
        return $this;
    }

    protected function renderOrderBy() : ?string
    {
        if ( ! isset($this->sql['order_by'])) {
            return null;
        }
        $columns = [];
        foreach ($this->sql['order_by'] as $column) {
            $columns[] = $this->database->protectIdentifier($column);
        }
        return ' ORDER BY ' . \implode(', ', $columns);
    }

    public function renameColumn(string $name, string $newName) : static
    {
        $this->sql['rename_columns'][$name] = $newName;
        return $this;
    }

    protected function renderRenameColumns() : ?string
    {
        if ( ! isset($this->sql['rename_columns'])) {
            return null;
        }
        $renames = [];
        foreach ($this->sql['rename_columns'] as $name => $newName) {
            $name = $this->database->protectIdentifier($name);
            $newName = $this->database->protectIdentifier($newName);
            $renames[] = ' RENAME COLUMN ' . $name . ' TO ' . $newName;
        }
        return \implode(',' . \PHP_EOL, $renames);
    }

    public function renameKey(string $name, string $newName) : static
    {
        $this->sql['rename_keys'][$name] = $newName;
        return $this;
    }

    protected function renderRenameKeys() : ?string
    {
        if ( ! isset($this->sql['rename_keys'])) {
            return null;
        }
        $renames = [];
        foreach ($this->sql['rename_keys'] as $name => $newName) {
            $name = $this->database->protectIdentifier($name);
            $newName = $this->database->protectIdentifier($newName);
            $renames[] = ' RENAME KEY ' . $name . ' TO ' . $newName;
        }
        return \implode(',' . \PHP_EOL, $renames);
    }

    public function convertToCharset(string $charset, string $collation = null) : static
    {
        $this->sql['convert_to_charset'] = [
            'charset' => $charset,
            'collation' => $collation,
        ];
        return $this;
    }

    protected function renderConvertToCharset() : ?string
    {
        if ( ! isset($this->sql['convert_to_charset'])) {
            return null;
        }
        $charset = $this->database->quote($this->sql['convert_to_charset']['charset']);
        $convert = ' CONVERT TO CHARACTER SET ' . $charset;
        if (isset($this->sql['convert_to_charset']['collation'])) {
            $convert .= ' COLLATE ' . $this->database->quote($this->sql['convert_to_charset']['collation']);
        }
        return $convert;
    }

    public function charset(?string $charset) : static
    {
        $this->sql['charset'] = $charset ?? 'DEFAULT';
        return $this;
    }

    protected function renderCharset() : ?string
    {
        if ( ! isset($this->sql['charset'])) {
            return null;
        }
        $charset = \strtolower($this->sql['charset']);
        if ($charset === 'default') {
            return ' DEFAULT CHARACTER SET';
        }
        return ' CHARACTER SET = ' . $this->database->quote($charset);
    }

    public function collate(?string $collation) : static
    {
        $this->sql['collate'] = $collation ?? 'DEFAULT';
        return $this;
    }

    protected function renderCollate() : ?string
    {
        if ( ! isset($this->sql['collate'])) {
            return null;
        }
        $collate = \strtolower($this->sql['collate']);
        if ($collate === 'default') {
            return ' DEFAULT COLLATE';
        }
        return ' COLLATE = ' . $this->database->quote($collate);
    }

    /**
     * @param string $type
     *
     * @see https://mariadb.com/kb/en/alter-table/#lock
     * @see AlterTable::LOCK_DEFAULT
     * @see AlterTable::LOCK_EXCLUSIVE
     * @see AlterTable::LOCK_NONE
     * @see AlterTable::LOCK_SHARED
     *
     * @return static
     */
    public function lock(string $type) : static
    {
        $this->sql['lock'] = $type;
        return $this;
    }

    protected function renderLock() : ?string
    {
        if ( ! isset($this->sql['lock'])) {
            return null;
        }
        $lock = \strtoupper($this->sql['lock']);
        if ( ! \in_array($lock, [
            static::LOCK_DEFAULT,
            static::LOCK_EXCLUSIVE,
            static::LOCK_NONE,
            static::LOCK_SHARED,
        ], true)) {
            throw new InvalidArgumentException("Invalid LOCK value: {$this->sql['lock']}");
        }
        return ' LOCK = ' . $lock;
    }

    public function force() : static
    {
        $this->sql['force'] = true;
        return $this;
    }

    protected function renderForce() : ?string
    {
        if ( ! isset($this->sql['force'])) {
            return null;
        }
        return ' FORCE';
    }

    /**
     * @param string $algo
     *
     * @see https://mariadb.com/kb/en/innodb-online-ddl-overview/#algorithm
     * @see AlterTable::ALGO_COPY
     * @see AlterTable::ALGO_DEFAULT
     * @see AlterTable::ALGO_INPLACE
     * @see AlterTable::ALGO_INSTANT
     * @see AlterTable::ALGO_NOCOPY
     *
     * @return static
     */
    public function algorithm(string $algo) : static
    {
        $this->sql['algorithm'] = $algo;
        return $this;
    }

    protected function renderAlgorithm() : ?string
    {
        if ( ! isset($this->sql['algorithm'])) {
            return null;
        }
        $algo = \strtoupper($this->sql['algorithm']);
        if ( ! \in_array($algo, [
            static::ALGO_COPY,
            static::ALGO_DEFAULT,
            static::ALGO_INPLACE,
            static::ALGO_INSTANT,
            static::ALGO_NOCOPY,
        ], true)) {
            throw new InvalidArgumentException("Invalid ALGORITHM value: {$this->sql['algorithm']}");
        }
        return ' ALGORITHM = ' . $algo;
    }

    public function sql() : string
    {
        $sql = 'ALTER' . $this->renderOnline() . $this->renderIgnore();
        $sql .= ' TABLE' . $this->renderIfExists();
        $sql .= $this->renderTable() . \PHP_EOL;
        $part = $this->renderWait() . $this->renderNoWait();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $sql .= $this->joinParts([
            $this->renderOptions(),
            $this->renderAdd(),
            $this->renderChange(),
            $this->renderModify(),
            $this->renderDropColumns(),
            $this->renderDropPrimaryKey(),
            $this->renderDropKeys(),
            $this->renderDropForeignKeys(),
            $this->renderDropConstraints(),
            $this->renderDisableKeys(),
            $this->renderEnableKeys(),
            $this->renderRenameTo(),
            $this->renderOrderBy(),
            $this->renderRenameColumns(),
            $this->renderRenameKeys(),
            $this->renderConvertToCharset(),
            $this->renderCharset(),
            $this->renderCollate(),
            $this->renderAlgorithm(),
            $this->renderLock(),
            $this->renderForce(),
        ]);
        return $sql;
    }

    /**
     * @param array<string|null> $parts
     *
     * @return string
     */
    protected function joinParts(array $parts) : string
    {
        $result = '';
        $hasBefore = false;
        foreach ($parts as $part) {
            if ($part !== null) {
                $result .= $hasBefore ? ',' . \PHP_EOL : '';
                $result .= $part;
                $hasBefore = true;
            }
        }
        return $result;
    }

    /**
     * Runs the ALTER TABLE statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int | string
    {
        return $this->database->exec($this->sql());
    }
}
