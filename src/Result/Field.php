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
 * Class Field.
 *
 * @package database
 */
readonly class Field
{
    /**
     * The name of the column.
     */
    public string $name;
    /**
     * Original column name if an alias was specified.
     */
    public string $orgname;
    /**
     * The name of the table this field belongs to (if not calculated).
     */
    public string $table;
    /**
     * Original table name if an alias was specified.
     */
    public string $orgtable;
    /**
     * Reserved for default value, currently always "".
     */
    public string $def;
    /**
     * Database (since PHP 5.3.6).
     */
    public string $db;
    /**
     * The catalog name, always "def" (since PHP 5.3.6).
     */
    public string $catalog;
    /**
     * The maximum width of the field for the result set. As of PHP 8.1, this
     * value is always 0.
     */
    public int $maxLength;
    /**
     * The width of the field, in bytes, as specified in the table definition.
     * Note that this number (bytes) might differ from your table definition
     * value (characters), depending on the character set you use. For example,
     * the character set utf8 has 3 bytes per character, so varchar(10) will
     * return a length of 30 for utf8 (10*3), but return 10 for latin1 (10*1).
     */
    public int $length;
    /**
     * The character set number (id) for the field.
     */
    public int $charsetnr;
    /**
     * An integer representing the bit-flags for the field.
     */
    public int $flags;
    /**
     * The data type used for this field.
     */
    public int $type;
    /**
     * The number of decimals used (for integer fields).
     */
    public int $decimals;
    /**
     * The name of the data type used for this field.
     */
    public ?string $typeName;
    /**
     * Tells if field is defined as BINARY.
     */
    public bool $isBinary;
    /**
     * Tells if field is defined as BLOB.
     */
    public bool $isBlob;
    /**
     * Tells if field is defined as ENUM.
     */
    public bool $isEnum;
    /**
     * Tells if field is part of GROUP BY.
     */
    public bool $isGroup;
    /**
     * Tells if field is defined as NUMERIC.
     */
    public bool $isNum;
    /**
     * Tells if field is defined as SET.
     */
    public bool $isSet;
    /**
     * Tells if field is defined as TIMESTAMP.
     */
    public bool $isTimestamp;
    /**
     * Tells if field is defined as UNSIGNED.
     */
    public bool $isUnsigned;
    /**
     * Tells if field is defined as ZEROFILL.
     */
    public bool $isZerofill;
    /**
     * Tells if field is defined as AUTO_INCREMENT.
     */
    public bool $isAutoIncrement;
    /**
     * Tells if field is part of an index.
     */
    public bool $isMultipleKey;
    /**
     * Tells if field is defined as NOT NULL.
     */
    public bool $isNotNull;
    /**
     * Tells if field is part of a multi-index.
     */
    public bool $isPartKey;
    /**
     * Tells if field is part of a primary index.
     */
    public bool $isPriKey;
    /**
     * Tells if field is part of a unique index.
     */
    public bool $isUniqueKey;
    public bool $isNoDefaultValue;
    public bool $isOnUpdateNow;

    public function __construct(\stdClass $field)
    {
        foreach ((array) $field as $key => $value) {
            $key = \ucwords($key, '_');
            $key = \strtr($key, ['_' => '']);
            $key[0] = \strtolower($key[0]);
            $this->{$key} = $value;
        }
        $this->setTypeName();
        $this->setFlags();
    }

    protected function setTypeName() : void
    {
        $this->typeName = match ($this->type) {
            \MYSQLI_TYPE_BIT => 'bit',
            \MYSQLI_TYPE_BLOB => 'blob',
            \MYSQLI_TYPE_CHAR => 'char',
            \MYSQLI_TYPE_DATE => 'date',
            \MYSQLI_TYPE_DATETIME => 'datetime',
            \MYSQLI_TYPE_DECIMAL => 'decimal',
            \MYSQLI_TYPE_DOUBLE => 'double',
            \MYSQLI_TYPE_ENUM => 'enum',
            \MYSQLI_TYPE_FLOAT => 'float',
            \MYSQLI_TYPE_GEOMETRY => 'geometry',
            \MYSQLI_TYPE_INT24 => 'int24',
            //\MYSQLI_TYPE_INTERVAL => 'interval',
            \MYSQLI_TYPE_JSON => 'json',
            \MYSQLI_TYPE_LONG => 'long',
            \MYSQLI_TYPE_LONG_BLOB => 'long_blob',
            \MYSQLI_TYPE_LONGLONG => 'longlong',
            \MYSQLI_TYPE_MEDIUM_BLOB => 'medium_blob',
            \MYSQLI_TYPE_NEWDATE => 'newdate',
            \MYSQLI_TYPE_NEWDECIMAL => 'newdecimal',
            \MYSQLI_TYPE_NULL => 'null',
            \MYSQLI_TYPE_SET => 'set',
            \MYSQLI_TYPE_SHORT => 'short',
            \MYSQLI_TYPE_STRING => 'string',
            \MYSQLI_TYPE_TIME => 'time',
            \MYSQLI_TYPE_TIMESTAMP => 'timestamp',
            //\MYSQLI_TYPE_TINY => 'tiny',
            \MYSQLI_TYPE_TINY_BLOB => 'tiny_blob',
            \MYSQLI_TYPE_VAR_STRING => 'var_string',
            \MYSQLI_TYPE_YEAR => 'year',
            default => null
        };
    }

    protected function setFlags() : void
    {
        $this->isBinary = (bool) ($this->flags & \MYSQLI_BINARY_FLAG);
        $this->isBlob = (bool) ($this->flags & \MYSQLI_BLOB_FLAG);
        $this->isEnum = (bool) ($this->flags & \MYSQLI_ENUM_FLAG);
        $this->isGroup = (bool) ($this->flags & \MYSQLI_GROUP_FLAG);
        $this->isNum = (bool) ($this->flags & \MYSQLI_NUM_FLAG);
        $this->isSet = (bool) ($this->flags & \MYSQLI_SET_FLAG);
        $this->isTimestamp = (bool) ($this->flags & \MYSQLI_TIMESTAMP_FLAG);
        $this->isUnsigned = (bool) ($this->flags & \MYSQLI_UNSIGNED_FLAG);
        $this->isZerofill = (bool) ($this->flags & \MYSQLI_ZEROFILL_FLAG);
        $this->isAutoIncrement = (bool) ($this->flags & \MYSQLI_AUTO_INCREMENT_FLAG);
        $this->isMultipleKey = (bool) ($this->flags & \MYSQLI_MULTIPLE_KEY_FLAG);
        $this->isNotNull = (bool) ($this->flags & \MYSQLI_NOT_NULL_FLAG);
        $this->isPartKey = (bool) ($this->flags & \MYSQLI_PART_KEY_FLAG);
        $this->isPriKey = (bool) ($this->flags & \MYSQLI_PRI_KEY_FLAG);
        $this->isUniqueKey = (bool) ($this->flags & \MYSQLI_UNIQUE_KEY_FLAG);
        $this->isNoDefaultValue = (bool) ($this->flags & \MYSQLI_NO_DEFAULT_VALUE_FLAG);
        $this->isOnUpdateNow = (bool) ($this->flags & \MYSQLI_ON_UPDATE_NOW_FLAG);
    }
}
