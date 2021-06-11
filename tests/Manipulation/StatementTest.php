<?php namespace Tests\Database\Manipulation;

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
		$this->assertNull($this->statement->renderLimit());
		$this->statement->limit(10);
		$this->assertSame(' LIMIT 10', $this->statement->renderLimit());
		$this->statement->limit(10, 20);
		$this->assertSame(' LIMIT 10 OFFSET 20', $this->statement->renderLimit());
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
		$this->assertSame('(select database())', $this->statement->subquery(static function () {
			return 'select database()';
		}));
		$this->assertSame(
			'(select * from posts)',
			$this->statement->subquery(static function () {
				return 'select * from posts';
			})
		);
		$this->assertSame(
			'(select * from `posts`)',
			$this->statement->subquery(function ($database) {
				$this->assertInstanceOf(Database::class, $database);
				return 'select * from ' . $database->protectIdentifier('posts');
			})
		);
	}

	public function testRenderIdentifier() : void
	{
		$this->assertSame('`name```', $this->statement->renderIdentifier('name`'));
		$this->assertSame(
			'(SELECT * from `foo`)',
			$this->statement->renderIdentifier(static function ($database) {
				return 'SELECT * from ' . $database->protectIdentifier('foo');
			})
		);
	}

	public function testRenderAliasedidentifier() : void
	{
		$this->assertSame('`name```', $this->statement->renderAliasedIdentifier('name`'));
		$this->assertSame(
			'(SELECT * from `foo`)',
			$this->statement->renderAliasedIdentifier(static function ($database) {
				return 'SELECT * from ' . $database->protectIdentifier('foo');
			})
		);
		$this->assertSame(
			'`name``` AS `foo`',
			$this->statement->renderAliasedIdentifier(['foo' => 'name`'])
		);
		$this->assertSame(
			"(SELECT id from table where username = '\\'hack') AS `foo`",
			$this->statement->renderAliasedIdentifier([
				'foo' => static function ($database) {
					return 'SELECT id from table where username = '
						. $database->quote("'hack");
				},
			])
		);
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Aliased column must have only 1 key');
		$this->statement->renderAliasedIdentifier(['foo' => 'name', 'bar']);
	}

	public function testToString() : void
	{
		$this->assertSame('SQL', (string) $this->statement);
	}

	public function testOptions() : void
	{
		$this->assertNull($this->statement->renderOptions());
		$this->statement->options('foo');
		$this->assertSame('foo', $this->statement->renderOptions());
		$this->statement->options('bar', 'baz');
		$this->assertSame('bar baz', $this->statement->renderOptions());
	}

	public function testReset() : void
	{
		$this->assertNull($this->statement->renderOptions());
		$this->statement->options('foo');
		$this->assertSame('foo', $this->statement->renderOptions());
		$this->statement->reset('where');
		$this->assertSame('foo', $this->statement->renderOptions());
		$this->statement->reset('options');
		$this->assertNull($this->statement->renderOptions());
		$this->statement->options('foo');
		$this->assertSame('foo', $this->statement->renderOptions());
		$this->statement->reset();
		$this->assertNull($this->statement->renderOptions());
	}

	public function testRenderAssignment() : void
	{
		$this->assertSame('`id` = 1', $this->statement->renderAssignment('id', 1));
		$this->assertSame("`id` = '1'", $this->statement->renderAssignment('id', '1'));
		$this->assertSame(
			'`id` = (select 1)',
			$this->statement->renderAssignment('id', static function () {
				return 'select 1';
			})
		);
	}

	public function testMergeExpressions() : void
	{
		$this->assertSame(['a'], $this->statement->mergeExpressions('a', []));
		$this->assertSame(['a', 'a'], $this->statement->mergeExpressions('a', ['a']));
	}
}
