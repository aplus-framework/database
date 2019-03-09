<?php namespace Tests\Database\Manipulation\Statements;

use Framework\Database\Manipulation\Manipulation;
use PHPUnit\Framework\TestCase;

class StatementTest extends TestCase
{
	/**
	 * @var StatementMock
	 */
	protected $statement;

	public function setup()
	{
		$this->statement = new StatementMock();
	}

	public function testLimit()
	{
		$this->assertNull($this->statement->renderLimit());
		$this->statement->limit(10);
		$this->assertEquals(' LIMIT 10', $this->statement->renderLimit());
		$this->statement->limit(10, 20);
		$this->assertEquals(' LIMIT 10 OFFSET 20', $this->statement->renderLimit());
	}

	public function testLimitLessThanOne()
	{
		$this->statement->limit(0);
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('LIMIT must be greater than 0');
		$this->statement->renderLimit();
	}

	public function testLimitOffsetLessThanOne()
	{
		$this->statement->limit(10, 0);
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('LIMIT OFFSET must be greater than 0');
		$this->statement->renderLimit();
	}

	public function testSubquery()
	{
		$this->assertEquals('(select database())', $this->statement->subquery(function () {
			return 'select database()';
		}));
		$this->assertEquals(
			'(select * from posts)',
			$this->statement->subquery(function () {
				return 'select * from posts';
			})
		);
		$this->assertEquals(
			'(select * from `posts`)',
			$this->statement->subquery(function ($manipulation) {
				$this->assertInstanceOf(Manipulation::class, $manipulation);
				return 'select * from ' . $manipulation->database->protectIdentifier('posts');
			})
		);
	}

	public function testRenderColumn()
	{
		$this->assertEquals('`name```', $this->statement->renderColumn('name`'));
		$this->assertEquals(
			'(SELECT * from `foo`)',
			$this->statement->renderColumn(function ($manipulation) {
				return 'SELECT * from ' . $manipulation->database->protectIdentifier('foo');
			})
		);
	}

	public function testRenderAliasedColumn()
	{
		$this->assertEquals('`name```', $this->statement->renderAliasedColumn('name`'));
		$this->assertEquals(
			'(SELECT * from `foo`)',
			$this->statement->renderAliasedColumn(function ($manipulation) {
				return 'SELECT * from ' . $manipulation->database->protectIdentifier('foo');
			})
		);
		$this->assertEquals(
			'`name``` AS `foo`',
			$this->statement->renderAliasedColumn(['foo' => 'name`'])
		);
		$this->assertEquals(
			"(SELECT id from table where username = '\\'hack') AS `foo`",
			$this->statement->renderAliasedColumn([
				'foo' => function ($manipulation) {
					return 'SELECT id from table where username = '
						. $manipulation->database->quote("'hack");
				},
			])
		);
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Aliased column must have only 1 key');
		$this->statement->renderAliasedColumn(['foo' => 'name', 'bar']);
	}

	public function testToString()
	{
		$this->assertEquals('SQL', (string) $this->statement);
	}
}
