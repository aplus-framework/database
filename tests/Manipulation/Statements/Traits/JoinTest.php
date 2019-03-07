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
			' FROM `t1`, `t2`, `t3` AS `aliasname`',
			$this->statement->renderFrom()
		);
		$this->statement->from(function () {
			return 'NOW()';
		});
		$this->assertEquals(
			' FROM `t1`, `t2`, `t3` AS `aliasname`, (NOW())',
			$this->statement->renderFrom()
		);
		$this->statement->from([
			'time' => function () {
				return 'SELECT NOW()';
			},
		], ['noindex']);
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
}
