<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation;

use Framework\Database\Database;
use Tests\Database\TestCase;

final class StatementTest extends TestCase
{
    protected StatementMock $statement;

    public function setup() : void
    {
        $this->statement = new StatementMock(static::$database);
    }

    public function testLimit() : void
    {
        self::assertNull($this->statement->renderLimit());
        $this->statement->limit(10);
        self::assertSame(' LIMIT 10', $this->statement->renderLimit());
        $this->statement->limit(10, 20);
        self::assertSame(' LIMIT 10 OFFSET 20', $this->statement->renderLimit());
    }

    public function testLimitLessThanOne() : void
    {
        $this->statement->limit(0);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('LIMIT must be greater than 0');
        $this->statement->renderLimit();
    }

    public function testLimitOffsetLessThanOne() : void
    {
        $this->statement->limit(10, 0);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('LIMIT OFFSET must be greater than 0');
        $this->statement->renderLimit();
    }

    public function testSubquery() : void
    {
        self::assertSame(
            '(select database())',
            $this->statement->subquery(static fn () => 'select database()')
        );
        self::assertSame(
            '(select * from posts)',
            $this->statement->subquery(static fn () => 'select * from posts')
        );
        self::assertSame(
            '(select * from `posts`)',
            $this->statement->subquery(static function ($database) {
                self::assertInstanceOf(Database::class, $database);
                return 'select * from ' . $database->protectIdentifier('posts');
            })
        );
    }

    public function testRenderIdentifier() : void
    {
        self::assertSame('`name```', $this->statement->renderIdentifier('name`'));
        self::assertSame(
            '(SELECT * from `foo`)',
            $this->statement->renderIdentifier(
                static fn ($db) => 'SELECT * from ' . $db->protectIdentifier('foo')
            )
        );
    }

    public function testRenderAliasedIdentifier() : void
    {
        self::assertSame('`name```', $this->statement->renderAliasedIdentifier('name`'));
        self::assertSame(
            '(SELECT * from `foo`)',
            $this->statement->renderAliasedIdentifier(
                static fn ($db) => 'SELECT * from ' . $db->protectIdentifier('foo')
            )
        );
        self::assertSame(
            '`name``` AS `foo`',
            $this->statement->renderAliasedIdentifier(['foo' => 'name`'])
        );
        self::assertSame(
            "(SELECT id from table where username = '\\'hack') AS `foo`",
            $this->statement->renderAliasedIdentifier([
                'foo' => static fn ($db) => 'SELECT id from table where username = '
                    . $db->quote("'hack"),
            ])
        );
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Aliased column must have only 1 key');
        // @phpstan-ignore-next-line
        $this->statement->renderAliasedIdentifier(['foo' => 'name', 'bar']);
    }

    public function testToString() : void
    {
        self::assertSame('SQL', (string) $this->statement);
    }

    public function testOptions() : void
    {
        self::assertNull($this->statement->renderOptions());
        $this->statement->options('foo');
        self::assertSame('foo', $this->statement->renderOptions());
        $this->statement->options('bar', 'baz');
        self::assertSame('bar baz', $this->statement->renderOptions());
    }

    public function testReset() : void
    {
        self::assertNull($this->statement->renderOptions());
        $this->statement->options('foo');
        self::assertSame('foo', $this->statement->renderOptions());
        $this->statement->reset('where');
        self::assertSame('foo', $this->statement->renderOptions());
        $this->statement->reset('options');
        self::assertNull($this->statement->renderOptions());
        $this->statement->options('foo');
        self::assertSame('foo', $this->statement->renderOptions());
        $this->statement->reset();
        self::assertNull($this->statement->renderOptions());
    }

    public function testRenderAssignment() : void
    {
        self::assertSame('`id` = 1', $this->statement->renderAssignment('id', 1));
        self::assertSame("`id` = '1'", $this->statement->renderAssignment('id', '1'));
        self::assertSame(
            '`id` = (select 1)',
            $this->statement->renderAssignment('id', static fn () => 'select 1')
        );
    }

    public function testMergeExpressions() : void
    {
        self::assertSame(['a'], $this->statement->mergeExpressions('a', []));
        self::assertSame(['a', 'a'], $this->statement->mergeExpressions('a', ['a']));
    }
}
