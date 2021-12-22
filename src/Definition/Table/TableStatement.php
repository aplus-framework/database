<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table;

use Framework\Database\Statement;
use InvalidArgumentException;

/**
 * Class TableStatement.
 *
 * @see https://mariadb.com/kb/en/create-table/#table-options
 *
 * @package database
 */
abstract class TableStatement extends Statement
{
    /**
     * @see https://mariadb.com/kb/en/create-table/#storage-engine
     *
     * @var string
     */
    public const OPT_ENGINE = 'ENGINE';
    /**
     * @see https://mariadb.com/kb/en/create-table/#auto_increment
     *
     * @var string
     */
    public const OPT_AUTO_INCREMENT = 'AUTO_INCREMENT';
    /**
     * @see https://mariadb.com/kb/en/create-table/#avg_row_length
     *
     * @var string
     */
    public const OPT_AVG_ROW_LENGTH = 'AVG_ROW_LENGTH';
    /**
     * @see https://mariadb.com/kb/en/create-table/#default-character-setcharset
     *
     * @var string
     */
    public const OPT_CHARSET = 'CHARSET';
    /**
     * @see https://mariadb.com/kb/en/create-table/#checksumtable_checksum
     *
     * @var string
     */
    public const OPT_CHECKSUM = 'CHECKSUM';
    /**
     * @see https://mariadb.com/kb/en/create-table/#default-collate
     *
     * @var string
     */
    public const OPT_COLLATE = 'COLLATE';
    /**
     * @see https://mariadb.com/kb/en/create-table/#comment
     *
     * @var string
     */
    public const OPT_COMMENT = 'COMMENT';
    /**
     * @see https://mariadb.com/kb/en/create-table/#connection
     *
     * @var string
     */
    public const OPT_CONNECTION = 'CONNECTION';
    /**
     * @see https://mariadb.com/kb/en/create-table/#data-directoryindex-directory
     *
     * @var string
     */
    public const OPT_DATA_DIRECTORY = 'DATA DIRECTORY';
    /**
     * @see https://mariadb.com/kb/en/create-table/#delay_key_write
     *
     * @var string
     */
    public const OPT_DELAY_KEY_WRITE = 'DELAY_KEY_WRITE';
    /**
     * @see https://mariadb.com/kb/en/create-table/#encrypted
     *
     * @var string
     */
    public const OPT_ENCRYPTED = 'ENCRYPTED';
    /**
     * @see https://mariadb.com/kb/en/create-table/#encryption_key_id
     *
     * @var string
     */
    public const OPT_ENCRYPTION_KEY_ID = 'ENCRYPTION_KEY_ID';
    /**
     * @see https://mariadb.com/kb/en/create-table/#ietf_quotes
     *
     * @var string
     */
    public const OPT_IETF_QUOTES = 'IETF_QUOTES';
    /**
     * @see https://mariadb.com/kb/en/create-table/#data-directoryindex-directory
     *
     * @var string
     */
    public const OPT_INDEX_DIRECTORY = 'INDEX DIRECTORY';
    /**
     * @see https://mariadb.com/kb/en/create-table/#insert_method
     *
     * @var string
     */
    public const OPT_INSERT_METHOD = 'INSERT_METHOD';
    /**
     * @see https://mariadb.com/kb/en/create-table/#key_block_size
     *
     * @var string
     */
    public const OPT_KEY_BLOCK_SIZE = 'KEY_BLOCK_SIZE';
    /**
     * @see https://mariadb.com/kb/en/create-table/#min_rowsmax_rows
     *
     * @var string
     */
    public const OPT_MAX_ROWS = 'MAX_ROWS';
    /**
     * @see https://mariadb.com/kb/en/create-table/#min_rowsmax_rows
     *
     * @var string
     */
    public const OPT_MIN_ROWS = 'MIN_ROWS';
    /**
     * @see https://mariadb.com/kb/en/create-table/#pack_keys
     *
     * @var string
     */
    public const OPT_PACK_KEYS = 'PACK_KEYS';
    /**
     * @see https://mariadb.com/kb/en/create-table/#page_checksum
     *
     * @var string
     */
    public const OPT_PAGE_CHECKSUM = 'PAGE_CHECKSUM';
    /**
     * @see https://mariadb.com/kb/en/create-table/#page_compressed
     *
     * @var string
     */
    public const OPT_PAGE_COMPRESSED = 'PAGE_COMPRESSED';
    /**
     * @see https://mariadb.com/kb/en/create-table/#page_compression_level
     *
     * @var string
     */
    public const OPT_PAGE_COMPRESSION_LEVEL = 'PAGE_COMPRESSION_LEVEL';
    /**
     * @see https://mariadb.com/kb/en/create-table/#password
     *
     * @var string
     */
    public const OPT_PASSWORD = 'PASSWORD';
    /**
     * @see https://mariadb.com/kb/en/create-table/#row_format
     *
     * @var string
     */
    public const OPT_ROW_FORMAT = 'ROW_FORMAT';
    /**
     * @see https://mariadb.com/kb/en/create-table/#sequence
     *
     * @var string
     */
    public const OPT_SEQUENCE = 'SEQUENCE';
    /**
     * @see https://mariadb.com/kb/en/create-table/#stats_auto_recalc
     *
     * @var string
     */
    public const OPT_STATS_AUTO_RECALC = 'STATS_AUTO_RECALC';
    /**
     * @see https://mariadb.com/kb/en/create-table/#stats_persistent
     *
     * @var string
     */
    public const OPT_STATS_PERSISTENT = 'STATS_PERSISTENT';
    /**
     * @see https://mariadb.com/kb/en/create-table/#stats_sample_pages
     *
     * @var string
     */
    public const OPT_STATS_SAMPLE_PAGES = 'STATS_SAMPLE_PAGES';
    /**
     * @see https://mariadb.com/kb/en/create-tablespace/
     *
     * @var string
     */
    public const OPT_TABLESPACE = 'TABLESPACE';
    /**
     * @see https://mariadb.com/kb/en/create-table/#transactional
     *
     * @var string
     */
    public const OPT_TRANSACTIONAL = 'TRANSACTIONAL';
    /**
     * @see https://mariadb.com/kb/en/create-table/#union
     *
     * @var string
     */
    public const OPT_UNION = 'UNION';
    /**
     * @see https://mariadb.com/kb/en/create-table/#with-system-versioning
     *
     * @var string
     */
    public const OPT_WITH_SYSTEM_VERSIONING = 'WITH SYSTEM VERSIONING';

    /**
     * Adds a table option.
     *
     * @param string $name
     * @param int|string|null $value
     *
     * @return static
     */
    public function option(string $name, int | string $value = null) : static
    {
        $this->sql['options'][$name] = $value;
        return $this;
    }

    /**
     * Adds table options.
     *
     * @param array<string,int|string> $options
     *
     * @return static
     */
    public function options(array $options) : static
    {
        foreach ($options as $name => $value) {
            $this->option($name, $value);
        }
        return $this;
    }

    protected function renderOptions() : ?string
    {
        if ( ! isset($this->sql['options'])) {
            return null;
        }
        $options = [];
        foreach ($this->sql['options'] as $name => $value) {
            $nameUpper = \strtoupper($name);
            $value = (string) $value;
            $value = match ($nameUpper) {
                static::OPT_ENGINE => $this->makeEngine($value),
                static::OPT_AUTO_INCREMENT => $this->makeAutoIncrement($value),
                static::OPT_AVG_ROW_LENGTH => $this->makeAvgRowLength($value),
                static::OPT_CHARSET => $this->makeCharset($value),
                static::OPT_CHECKSUM => $this->makeChecksum($value),
                static::OPT_COLLATE => $this->makeCollate($value),
                static::OPT_COMMENT => $this->makeComment($value),
                static::OPT_CONNECTION => $this->makeConnection($value),
                static::OPT_DATA_DIRECTORY => $this->makeDataDirectory($value),
                static::OPT_DELAY_KEY_WRITE => $this->makeDelayKeyWrite($value),
                static::OPT_ENCRYPTED => $this->makeEncrypted($value),
                static::OPT_ENCRYPTION_KEY_ID => $this->makeEncryptionKeyId($value),
                static::OPT_IETF_QUOTES => $this->makeIetfQuotes($value),
                static::OPT_INDEX_DIRECTORY => $this->makeIndexDirectory($value),
                static::OPT_INSERT_METHOD => $this->makeInsertMethod($value),
                static::OPT_KEY_BLOCK_SIZE => $this->makeKeyBlockSize($value),
                static::OPT_MAX_ROWS => $this->makeMaxRows($value),
                static::OPT_MIN_ROWS => $this->makeMinRows($value),
                static::OPT_PACK_KEYS => $this->makePackKeys($value),
                static::OPT_PAGE_CHECKSUM => $this->makePageChecksum($value),
                static::OPT_PAGE_COMPRESSED => $this->makePageCompressed($value),
                static::OPT_PAGE_COMPRESSION_LEVEL => $this->makePageCompressionLevel($value),
                static::OPT_PASSWORD => $this->makePassword($value),
                static::OPT_ROW_FORMAT => $this->makeRowFormat($value),
                static::OPT_SEQUENCE => $this->makeSequence($value),
                static::OPT_STATS_AUTO_RECALC => $this->makeStatsAutoRecalc($value),
                static::OPT_STATS_PERSISTENT => $this->makeStatsPersistent($value),
                static::OPT_STATS_SAMPLE_PAGES => $this->makeStatsSamplePages($value),
                static::OPT_TABLESPACE => $this->makeTablespace($value),
                static::OPT_TRANSACTIONAL => $this->makeTransactional($value),
                static::OPT_UNION => $this->makeUnion($value),
                static::OPT_WITH_SYSTEM_VERSIONING => '',
                default => throw new InvalidArgumentException('Invalid option: ' . $name)
            };
            $option = $nameUpper;
            if ($value !== '') {
                $option .= ' = ' . $value;
            }
            $options[] = $option;
        }
        return ' ' . \implode(', ', $options);
    }

    private function makeEngine(string $value) : string
    {
        return $this->getValue(static::OPT_ENGINE, $value, [
            'aria' => 'Aria',
            'csv' => 'CSV',
            'innodb' => 'InnoDB',
            'memory' => 'MEMORY',
            'mrg_myisam' => 'MRG_MyISAM',
            'myisam' => 'MyISAM',
            'sequence' => 'SEQUENCE',
        ], \strtolower($value), true);
    }

    private function makeAutoIncrement(string $value) : string
    {
        return \is_numeric($value)
            ? $value
            : throw $this->invalidValue(static::OPT_AUTO_INCREMENT, $value);
    }

    private function makeAvgRowLength(string $value) : string
    {
        if (\is_numeric($value) && $value >= 0) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_AVG_ROW_LENGTH, $value);
    }

    private function makeCharset(string $value) : string
    {
        return $this->getValue(static::OPT_CHARSET, $value, [
            'armscii8',
            'ascii',
            'big5',
            'binary',
            'cp1250',
            'cp1251',
            'cp1256',
            'cp1257',
            'cp850',
            'cp852',
            'cp866',
            'cp932',
            'dec8',
            'eucjpms',
            'euckr',
            'gb2312',
            'gbk',
            'geostd8',
            'greek',
            'hebrew',
            'hp8',
            'keybcs2',
            'koi8r',
            'koi8u',
            'latin1',
            'latin2',
            'latin5',
            'latin7',
            'macce',
            'macroman',
            'sjis',
            'swe7',
            'tis620',
            'ucs2',
            'ujis',
            'utf16',
            'utf16le',
            'utf32',
            'utf8',
            'utf8mb3',
            'utf8mb4',
        ], \strtolower($value));
    }

    private function makeChecksum(string $value) : string
    {
        return $this->getValue(static::OPT_CHECKSUM, $value, [
            '0',
            '1',
        ]);
    }

    private function makeCollate(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeComment(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeConnection(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeDataDirectory(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeDelayKeyWrite(string $value) : string
    {
        return $this->getValue(static::OPT_DELAY_KEY_WRITE, $value, [
            '0',
            '1',
        ]);
    }

    private function makeIetfQuotes(string $value) : string
    {
        return $this->getValue(static::OPT_IETF_QUOTES, $value, [
            'NO',
            'YES',
        ], \strtoupper($value));
    }

    private function makeIndexDirectory(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeInsertMethod(string $value) : string
    {
        return $this->getValue(static::OPT_INSERT_METHOD, $value, [
            'FIRST',
            'LAST',
            'NO',
        ], \strtoupper($value));
    }

    private function makeKeyBlockSize(string $value) : string
    {
        if (\is_numeric($value) && $value >= 0) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_KEY_BLOCK_SIZE, $value);
    }

    private function makeEncrypted(string $value) : string
    {
        return $this->getValue(static::OPT_ENCRYPTED, $value, [
            'NO',
            'YES',
        ], \strtoupper($value));
    }

    private function makeEncryptionKeyId(string $value) : string
    {
        if (\is_numeric($value) && $value >= 1) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_ENCRYPTION_KEY_ID, $value);
    }

    private function makeMaxRows(string $value) : string
    {
        if (\is_numeric($value) && $value >= 0) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_MAX_ROWS, $value);
    }

    private function makeMinRows(string $value) : string
    {
        if (\is_numeric($value) && $value >= 0) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_MIN_ROWS, $value);
    }

    private function makePackKeys(string $value) : string
    {
        return $this->getValue(static::OPT_PACK_KEYS, $value, [
            '0',
            '1',
        ]);
    }

    private function makePageChecksum(string $value) : string
    {
        return $this->getValue(static::OPT_PAGE_CHECKSUM, $value, [
            '0',
            '1',
        ]);
    }

    private function makePageCompressed(string $value) : string
    {
        return $this->getValue(
            static::OPT_PAGE_COMPRESSED,
            $value,
            \array_map('strval', \range(0, 9))
        );
    }

    private function makePageCompressionLevel(string $value) : string
    {
        return $this->getValue(static::OPT_PAGE_COMPRESSION_LEVEL, $value, [
            '0',
            '1',
        ]);
    }

    private function makePassword(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeStatsAutoRecalc(string $value) : string
    {
        return $this->getValue(static::OPT_STATS_AUTO_RECALC, $value, [
            '0',
            '1',
            'DEFAULT',
        ], \strtoupper($value));
    }

    private function makeStatsPersistent(string $value) : string
    {
        return $this->getValue(static::OPT_STATS_PERSISTENT, $value, [
            '0',
            '1',
            'DEFAULT',
        ], \strtoupper($value));
    }

    private function makeStatsSamplePages(string $value) : string
    {
        if (\strtoupper($value) === 'DEFAULT') {
            return 'DEFAULT';
        }
        if (\is_numeric($value) && $value > 0) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_STATS_SAMPLE_PAGES, $value);
    }

    private function makeTablespace(string $value) : string
    {
        return $this->quote($value);
    }

    private function makeRowFormat(string $value) : string
    {
        return $this->getValue(static::OPT_ROW_FORMAT, $value, [
            'COMPACT',
            'COMPRESSED',
            'DEFAULT',
            'DYNAMIC',
            'FIXED',
            'PAGE',
            'REDUNDANT',
        ], \strtoupper($value));
    }

    private function makeSequence(string $value) : string
    {
        if (\is_numeric($value) && $value >= 0) {
            return $value;
        }
        throw $this->invalidValue(static::OPT_SEQUENCE, $value);
    }

    private function makeTransactional(string $value) : string
    {
        return $this->getValue(static::OPT_TRANSACTIONAL, $value, [
            '0',
            '1',
        ]);
    }

    private function makeUnion(string $value) : string
    {
        if ($value === '') {
            throw $this->invalidValue(static::OPT_UNION, $value);
        }
        $tables = \array_map('trim', \explode(',', $value));
        foreach ($tables as &$table) {
            $table = $this->database->protectIdentifier($table);
        }
        unset($table);
        $tables = \implode(', ', $tables);
        return '(' . $tables . ')';
    }

    /**
     * @param string $optionName
     * @param string $originalValue
     * @param array<string> $options
     * @param string|null $value
     * @param bool $getByKey
     *
     * @return string
     */
    private function getValue(
        string $optionName,
        string $originalValue,
        array $options,
        string $value = null,
        bool $getByKey = false
    ) : string {
        $value ??= $originalValue;
        if ($getByKey) {
            if (isset($options[$value])) {
                return $options[$value];
            }
        } elseif (\in_array($value, $options, true)) {
            return $value;
        }
        throw $this->invalidValue($optionName, $originalValue);
    }

    private function invalidValue(string $option, string $value) : InvalidArgumentException
    {
        return new InvalidArgumentException("Invalid {$option} option value: {$value}");
    }

    private function quote(string $value) : string
    {
        return (string) $this->database->quote($value);
    }
}
