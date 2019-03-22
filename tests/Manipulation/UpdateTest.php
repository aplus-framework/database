<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Update;
use Tests\Database\TestCase;

class UpdateTest extends TestCase
{
	/**
	 * @var Update
	 */
	protected $update;

	public function setup()
	{
		$this->update = new Update(static::$database);
	}

	protected function prepare()
	{
		$this->update->table('t1');
	}

	public function testRenderWithoutTable()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Table references must be set');
		$this->update->sql();
	}

	public function testRenderWithoutSet()
	{
		$this->prepare();
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SET statement must be set');
		$this->update->sql();
	}

	public function testOptions()
	{
		$this->update->table('t1')->set(['id' => 1]);
		$this->update->options($this->update::OPT_LOW_PRIORITY);
		$this->assertEquals(
			"UPDATE\n LOW_PRIORITY\n `t1`\n SET `id` = 1\n",
			$this->update->sql()
		);
		$this->update->options($this->update::OPT_IGNORE);
		$this->assertEquals(
			"UPDATE\n IGNORE\n `t1`\n SET `id` = 1\n",
			$this->update->sql()
		);
		$this->update->options($this->update::OPT_LOW_PRIORITY, $this->update::OPT_IGNORE);
		$this->assertEquals(
			"UPDATE\n LOW_PRIORITY IGNORE\n `t1`\n SET `id` = 1\n",
			$this->update->sql()
		);
	}

	public function testInvalidOption()
	{
		$this->prepare();
		$this->update->set(['id' => 1]);
		$this->update->options('foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid option: foo');
		$this->update->sql();
	}

	public function testLimit()
	{
		$this->update->table('t1')->set(['id' => 1]);
		$this->update->limit(1);
		$this->assertEquals(
			"UPDATE\n `t1`\n SET `id` = 1\n LIMIT 1\n",
			$this->update->sql()
		);
		$this->update->limit('235');
		$this->assertEquals(
			"UPDATE\n `t1`\n SET `id` = 1\n LIMIT 235\n",
			$this->update->sql()
		);
	}

	public function testWhere()
	{
		$this->update->table('t1')->set(['name' => 'Foo']);
		$this->update->whereEqual('id', 1);
		$this->assertEquals(
			"UPDATE\n `t1`\n SET `name` = 'Foo'\n WHERE `id` = 1\n",
			$this->update->sql()
		);
	}

	public function testOrderBy()
	{
		$this->update->table('t1')->set(['name' => 'Foo']);
		$this->update->orderByAsc('id');
		$this->assertEquals(
			"UPDATE\n `t1`\n SET `name` = 'Foo'\n ORDER BY `id` ASC\n",
			$this->update->sql()
		);
	}

	public function testRun()
	{
		$this->createDummyData();
		$this->assertEquals(
			3,
			$this->update->table('t1')->set(['c2' => 'x'])->whereIn('c1', 1, 2, 3)->run()
		);
	}
}
