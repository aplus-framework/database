<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Result;

/**
 * Class Explain.
 *
 * @see https://mariadb.com/kb/en/explain/
 *
 * @package database
 */
readonly class Explain
{
    /**
     * Sequence number that shows in which order tables are joined.
     */
    public int $id;
    /**
     * What kind of SELECT the table comes from.
     *
     * @see https://mariadb.com/kb/en/explain/#select_type-column
     */
    public string $selectType;
    /**
     * Alias name of table. Materialized temporary tables for sub queries are
     * named <subquery#>.
     */
    public string $table;
    /**
     * How rows are found from the table (join type).
     *
     * @see https://mariadb.com/kb/en/explain/#type-column
     */
    public string $type;
    /**
     * keys in table that could be used to find rows in the table.
     */
    public ?string $possibleKeys;
    /**
     * The name of the key that is used to retrieve rows. NULL is no key was used.
     */
    public ?string $key;
    /**
     * How many bytes of the key that was used (shows if we are using only parts
     * of the multi-column key).
     */
    public ?string $keyLen;
    /**
     * The reference that is used as the key value.
     */
    public ?string $ref;
    /**
     * An estimate of how many rows we will find in the table for each key lookup.
     */
    public string $rows;
    /**
     * Extra information about this join.
     *
     * @see https://mariadb.com/kb/en/explain/#extra-column
     */
    public string $extra;
    /**
     * The EXTENDED keyword adds another column, filtered, to the output. This
     * is a percentage estimate of the table rows that will be filtered by the
     * condition.
     *
     * @see https://mariadb.com/kb/en/explain/#explain-extended
     */
    public float $filtered;
    /**
     * EXPLAIN FORMAT=JSON is a variant of EXPLAIN command that produces output
     * in JSON form.
     *
     * @see https://mariadb.com/kb/en/explain-format-json/
     */
    public string $explain;

    public function __construct(\stdClass $result)
    {
        foreach ((array) $result as $key => $value) {
            $key = \strtolower($key);
            $key = \ucwords($key, '_');
            $key = \strtr($key, ['_' => '']);
            $key[0] = \strtolower($key[0]);
            $this->{$key} = $value;
        }
    }
}
