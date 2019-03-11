<?php namespace Tests\Database\Manipulation\Statements\Traits;

use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
	/**
	 * @var JoinMock
	 */
	protected $statement;

	public function setup()
	{
		$this->statement = new JoinMock();
	}

	public function testFrom()
	{
		$this->assertNull($this->statement->renderFrom());
		$this->statement->from('t1', 't2');
		$this->assertEquals(
			' FROM `t1`, `t2`',
			$this->statement->renderFrom()
		);
		$this->statement->from(['aliasname' => 't3']);
		$this->assertEquals(
			' FROM `t3` AS `aliasname`',
			$this->statement->renderFrom()
		);
		$this->statement->from(function () {
			return 'NOW()';
		});
		$this->assertEquals(
			' FROM (NOW())',
			$this->statement->renderFrom()
		);
		$this->statement->from([
			'time' => function () {
				return 'SELECT NOW()';
			},
		], ['noindex']);
		$this->assertEquals(
			' FROM (SELECT NOW()) AS `time`, `noindex` AS `0`',
			$this->statement->renderFrom()
		);
		$this->statement->from(
			't1',
			't2',
			['aliasname' => 't3'],
			function () {
				return 'NOW()';
			},
			[
				'time' => function () {
					return 'SELECT NOW()';
				},
			],
			[
				'noindex',
			]
		);
		$this->assertEquals(
			' FROM `t1`, `t2`, `t3` AS `aliasname`, (NOW()), (SELECT NOW()) AS `time`, `noindex` AS `0`',
			$this->statement->renderFrom()
		);
	}

	public function testHasFrom()
	{
		$this->assertFalse($this->statement->hasFrom());
		$this->statement->from('t1');
		$this->assertTrue($this->statement->hasFrom());
	}

	public function testHasFromException()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Clause WHERe only works with FROM');
		$this->statement->hasFrom('WHERe');
	}

	public function testJoin()
	{
		$this->assertNull($this->statement->renderJoin());
		$this->statement->join('users');
		$this->assertEquals(' JOIN `users`', $this->statement->renderJoin());
		$this->statement->join('users', 'natural');
		$this->assertEquals(' NATURAL JOIN `users`', $this->statement->renderJoin());
		$this->statement->join('users', 'cross', 'using', ['user_id']);
		$this->assertEquals(
			' CROSS JOIN `users` USING (`user_id`)',
			$this->statement->renderJoin()
		);
		$this->statement->join('users', 'left', 'on', function () {
			return 'profiles.user_id = users.id';
		});
		$this->assertEquals(
			' LEFT JOIN `users` ON (profiles.user_id = users.id)',
			$this->statement->renderJoin()
		);
	}

	public function testInvalidJoinType()
	{
		$this->statement->join('t1', 'innes');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid JOIN type: innes');
		$this->statement->renderJoin();
	}

	public function testInvalidJoinConditionClause()
	{
		$this->statement->join('t1', 'inner', 'oi');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid JOIN condition clause: oi');
		$this->statement->renderJoin();
	}

	public function testNaturalJoinWithCondition()
	{
		$this->statement->join('t1', 'natural left', 'on');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('NATURAL LEFT JOIN has not condition');
		$this->statement->renderJoin();
	}

	public function testJoinOn()
	{
		$this->statement->joinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(' JOIN `t1` ON (t1.id = t2.id)', $this->statement->renderJoin());
	}

	public function testJoinUsing()
	{
		$this->statement->joinUsing('t1', 'user_id');
		$this->assertEquals(' JOIN `t1` USING (`user_id`)', $this->statement->renderJoin());
	}

	public function testInnerJoinOn()
	{
		$this->statement->innerJoinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(' INNER JOIN `t1` ON (t1.id = t2.id)', $this->statement->renderJoin());
	}

	public function testInnerJoinUsing()
	{
		$this->statement->innerJoinUsing('t1', 'user_id');
		$this->assertEquals(' INNER JOIN `t1` USING (`user_id`)', $this->statement->renderJoin());
	}

	public function testCrossJoinOn()
	{
		$this->statement->crossJoinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(' CROSS JOIN `t1` ON (t1.id = t2.id)', $this->statement->renderJoin());
	}

	public function testCrossJoinUsing()
	{
		$this->statement->crossJoinUsing('t1', 'user_id');
		$this->assertEquals(' CROSS JOIN `t1` USING (`user_id`)', $this->statement->renderJoin());
	}

	public function testLeftJoinOn()
	{
		$this->statement->leftJoinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(' LEFT JOIN `t1` ON (t1.id = t2.id)', $this->statement->renderJoin());
	}

	public function testLeftJoinUsing()
	{
		$this->statement->leftJoinUsing('t1', 'user_id');
		$this->assertEquals(' LEFT JOIN `t1` USING (`user_id`)', $this->statement->renderJoin());
	}

	public function testLeftOuterJoinOn()
	{
		$this->statement->leftOuterJoinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(
			' LEFT OUTER JOIN `t1` ON (t1.id = t2.id)',
			$this->statement->renderJoin()
		);
	}

	public function testLeftOuterJoinUsing()
	{
		$this->statement->leftOuterJoinUsing('t1', 'user_id');
		$this->assertEquals(
			' LEFT OUTER JOIN `t1` USING (`user_id`)',
			$this->statement->renderJoin()
		);
	}

	public function testRightJoinOn()
	{
		$this->statement->rightJoinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(' RIGHT JOIN `t1` ON (t1.id = t2.id)', $this->statement->renderJoin());
	}

	public function testRightJoinUsing()
	{
		$this->statement->rightJoinUsing('t1', 'user_id');
		$this->assertEquals(' RIGHT JOIN `t1` USING (`user_id`)', $this->statement->renderJoin());
	}

	public function testRightOuterJoinOn()
	{
		$this->statement->rightOuterJoinOn('t1', function () {
			return 't1.id = t2.id';
		});
		$this->assertEquals(
			' RIGHT OUTER JOIN `t1` ON (t1.id = t2.id)',
			$this->statement->renderJoin()
		);
	}

	public function testRightOuterJoinUsing()
	{
		$this->statement->rightOuterJoinUsing('t1', 'user_id');
		$this->assertEquals(
			' RIGHT OUTER JOIN `t1` USING (`user_id`)',
			$this->statement->renderJoin()
		);
	}

	public function testNaturalJoin()
	{
		$this->statement->naturalJoin('t1');
		$this->assertEquals(' NATURAL JOIN `t1`', $this->statement->renderJoin());
	}

	public function testNaturalLeftJoin()
	{
		$this->statement->naturalLeftJoin('t1');
		$this->assertEquals(' NATURAL LEFT JOIN `t1`', $this->statement->renderJoin());
	}

	public function testNaturalLeftOuterJoin()
	{
		$this->statement->naturalLeftOuterJoin('t1');
		$this->assertEquals(' NATURAL LEFT OUTER JOIN `t1`', $this->statement->renderJoin());
	}

	public function testNaturalRightJoin()
	{
		$this->statement->naturalRightJoin('t1');
		$this->assertEquals(' NATURAL RIGHT JOIN `t1`', $this->statement->renderJoin());
	}

	public function testNaturalRightOuterJoin()
	{
		$this->statement->naturalRightOuterJoin('t1');
		$this->assertEquals(' NATURAL RIGHT OUTER JOIN `t1`', $this->statement->renderJoin());
	}
}
