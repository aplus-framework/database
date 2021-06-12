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
		$this->assertEquals(' LIMIT 10', $this->statement->renderLimit());
		$this->statement->limit(10, 20);
		$this->assertEquals(' LIMIT 10 OFFSET 20', $this->statement->renderLimit());
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
		$this->assertEquals('(select database())', $this->statement->subquery(static function () {
			return 'select database()';
		}));
		$this->assertEquals(
			'(select * from posts)',
			$this->statement->subquery(static function () {
				return 'select * from posts';
			})
		);
		$this->assertEquals(
			'(select * from `posts`)',
			$this->statement->subquery(function ($database) {
				$this->assertInstanceOf(Database::class, $database);
				return 'select * from ' . $database->protectIdentifier('posts');
			})
		);
	}

	public function testRenderIdentifier() : void
	{
		$this->assertEquals('`name```', $this->statement->renderIdentifier('name`'));
		$this->assertEquals(
			'(SELECT * from `foo`)',
			$this->statement->renderIdentifier(static function ($database) {
				return 'SELECT * from ' . $database->protectIdentifier('foo');
			})
		);
	}

	public function testRenderAliasedidentifier() : void
	{
		$this->assertEquals('`name```', $this->statement->renderAliasedIdentifier('name`'));
		$this->assertEquals(
			'(SELECT * from `foo`)',
			$this->statement->renderAliasedIdentifier(static function ($database) {
				return 'SELECT * from ' . $database->protectIdentifier('foo');
			})
		);
		$this->assertEquals(
			'`name``` AS `foo`',
			$this->statement->renderAliasedIdentifier(['foo' => 'name`'])
		);
		$this->assertEquals(
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
		$this->assertEquals('SQL', (string) $this->statement);
	}

	public function testOptions() : void
	{
		$this->assertNull($this->statement->renderOptions());
		$this->statement->options('foo');
		$this->assertEquals('foo', $this->statement->renderOptions());
		$this->statement->options('bar', 'baz');
		$this->assertEquals('bar baz', $this->statement->renderOptions());
	}

	public function testReset() : void
	{
		$this->assertNull($this->statement->renderOptions());
		$this->statement->options('foo');
		$this->assertEquals('foo', $this->statement->renderOptions());
		$this->statement->reset('where');
		$this->assertEquals('foo', $this->statement->renderOptions());
		$this->statement->reset('options');
		$this->assertNull($this->statement->renderOptions());
		$this->statement->options('foo');
		$this->assertEquals('foo', $this->statement->renderOptions());
		$this->statement->reset();
		$this->assertNull($this->statement->renderOptions());
	}

	public function testRenderAssignment() : void
	{
		$this->assertEquals('`id` = 1', $this->statement->renderAssignment('id', 1));
		$this->assertEquals("`id` = '1'", $this->statement->renderAssignment('id', '1'));
		$this->assertEquals(
			'`id` = (select 1)',
			$this->statement->renderAssignment('id', static function () {
				return 'select 1';
			})
		);
	}

	public function testMergeExpressions() : void
	{
		$this->assertEquals(['a'], $this->statement->mergeExpressions('a', []));
		$this->assertEquals(['a', 'a'], $this->statement->mergeExpressions('a', ['a']));
	}
}
