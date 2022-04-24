<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Result;

use Framework\Database\Result\Field;
use PHPUnit\Framework\TestCase;

final class FieldTest extends TestCase
{
    /**
     * @return array<array<mixed>>
     */
    public function typeProvider() : array
    {
        return [
            [\MYSQLI_TYPE_BIT, 'bit'],
            [\MYSQLI_TYPE_BLOB, 'blob'],
            [\MYSQLI_TYPE_CHAR, 'char'],
            [\MYSQLI_TYPE_DATE, 'date'],
            [\MYSQLI_TYPE_DATETIME, 'datetime'],
            [\MYSQLI_TYPE_DECIMAL, 'decimal'],
            [\MYSQLI_TYPE_DOUBLE, 'double'],
            [\MYSQLI_TYPE_ENUM, 'enum'],
            [\MYSQLI_TYPE_FLOAT, 'float'],
            [\MYSQLI_TYPE_GEOMETRY, 'geometry'],
            [\MYSQLI_TYPE_INT24, 'int24'],
            //[\MYSQLI_TYPE_INTERVAL, 'interval'],
            [\MYSQLI_TYPE_JSON, 'json'],
            [\MYSQLI_TYPE_LONG, 'long'],
            [\MYSQLI_TYPE_LONG_BLOB, 'long_blob'],
            [\MYSQLI_TYPE_LONGLONG, 'longlong'],
            [\MYSQLI_TYPE_MEDIUM_BLOB, 'medium_blob'],
            [\MYSQLI_TYPE_NEWDATE, 'newdate'],
            [\MYSQLI_TYPE_NEWDECIMAL, 'newdecimal'],
            [\MYSQLI_TYPE_NULL, 'null'],
            [\MYSQLI_TYPE_SET, 'set'],
            [\MYSQLI_TYPE_SHORT, 'short'],
            [\MYSQLI_TYPE_STRING, 'string'],
            [\MYSQLI_TYPE_TIME, 'time'],
            [\MYSQLI_TYPE_TIMESTAMP, 'timestamp'],
            //[\MYSQLI_TYPE_TINY, 'tiny'],
            [\MYSQLI_TYPE_TINY_BLOB, 'tiny_blob'],
            [\MYSQLI_TYPE_VAR_STRING, 'var_string'],
            [\MYSQLI_TYPE_YEAR, 'year'],
            [-1, null],
        ];
    }

    /**
     * @param int $type
     * @param string|null $typeName
     *
     * @dataProvider typeProvider
     */
    public function testType(int $type, ?string $typeName) : void
    {
        $object = new \stdClass();
        $object->flags = $type;
        $object->type = $type;
        $field = new Field($object);
        self::assertSame($type, $field->type);
        self::assertSame($typeName, $field->typeName);
    }
}
