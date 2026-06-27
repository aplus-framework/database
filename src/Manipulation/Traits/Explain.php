<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Manipulation\Traits;

use InvalidArgumentException;

/**
 * Trait Explain.
 *
 * @see https://mariadb.com/docs/server/reference/sql-statements/administrative-sql-statements/analyze-and-explain-statements/explain
 *
 * @package database
 *
 * @since 4
 */
trait Explain
{
    /**
     * @see https://mariadb.com/docs/server/reference/sql-statements/administrative-sql-statements/analyze-and-explain-statements/explain#explain-extended
     */
    public const string EXP_EXTENDED = 'EXTENDED';
    /**
     * @see https://mariadb.com/docs/server/server-usage/partitioning-tables/partition-pruning-and-selection
     */
    public const string EXP_PARTITIONS = 'PARTITIONS';
    /**
     * @see https://mariadb.com/docs/server/reference/sql-statements/administrative-sql-statements/analyze-and-explain-statements/explain-format-json
     */
    public const string EXP_FORMAT_JSON = 'FORMAT=JSON';

    /**
     * EXPLAIN provides information about statements.
     *
     * @param string|null $option WARNING: This feature is MariaDB only. It is not compatible with MySQL.
     *
     * @see https://mariadb.com/docs/server/reference/sql-statements/administrative-sql-statements/analyze-and-explain-statements/explain
     *
     * @return static
     */
    public function explain(?string $option = null) : static
    {
        $this->sql['explain'] = [
            'option' => null,
        ];
        if ($option !== null) {
            $opt = \strtoupper($option);
            if (!\in_array($opt, [
                static::EXP_EXTENDED,
                static::EXP_FORMAT_JSON,
                static::EXP_PARTITIONS,
            ], true)) {
                throw new InvalidArgumentException('Invalid EXPLAIN option: ' . $option);
            }
            $this->sql['explain']['option'] = $opt;
        }
        return $this;
    }

    protected function hasExplain() : bool
    {
        return isset($this->sql['explain']);
    }

    protected function renderExplain() : ?string
    {
        if (!$this->hasExplain()) {
           return null;
        }
        $option = '';
        if (isset($this->sql['explain']['option'])) {
            $option = ' ' . $this->sql['explain']['option'];
        }
        return 'EXPLAIN' . $option . \PHP_EOL;
    }
}
