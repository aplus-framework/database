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
use LogicException;

/**
 * Class AlterSchema.
 *
 * @see https://mariadb.com/kb/en/alter-database/
 *
 * @package database
 */
class AlterSchema extends Statement
{
    /**
     * @param string $schemaName
     *
     * @return static
     */
    public function schema(string $schemaName) : static
    {
        $this->sql['schema'] = $schemaName;
        return $this;
    }

    protected function renderSchema() : ?string
    {
        if ( ! isset($this->sql['schema'])) {
            return null;
        }
        $schema = $this->sql['schema'];
        if (isset($this->sql['upgrade'])) {
            $schema = "#mysql50#{$schema}";
        }
        return ' ' . $this->database->protectIdentifier($schema);
    }

    /**
     * @param string $charset
     *
     * @return static
     */
    public function charset(string $charset) : static
    {
        $this->sql['charset'] = $charset;
        return $this;
    }

    protected function renderCharset() : ?string
    {
        if ( ! isset($this->sql['charset'])) {
            return null;
        }
        $charset = $this->database->quote($this->sql['charset']);
        return " CHARACTER SET = {$charset}";
    }

    /**
     * @param string $collation
     *
     * @return static
     */
    public function collate(string $collation) : static
    {
        $this->sql['collation'] = $collation;
        return $this;
    }

    protected function renderCollate() : ?string
    {
        if ( ! isset($this->sql['collation'])) {
            return null;
        }
        $collation = $this->database->quote($this->sql['collation']);
        return " COLLATE = {$collation}";
    }

    /**
     * @return static
     */
    public function upgrade() : static
    {
        $this->sql['upgrade'] = true;
        return $this;
    }

    protected function renderUpgrade() : ?string
    {
        if ( ! isset($this->sql['upgrade'])) {
            return null;
        }
        if (isset($this->sql['charset']) || isset($this->sql['collation'])) {
            throw new LogicException(
                'UPGRADE DATA DIRECTORY NAME can not be used with CHARACTER SET or COLLATE'
            );
        }
        return ' UPGRADE DATA DIRECTORY NAME';
    }

    protected function checkSpecifications() : void
    {
        if ( ! isset($this->sql['charset'])
            && ! isset($this->sql['collation'])
            && ! isset($this->sql['upgrade'])
        ) {
            throw new LogicException(
                'ALTER SCHEMA must have a specification'
            );
        }
    }

    public function sql() : string
    {
        $sql = 'ALTER SCHEMA';
        $sql .= $this->renderSchema() . \PHP_EOL;
        $part = $this->renderCharset();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderCollate();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $part = $this->renderUpgrade();
        if ($part) {
            $sql .= $part . \PHP_EOL;
        }
        $this->checkSpecifications();
        return $sql;
    }

    /**
     * Runs the ALTER SCHEMA statement.
     *
     * @return int|string The number of affected rows
     */
    public function run() : int|string
    {
        return $this->database->exec($this->sql());
    }
}
