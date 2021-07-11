<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Columns\Numeric;

use Tests\Database\TestCase;

final class NumericDataTypeTest extends TestCase
{
    protected NumericDataTypeMock $column;

    protected function setUp() : void
    {
        $this->column = new NumericDataTypeMock(static::$database);
    }

    public function testAutoIncrement() : void
    {
        self::assertSame(
            ' mock AUTO_INCREMENT NOT NULL',
            $this->column->autoIncrement()->sql()
        );
    }

    public function testSigned() : void
    {
        self::assertSame(
            ' mock signed NOT NULL',
            $this->column->signed()->sql()
        );
    }

    public function testUnsigned() : void
    {
        self::assertSame(
            ' mock unsigned NOT NULL',
            $this->column->unsigned()->sql()
        );
    }

    public function testZerofill() : void
    {
        self::assertSame(
            ' mock zerofill NOT NULL',
            $this->column->zerofill()->sql()
        );
    }

    public function testFull() : void
    {
        self::assertSame(
            ' mock unsigned zerofill AUTO_INCREMENT NOT NULL',
            $this->column->unsigned()->zerofill()->autoIncrement()->sql()
        );
    }
}
