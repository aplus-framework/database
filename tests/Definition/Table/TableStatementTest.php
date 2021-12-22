<?php
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table;

use Framework\Database\Definition\Table\TableStatement;

final class TableStatementTest extends \Tests\Database\TestCase
{
    protected TableStatementMock $statement;

    protected function setUp() : void
    {
        $this->statement = new TableStatementMock(static::$database);
    }

    public function testOption() : void
    {
        $this->statement->option('engine', 'innodb');
        self::assertSame(' ENGINE = InnoDB', $this->statement->sql());
        $this->statement->option('charset', 'utf8');
        self::assertSame(' ENGINE = InnoDB, CHARSET = utf8', $this->statement->sql());
        $this->statement->option('engine', 'myisam');
        self::assertSame(' ENGINE = MyISAM, CHARSET = utf8', $this->statement->sql());
    }

    public function testOptions() : void
    {
        $this->statement->options([
            'charset' => 'utf8',
            'engine' => 'innodb',
            'checksum' => 0,
        ]);
        self::assertSame(
            ' CHARSET = utf8, ENGINE = InnoDB, CHECKSUM = 0',
            $this->statement->sql()
        );
    }

    public function testInvalidOption() : void
    {
        $this->statement->option('Foo', 'bar');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid option: Foo');
        $this->statement->sql();
    }

    public function testEngineOption() : void
    {
        $this->statement->option(TableStatement::OPT_ENGINE, 'myisam');
        self::assertSame(' ENGINE = MyISAM', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_ENGINE, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ENGINE option value: Foo');
        $this->statement->sql();
    }

    public function testAutoIncrementOption() : void
    {
        $this->statement->option(TableStatement::OPT_AUTO_INCREMENT, 1000);
        self::assertSame(' AUTO_INCREMENT = 1000', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_AUTO_INCREMENT, 'One');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid AUTO_INCREMENT option value: One');
        $this->statement->sql();
    }

    public function testCharsetOption() : void
    {
        $this->statement->option(TableStatement::OPT_CHARSET, 'UTF8MB4');
        self::assertSame(' CHARSET = utf8mb4', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_CHARSET, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CHARSET option value: Foo');
        $this->statement->sql();
    }

    public function testChecksumOption() : void
    {
        $this->statement->option(TableStatement::OPT_CHECKSUM, 0);
        self::assertSame(' CHECKSUM = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_CHECKSUM, 2);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid CHECKSUM option value: 2');
        $this->statement->sql();
    }

    public function testCommentOption() : void
    {
        $this->statement->option(TableStatement::OPT_COMMENT, "Fo\\'o is F\\\"oo");
        self::assertSame(" COMMENT = 'Fo\\\\\\'o is F\\\\\\\"oo'", $this->statement->sql());
    }

    public function testConnectionOption() : void
    {
        $this->statement->option(TableStatement::OPT_CONNECTION, 'mysql://root@127.0.0.1:3307/db1/t1');
        self::assertSame(" CONNECTION = 'mysql://root@127.0.0.1:3307/db1/t1'", $this->statement->sql());
    }

    public function testDataDirectoryOption() : void
    {
        $this->statement->option(TableStatement::OPT_DATA_DIRECTORY, 'foo');
        self::assertSame(" DATA DIRECTORY = 'foo'", $this->statement->sql());
    }

    public function testDelayKeyWriteOption() : void
    {
        $this->statement->option(TableStatement::OPT_DELAY_KEY_WRITE, 0);
        self::assertSame(' DELAY_KEY_WRITE = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_DELAY_KEY_WRITE, 2);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid DELAY_KEY_WRITE option value: 2');
        $this->statement->sql();
    }

    public function testIetfQuotesOption() : void
    {
        $this->statement->option(TableStatement::OPT_IETF_QUOTES, 'yes');
        self::assertSame(' IETF_QUOTES = YES', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_IETF_QUOTES, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid IETF_QUOTES option value: Foo');
        $this->statement->sql();
    }

    public function testIndexDirectoryOption() : void
    {
        $this->statement->option(TableStatement::OPT_INDEX_DIRECTORY, 'foo');
        self::assertSame(" INDEX DIRECTORY = 'foo'", $this->statement->sql());
    }

    public function testInsertMethodOption() : void
    {
        $this->statement->option(TableStatement::OPT_INSERT_METHOD, 'last');
        self::assertSame(' INSERT_METHOD = LAST', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_INSERT_METHOD, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid INSERT_METHOD option value: Foo');
        $this->statement->sql();
    }

    public function testEncryptedOption() : void
    {
        $this->statement->option(TableStatement::OPT_ENCRYPTED, 'yes');
        self::assertSame(' ENCRYPTED = YES', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_ENCRYPTED, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ENCRYPTED option value: Foo');
        $this->statement->sql();
    }

    public function testMaxRowsOption() : void
    {
        $this->statement->option(TableStatement::OPT_MAX_ROWS, 10000);
        self::assertSame(' MAX_ROWS = 10000', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_MAX_ROWS, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid MAX_ROWS option value: Foo');
        $this->statement->sql();
    }

    public function testMinRowsOption() : void
    {
        $this->statement->option(TableStatement::OPT_MIN_ROWS, 100);
        self::assertSame(' MIN_ROWS = 100', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_MIN_ROWS, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid MIN_ROWS option value: Foo');
        $this->statement->sql();
    }

    public function testPageChecksumOption() : void
    {
        $this->statement->option(TableStatement::OPT_PAGE_CHECKSUM, 0);
        self::assertSame(' PAGE_CHECKSUM = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_PAGE_CHECKSUM, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid PAGE_CHECKSUM option value: Foo');
        $this->statement->sql();
    }

    public function testPageCompressedOption() : void
    {
        $this->statement->option(TableStatement::OPT_PAGE_COMPRESSED, 0);
        self::assertSame(' PAGE_COMPRESSED = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_PAGE_COMPRESSED, 10);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid PAGE_COMPRESSED option value: 10');
        $this->statement->sql();
    }

    public function testPageCompressionLevelOption() : void
    {
        $this->statement->option(TableStatement::OPT_PAGE_COMPRESSION_LEVEL, 0);
        self::assertSame(' PAGE_COMPRESSION_LEVEL = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_PAGE_COMPRESSION_LEVEL, 2);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid PAGE_COMPRESSION_LEVEL option value: 2');
        $this->statement->sql();
    }

    public function testPasswordOption() : void
    {
        $this->statement->option(TableStatement::OPT_PASSWORD, 'foo');
        self::assertSame(" PASSWORD = 'foo'", $this->statement->sql());
    }

    public function testStatsAutoRecalcOption() : void
    {
        $this->statement->option(TableStatement::OPT_STATS_AUTO_RECALC, 0);
        self::assertSame(' STATS_AUTO_RECALC = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_STATS_AUTO_RECALC, 2);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid STATS_AUTO_RECALC option value: 2');
        $this->statement->sql();
    }

    public function testStatsPersistentOption() : void
    {
        $this->statement->option(TableStatement::OPT_STATS_PERSISTENT, 0);
        self::assertSame(' STATS_PERSISTENT = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_STATS_PERSISTENT, 2);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid STATS_PERSISTENT option value: 2');
        $this->statement->sql();
    }

    public function testRowFormatOption() : void
    {
        $this->statement->option(TableStatement::OPT_ROW_FORMAT, 'default');
        self::assertSame(' ROW_FORMAT = DEFAULT', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_ROW_FORMAT, 'Foo');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ROW_FORMAT option value: Foo');
        $this->statement->sql();
    }

    public function testTransactionOption() : void
    {
        $this->statement->option(TableStatement::OPT_TRANSACTIONAL, 0);
        self::assertSame(' TRANSACTIONAL = 0', $this->statement->sql());
        $this->statement->option(TableStatement::OPT_TRANSACTIONAL, 2);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid TRANSACTIONAL option value: 2');
        $this->statement->sql();
    }
}
