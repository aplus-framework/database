<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition;

use Framework\Database\Definition\DropSchema;
use Tests\Database\TestCase;

final class DropSchemaTest extends TestCase
{
    protected DropSchema $dropSchema;

    protected function setUp() : void
    {
        $this->dropSchema = new DropSchema(static::$database);
    }

    public function testEmptySchema() : void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('SCHEMA name must be set');
        $this->dropSchema->sql();
    }

    public function testSchema() : void
    {
        self::assertSame(
            "DROP SCHEMA `app`\n",
            $this->dropSchema->schema('app')->sql()
        );
    }

    public function testIfExists() : void
    {
        self::assertSame(
            "DROP SCHEMA IF EXISTS `app`\n",
            $this->dropSchema->ifExists()->schema('app')->sql()
        );
    }

    public function testRun() : void
    {
        static::$database->createSchema('app')->ifNotExists()->run();
        self::assertSame(
            0,
            $this->dropSchema->schema('app')->run()
        );
    }
}
