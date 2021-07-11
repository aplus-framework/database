<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Manipulation\Traits;

use Framework\Database\Database;
use Tests\Database\TestCase;

final class HavingTest extends TestCase
{
    protected HavingMock $statement;

    public function setup() : void
    {
        $this->statement = new HavingMock(static::$database);
    }

    public function testHaving() : void
    {
        self::assertNull($this->statement->renderHaving());
        $this->statement->having('id', '=', 10);
        self::assertSame(' HAVING `id` = 10', $this->statement->renderHaving());
        $this->statement->having('name', '=', "'foo");
        self::assertSame(
            " HAVING `id` = 10 AND `name` = '\\'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->orHaving('created_at', '>', static function () {
            return 'NOW() - 60';
        });
        self::assertSame(
            " HAVING `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60)",
            $this->statement->renderHaving()
        );
        $this->statement->having(static function (Database $database) {
            return $database->protectIdentifier('random_table');
        }, '!=', 'bar');
        self::assertSame(
            " HAVING `id` = 10 AND `name` = '\\'foo' OR `created_at` > (NOW() - 60) AND (`random_table`) != 'bar'",
            $this->statement->renderHaving()
        );
    }

    public function testEqual() : void
    {
        $this->statement->havingEqual('email', 'user@mail.com');
        self::assertSame(" HAVING `email` = 'user@mail.com'", $this->statement->renderHaving());
        $this->statement->orHavingEqual('name', 'foo');
        self::assertSame(
            " HAVING `email` = 'user@mail.com' OR `name` = 'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->havingEqual(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `email` = 'user@mail.com' OR `name` = 'foo' AND (id) = (10)",
            $this->statement->renderHaving()
        );
    }

    public function testNotEqual() : void
    {
        $this->statement->havingNotEqual('email', 'user@mail.com');
        self::assertSame(" HAVING `email` != 'user@mail.com'", $this->statement->renderHaving());
        $this->statement->orHavingNotEqual('name', 'foo');
        self::assertSame(
            " HAVING `email` != 'user@mail.com' OR `name` != 'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->havingNotEqual(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `email` != 'user@mail.com' OR `name` != 'foo' AND (id) != (10)",
            $this->statement->renderHaving()
        );
    }

    public function testNullSafeEqual() : void
    {
        $this->statement->havingNullSafeEqual('email', 'user@mail.com');
        self::assertSame(
            " HAVING `email` <=> 'user@mail.com'",
            $this->statement->renderHaving()
        );
        $this->statement->orHavingNullSafeEqual('name', null);
        self::assertSame(
            " HAVING `email` <=> 'user@mail.com' OR `name` <=> NULL",
            $this->statement->renderHaving()
        );
        $this->statement->havingNullSafeEqual(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `email` <=> 'user@mail.com' OR `name` <=> NULL AND (id) <=> (10)",
            $this->statement->renderHaving()
        );
    }

    public function testLessThan() : void
    {
        $this->statement->havingLessThan('count', 5);
        self::assertSame(' HAVING `count` < 5', $this->statement->renderHaving());
        $this->statement->orHavingLessThan('name', 'foo');
        self::assertSame(
            " HAVING `count` < 5 OR `name` < 'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->havingLessThan(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `count` < 5 OR `name` < 'foo' AND (id) < (10)",
            $this->statement->renderHaving()
        );
    }

    public function testLessThanOrEqual() : void
    {
        $this->statement->havingLessThanOrEqual('count', 5);
        self::assertSame(' HAVING `count` <= 5', $this->statement->renderHaving());
        $this->statement->orHavingLessThanOrEqual('name', 'foo');
        self::assertSame(
            " HAVING `count` <= 5 OR `name` <= 'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->havingLessThanOrEqual(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `count` <= 5 OR `name` <= 'foo' AND (id) <= (10)",
            $this->statement->renderHaving()
        );
    }

    public function testGreaterThan() : void
    {
        $this->statement->havingGreaterThan('count', 5);
        self::assertSame(' HAVING `count` > 5', $this->statement->renderHaving());
        $this->statement->orHavingGreaterThan('name', 'foo');
        self::assertSame(
            " HAVING `count` > 5 OR `name` > 'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->havingGreaterThan(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `count` > 5 OR `name` > 'foo' AND (id) > (10)",
            $this->statement->renderHaving()
        );
    }

    public function testGreaterThanOrEqual() : void
    {
        $this->statement->havingGreaterThanOrEqual('count', 5);
        self::assertSame(' HAVING `count` >= 5', $this->statement->renderHaving());
        $this->statement->orHavingGreaterThanOrEqual('name', 'foo');
        self::assertSame(
            " HAVING `count` >= 5 OR `name` >= 'foo'",
            $this->statement->renderHaving()
        );
        $this->statement->havingGreaterThanOrEqual(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `count` >= 5 OR `name` >= 'foo' AND (id) >= (10)",
            $this->statement->renderHaving()
        );
    }

    public function testLike() : void
    {
        $this->statement->havingLike('email', '%@mail.com');
        self::assertSame(" HAVING `email` LIKE '%@mail.com'", $this->statement->renderHaving());
        $this->statement->orHavingLike('name', 'foo%');
        self::assertSame(
            " HAVING `email` LIKE '%@mail.com' OR `name` LIKE 'foo%'",
            $this->statement->renderHaving()
        );
        $this->statement->havingLike(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `email` LIKE '%@mail.com' OR `name` LIKE 'foo%' AND (id) LIKE (10)",
            $this->statement->renderHaving()
        );
    }

    public function testNotLike() : void
    {
        $this->statement->havingNotLike('email', '%@mail.com');
        self::assertSame(
            " HAVING `email` NOT LIKE '%@mail.com'",
            $this->statement->renderHaving()
        );
        $this->statement->orHavingNotLike('name', 'foo%');
        self::assertSame(
            " HAVING `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%'",
            $this->statement->renderHaving()
        );
        $this->statement->havingNotLike(static function () {
            return 'id';
        }, static function () {
            return 10;
        });
        self::assertSame(
            " HAVING `email` NOT LIKE '%@mail.com' OR `name` NOT LIKE 'foo%' AND (id) NOT LIKE (10)",
            $this->statement->renderHaving()
        );
    }

    public function testIn() : void
    {
        $this->statement->havingIn('id', 1, 2, 8);
        self::assertSame(' HAVING `id` IN (1, 2, 8)', $this->statement->renderHaving());
        $this->statement->orHavingIn('code', 'abc', 'def');
        self::assertSame(
            " HAVING `id` IN (1, 2, 8) OR `code` IN ('abc', 'def')",
            $this->statement->renderHaving()
        );
        $this->statement->havingIn(static function () {
            return 'id';
        }, static function () {
            return 'SELECT * FROM foo';
        });
        self::assertSame(
            " HAVING `id` IN (1, 2, 8) OR `code` IN ('abc', 'def') AND (id) IN ((SELECT * FROM foo))",
            $this->statement->renderHaving()
        );
    }

    public function testNotIn() : void
    {
        $this->statement->havingNotIn('id', 1, 2, 8);
        self::assertSame(' HAVING `id` NOT IN (1, 2, 8)', $this->statement->renderHaving());
        $this->statement->orHavingNotIn('code', 'abc', 'def');
        self::assertSame(
            " HAVING `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def')",
            $this->statement->renderHaving()
        );
        $this->statement->havingNotIn(static function () {
            return 'id';
        }, static function () {
            return 'SELECT * FROM foo';
        });
        self::assertSame(
            " HAVING `id` NOT IN (1, 2, 8) OR `code` NOT IN ('abc', 'def') AND (id) NOT IN ((SELECT * FROM foo))",
            $this->statement->renderHaving()
        );
    }

    public function testBetween() : void
    {
        $this->statement->havingBetween('id', 1, 10);
        self::assertSame(' HAVING `id` BETWEEN 1 AND 10', $this->statement->renderHaving());
        $this->statement->orHavingBetween('code', 'abc', 'def');
        self::assertSame(
            " HAVING `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def'",
            $this->statement->renderHaving()
        );
        $this->statement->havingBetween(static function () {
            return 'id';
        }, static function () {
            return 'SELECT * FROM foo';
        }, static function () {
            return 'SELECT * FROM bar';
        });
        self::assertSame(
            " HAVING `id` BETWEEN 1 AND 10 OR `code` BETWEEN 'abc' AND 'def' AND (id) BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
            $this->statement->renderHaving()
        );
    }

    public function testNotBetween() : void
    {
        $this->statement->havingNotBetween('id', 1, 10);
        self::assertSame(' HAVING `id` NOT BETWEEN 1 AND 10', $this->statement->renderHaving());
        $this->statement->orHavingNotBetween('code', 'abc', 'def');
        self::assertSame(
            " HAVING `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def'",
            $this->statement->renderHaving()
        );
        $this->statement->havingNotBetween(static function () {
            return 'id';
        }, static function () {
            return 'SELECT * FROM foo';
        }, static function () {
            return 'SELECT * FROM bar';
        });
        self::assertSame(
            " HAVING `id` NOT BETWEEN 1 AND 10 OR `code` NOT BETWEEN 'abc' AND 'def' AND (id) NOT BETWEEN (SELECT * FROM foo) AND (SELECT * FROM bar)",
            $this->statement->renderHaving()
        );
    }

    public function testIsNull() : void
    {
        $this->statement->havingIsNull('email');
        self::assertSame(' HAVING `email` IS NULL', $this->statement->renderHaving());
        $this->statement->orHavingIsNull('name');
        self::assertSame(
            ' HAVING `email` IS NULL OR `name` IS NULL',
            $this->statement->renderHaving()
        );
        $this->statement->havingIsNull(static function () {
            return 'id';
        });
        self::assertSame(
            ' HAVING `email` IS NULL OR `name` IS NULL AND (id) IS NULL',
            $this->statement->renderHaving()
        );
    }

    public function testIsNotNull() : void
    {
        $this->statement->havingIsNotNull('email');
        self::assertSame(' HAVING `email` IS NOT NULL', $this->statement->renderHaving());
        $this->statement->orHavingIsNotNull('name');
        self::assertSame(
            ' HAVING `email` IS NOT NULL OR `name` IS NOT NULL',
            $this->statement->renderHaving()
        );
        $this->statement->havingIsNotNull(static function () {
            return 'id';
        });
        self::assertSame(
            ' HAVING `email` IS NOT NULL OR `name` IS NOT NULL AND (id) IS NOT NULL',
            $this->statement->renderHaving()
        );
    }
}
