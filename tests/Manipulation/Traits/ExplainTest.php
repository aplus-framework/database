<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation\Traits;

use InvalidArgumentException;
use Tests\Database\TestCase;

final class ExplainTest extends TestCase
{
    protected ExplainMock $statement;

    public function setup() : void
    {
        $this->statement = new ExplainMock(static::$database);
    }

    public function testExplain() : void
    {
        self::assertNull($this->statement->renderExplain());
        $this->statement->explain();
        self::assertSame(
            'EXPLAIN' . \PHP_EOL,
            $this->statement->renderExplain()
        );
    }

    public function testExplainWithOption() : void
    {
        $this->statement->explain('format=json');
        self::assertSame(
            'EXPLAIN FORMAT=JSON',
            $this->statement->renderExplain()
        );
    }

    public function testExplainWithInvalidOption() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid EXPLAIN option: foo');
        $this->statement->explain('foo');
    }

    public function testInvalidExpressionDataType() : void
    {
        $this->expectException(\TypeError::class);
        $this->statement->explain([]); // @phpstan-ignore-line
    }
}
