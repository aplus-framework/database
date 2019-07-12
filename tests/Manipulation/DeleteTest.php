<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Delete;
use Tests\Database\TestCase;

class DeleteTest extends TestCase
{
	/**
	 * @var Delete
	 */
	protected $delete;

	public function setup() : void
	{
		$this->delete = new Delete(static::$database);
	}

	public function testOptions()
	{
		$this->delete->from('t1');
		$this->delete->options($this->delete::OPT_LOW_PRIORITY);
		$this->assertEquals(
			"DELETE\n LOW_PRIORITY\n FROM `t1`\n",
			$this->delete->sql()
		);
		$this->delete->options($this->delete::OPT_IGNORE);
		$this->assertEquals(
			"DELETE\n IGNORE\n FROM `t1`\n",
			$this->delete->sql()
		);
		$this->delete->options($this->delete::OPT_LOW_PRIORITY, $this->delete::OPT_IGNORE);
		$this->assertEquals(
			"DELETE\n LOW_PRIORITY IGNORE\n FROM `t1`\n",
			$this->delete->sql()
		);
	}

	public function testInvalidOption()
	{
		$this->delete->from('t1');
		$this->delete->options('foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid option: foo');
		$this->delete->sql();
	}

	public function testLimit()
	{
		$this->delete->from('t1');
		$this->delete->limit(1);
		$this->assertEquals(
			"DELETE\n FROM `t1`\n LIMIT 1\n",
			$this->delete->sql()
		);
		$this->delete->limit('235');
		$this->assertEquals(
			"DELETE\n FROM `t1`\n LIMIT 235\n",
			$this->delete->sql()
		);
	}

	public function testWhere()
	{
		$this->delete->from('t1');
		$this->delete->whereEqual('id', 1);
		$this->assertEquals(
			"DELETE\n FROM `t1`\n WHERE `id` = 1\n",
			$this->delete->sql()
		);
	}

	public function testOrderBy()
	{
		$this->delete->from('t1');
		$this->delete->orderByAsc('id');
		$this->assertEquals(
			"DELETE\n FROM `t1`\n ORDER BY `id` ASC\n",
			$this->delete->sql()
		);
	}

	public function testJoin()
	{
		$this->delete->table('t1', 't2')
			->from('t1')
			->innerJoinOn('t2', function () {
				return 't2.ref = t1.id';
			});
		$this->assertEquals(
			"DELETE\n `t1`, `t2`\n FROM `t1`\n INNER JOIN `t2` ON (t2.ref = t1.id)\n",
			$this->delete->sql()
		);
	}

	public function testRun()
	{
		$this->createDummyData();
		$this->assertEquals(
			3,
			$this->delete->from('t1')->whereIn('c1', 1, 2, 3)->run()
		);
	}
}
