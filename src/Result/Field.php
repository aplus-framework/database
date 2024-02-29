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
 * @property-read string $name The name of the column
 * @property-read string $orgname Original column name if an alias was specified
 * @property-read string $table The name of the table this field belongs to (if not calculated)
 * @property-read string $orgtable Original table name if an alias was specified
 * @property-read string $def The default value for this field, represented as a string
 * @property-read string $db
 * @property-read string $catalog
 * @property-read int $maxLength The maximum width of the field for the result set
 * @property-read int $length The width of the field, as specified in the table definition
 * @property-read int $charsetnr The character set number for the field
 * @property-read int $flags An integer representing the bit-flags for the field
 * @property-read int $type The data type used for this field
 * @property-read int $decimals The number of decimals used (for integer fields)
 * @property-read string|null $typeName The data type name
 * @property-read bool $flagBinary Tell if it has the MYSQLI_BINARY_FLAG bit-flag
 * @property-read bool $flagBlob Tell if it has the MYSQLI_BLOB_FLAG bit-flag
 * @property-read bool $flagEnum Tell if it has the MYSQLI_ENUM_FLAG bit-flag
 * @property-read bool $flagGroup Tell if it has the MYSQLI_GROUP_FLAG bit-flag
 * @property-read bool $flagNum Tell if it has the MYSQLI_NUM_FLAG bit-flag
 * @property-read bool $flagSet Tell if it has the MYSQLI_SET_FLAG bit-flag
 * @property-read bool $flagTimestamp Tell if it has the MYSQLI_TIMESTAMP_FLAG bit-flag
 * @property-read bool $flagUnsigned Tell if it has the MYSQLI_UNSIGNED_FLAG bit-flag
 * @property-read bool $flagZerofill Tell if it has the MYSQLI_ZEROFILL_FLAG bit-flag
 * @property-read bool $flagAutoIncrement Tell if it has the MYSQLI_AUTO_INCREMENT_FLAG bit-flag
 * @property-read bool $flagMultipleKey Tell if it has the MYSQLI_MULTIPLE_KEY_FLAG bit-flag
 * @property-read bool $flagNotNull Tell if it has the MYSQLI_NOT_NULL_FLAG bit-flag
 * @property-read bool $flagPartKey Tell if it has the MYSQLI_PART_KEY_FLAG bit-flag
 * @property-read bool $flagPriKey Tell if it has the MYSQLI_PRI_KEY_FLAG bit-flag
 * @property-read bool $flagUniqueKey Tell if it has the MYSQLI_UNIQUE_KEY_FLAG bit-flag
 * @property-read bool $flagNoDefaultValue Tell if it has the MYSQLI_NO_DEFAULT_VALUE_FLAG bit-flag
 * @property-read bool $flagOnUpdateNow Tell if it has the MYSQLI_ON_UPDATE_NOW_FLAG bit-flag
 *
 * @package database
 */
readonly class Field
{
    public string $name;
    public string $orgname;
    public string $table;
    public string $orgtable;
    public string $def;
    public string $db;
    public string $catalog;
    public int $maxLength;
    public int $length;
    public int $charsetnr;
    public int $flags;
    public int $type;
    public int $decimals;
    public ?string $typeName;
    public bool $isBinary;
    public bool $isBlob;
    public bool $isEnum;
    public bool $isGroup;
    public bool $isNum;
    public bool $isSet;
    public bool $isTimestamp;
    public bool $isUnsigned;
    public bool $isZerofill;
    public bool $isAutoIncrement;
    public bool $isMultipleKey;
    public bool $isNotNull;
    public bool $isPartKey;
    public bool $isPriKey;
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
