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
     * [STORAGE] ENGINE specifies a storage engine for the table.
     * If this option is not used, the default storage engine is used instead.
     *
     * @see https://mariadb.com/kb/en/create-table/#storage-engine
     *
     * @var string
     */
    public const OPT_ENGINE = 'ENGINE';
    /**
     * AUTO_INCREMENT specifies the initial value for the AUTO_INCREMENT primary
     * key. This works for MyISAM, Aria, InnoDB/XtraDB, MEMORY, and ARCHIVE tables.
     * You can change this option with ALTER TABLE, but in that case the new
     * value must be higher than the highest value which is present in the
     * AUTO_INCREMENT column. If the storage engine does not support this option,
     * you can insert (and then delete) a row having the wanted value - 1 in the
     * AUTO_INCREMENT column.
     *
     * @see https://mariadb.com/kb/en/create-table/#auto_increment
     *
     * @var string
     */
    public const OPT_AUTO_INCREMENT = 'AUTO_INCREMENT';
    /**
     * @var string
     */
    public const OPT_AVG_ROW_LENGTH = 'AVG_ROW_LENGTH';
    /**
     * @var string
     */
    public const OPT_CHARSET = 'CHARSET';
    /**
     * @var string
     */
    public const OPT_CHECKSUM = 'CHECKSUM';
    /**
     * @var string
     */
    public const OPT_COLLATE = 'COLLATE';
    /**
     * @var string
     */
    public const OPT_COMMENT = 'COMMENT';
    /**
     * @var string
     */
    public const OPT_CONNECTION = 'CONNECTION';
    /**
     * @var string
     */
    public const OPT_DATA_DIRECTORY = 'DATA DIRECTORY';
    /**
     * @var string
     */
    public const OPT_DELAY_KEY_WRITE = 'DELAY_KEY_WRITE';
    /**
     * @var string
     */
    public const OPT_ENCRYPTED = 'ENCRYPTED';
    /**
     * @var string
     */
    public const OPT_ENCRYPTION_KEY_ID = 'ENCRYPTION_KEY_ID';
    /**
     * @var string
     */
    public const OPT_IETF_QUOTES = 'IETF_QUOTES';
    /**
     * @var string
     */
    public const OPT_INDEX_DIRECTORY = 'INDEX DIRECTORY';
    /**
     * @var string
     */
    public const OPT_INSERT_METHOD = 'INSERT_METHOD';
    /**
     * @var string
     */
    public const OPT_KEY_BLOCK_SIZE = 'KEY_BLOCK_SIZE';
    /**
     * @var string
     */
    public const OPT_MAX_ROWS = 'MAX_ROWS';
    /**
     * @var string
     */
    public const OPT_MIN_ROWS = 'MIN_ROWS';
    /**
     * @var string
     */
    public const OPT_PACK_KEYS = 'PACK_KEYS';
    /**
     * @var string
     */
    public const OPT_PAGE_CHECKSUM = 'PAGE_CHECKSUM';
    /**
     * @var string
     */
    public const OPT_PAGE_COMPRESSED = 'PAGE_COMPRESSED';
    /**
     * @var string
     */
    public const OPT_PAGE_COMPRESSION_LEVEL = 'PAGE_COMPRESSION_LEVEL';
    /**
     * @var string
     */
    public const OPT_PASSWORD = 'PASSWORD';
    /**
     * @var string
     */
    public const OPT_ROW_FORMAT = 'ROW_FORMAT';
    /**
     * @var string
     */
    public const OPT_SEQUENCE = 'SEQUENCE';
    /**
     * @var string
     */
    public const OPT_STATS_AUTO_RECALC = 'STATS_AUTO_RECALC';
    /**
     * @var string
     */
    public const OPT_STATS_PERSISTENT = 'STATS_PERSISTENT';
    /**
     * @var string
     */
    public const OPT_STATS_SAMPLE_PAGES = 'STATS_SAMPLE_PAGES';
    /**
     * @var string
     */
    public const OPT_TABLESPACE = 'TABLESPACE';
    /**
     * @var string
     */
    public const OPT_TRANSACTIONAL = 'TRANSACTIONAL';
    /**
     * @var string
     */
    public const OPT_UNION = 'UNION';
    /**
     * @var string
     */
    public const OPT_WITH_SYSTEM_VERSIONING = 'WITH SYSTEM VERSIONING';

    public function option(string $name, int | string $value = null) : static
    {
        $this->sql['options'][$name] = $value;
        return $this;
    }

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
                static::OPT_AVG_ROW_LENGTH => '',
                static::OPT_CHARSET => $this->makeCharset($value),
                static::OPT_CHECKSUM => $this->makeChecksum($value),
                static::OPT_COLLATE => '',
                static::OPT_COMMENT => $this->makeComment($value),
                static::OPT_CONNECTION => $this->makeConnection($value),
                static::OPT_DATA_DIRECTORY => $this->makeDataDirectory($value),
                static::OPT_DELAY_KEY_WRITE => $this->makeDelayKeyWrite($value),
                static::OPT_ENCRYPTED => $this->makeEncrypted($value),
                static::OPT_ENCRYPTION_KEY_ID => '',
                static::OPT_IETF_QUOTES => $this->makeIetfQuotes($value),
                static::OPT_INDEX_DIRECTORY => $this->makeIndexDirectory($value),
                static::OPT_INSERT_METHOD => $this->makeInsertMethod($value),
                static::OPT_KEY_BLOCK_SIZE => '',
                static::OPT_MAX_ROWS => $this->makeMaxRows($value),
                static::OPT_MIN_ROWS => $this->makeMinRows($value),
                static::OPT_PACK_KEYS => '',
                static::OPT_PAGE_CHECKSUM => $this->makePageChecksum($value),
                static::OPT_PAGE_COMPRESSED => $this->makePageCompressed($value),
                static::OPT_PAGE_COMPRESSION_LEVEL => $this->makePageCompressionLevel($value),
                static::OPT_PASSWORD => $this->makePassword($value),
                static::OPT_ROW_FORMAT => $this->makeRowFormat($value),
                static::OPT_SEQUENCE => '',
                static::OPT_STATS_AUTO_RECALC => $this->makeStatsAutoRecalc($value),
                static::OPT_STATS_PERSISTENT => $this->makeStatsPersistent($value),
                static::OPT_STATS_SAMPLE_PAGES => '',
                static::OPT_TABLESPACE => '',
                static::OPT_TRANSACTIONAL => $this->makeTransactional($value),
                static::OPT_UNION => '',
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
        return $this->getValue(\strtolower($value), [
            'aria' => 'Aria',
            'csv' => 'CSV',
            'innodb' => 'InnoDB',
            'memory' => 'MEMORY',
            'mrg_myisam' => 'MRG_MyISAM',
            'myisam' => 'MyISAM',
            'sequence' => 'SEQUENCE',
        ], true) ?? $this->invalidValue(static::OPT_ENGINE, $value);
    }

    private function makeAutoIncrement(string $value) : string
    {
        return \is_numeric($value) ? $value : $this->invalidValue(static::OPT_AUTO_INCREMENT, $value);
    }

    private function makeCharset(string $value) : string
    {
        return $this->getValue(\strtolower($value), [
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
        ]) ?? $this->invalidValue(static::OPT_CHARSET, $value);
    }

    private function makeChecksum(string $value) : string
    {
        return $this->getValue($value, [
            '0',
            '1',
        ]) ?? $this->invalidValue(static::OPT_CHECKSUM, $value);
    }

    private function makeComment(string $value) : string
    {
        return $this->database->quote($value);
    }

    private function makeConnection(string $value) : string
    {
        return $this->database->quote($value);
    }

    private function makeDataDirectory(string $value) : string
    {
        return $this->database->quote($value);
    }

    private function makeDelayKeyWrite(string $value) : string
    {
        return $this->getValue($value, [
            '0',
            '1',
        ]) ?? $this->invalidValue(static::OPT_DELAY_KEY_WRITE, $value);
    }

    private function makeIetfQuotes(string $value) : string
    {
        return $this->getValue(\strtoupper($value), [
            'NO',
            'YES',
        ]) ?? $this->invalidValue(static::OPT_IETF_QUOTES, $value);
    }

    private function makeIndexDirectory(string $value) : string
    {
        return $this->database->quote($value);
    }

    private function makeInsertMethod(string $value) : string
    {
        return $this->getValue(\strtoupper($value), [
            'FIRST',
            'LAST',
            'NO',
        ]) ?? $this->invalidValue(static::OPT_INSERT_METHOD, $value);
    }

    private function makeEncrypted(string $value) : string
    {
        return $this->getValue(\strtoupper($value), [
            'NO',
            'YES',
        ]) ?? $this->invalidValue(static::OPT_ENCRYPTED, $value);
    }

    private function makeMaxRows(string $value) : string
    {
        if (\is_numeric($value) && $value > 0) {
            return $value;
        }
        $this->invalidValue(static::OPT_MAX_ROWS, $value);
    }

    private function makeMinRows(string $value) : string
    {
        if (\is_numeric($value) && $value >= 0) {
            return $value;
        }
        $this->invalidValue(static::OPT_MIN_ROWS, $value);
    }

    private function makePageChecksum(string $value) : string
    {
        return $this->getValue($value, [
            '0',
            '1',
        ]) ?? $this->invalidValue(static::OPT_PAGE_CHECKSUM, $value);
    }

    private function makePageCompressed(string $value) : string
    {
        return $this->getValue($value, \array_map('strval', \range('0', '9')))
        ?? $this->invalidValue(static::OPT_PAGE_COMPRESSED, $value);
    }

    private function makePageCompressionLevel(string $value) : string
    {
        return $this->getValue($value, [
            '0',
            '1',
        ]) ?? $this->invalidValue(static::OPT_PAGE_COMPRESSION_LEVEL, $value);
    }

    private function makePassword(string $value) : string
    {
        return $this->database->quote($value);
    }

    private function makeStatsAutoRecalc(string $value) : string
    {
        return $this->getValue(\strtoupper($value), [
            '0',
            '1',
            'DEFAULT',
        ]) ?? $this->invalidValue(static::OPT_STATS_AUTO_RECALC, $value);
    }

    private function makeStatsPersistent(string $value) : string
    {
        return $this->getValue(\strtoupper($value), [
            '0',
            '1',
            'DEFAULT',
        ]) ?? $this->invalidValue(static::OPT_STATS_PERSISTENT, $value);
    }

    private function makeRowFormat(string $value) : string
    {
        return $this->getValue(\strtoupper($value), [
            'COMPACT',
            'COMPRESSED',
            'DEFAULT',
            'DYNAMIC',
            'FIXED',
            'PAGE',
            'REDUNDANT',
        ]) ?? $this->invalidValue(static::OPT_ROW_FORMAT, $value);
    }

    private function makeTransactional(string $value) : string
    {
        return $this->getValue($value, [
            '0',
            '1',
        ]) ?? $this->invalidValue(static::OPT_TRANSACTIONAL, $value);
    }

    private function getValue(string $value, array $options, bool $getByKey = false) : ?string
    {
        if ($getByKey) {
            return $options[$value] ?? null;
        }
        if (\in_array($value, $options, true)) {
            return $value;
        }
        return null;
    }

    private function invalidValue(string $option, $value) : void
    {
        throw new InvalidArgumentException("Invalid {$option} option value: {$value}");
    }
}
