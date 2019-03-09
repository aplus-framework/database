<?php namespace Tests\Database\Manipulation\Statements;

use Framework\Database\Manipulation\Statements\Select;
use PHPUnit\Framework\TestCase;
use Tests\Database\Manipulation\ManipulationMock;

class SelectTest extends TestCase
{
	/**
	 * @var Select
	 */
	protected $select;

	public function setup()
	{
		$this->select = new Select(new ManipulationMock());
	}

	protected function selectAllFrom(...$from) : string
	{
		return $this->select->columns('*')->from(...$from)->sql();
	}

	protected function renderSelectAllFrom() : string
	{
		return $this->select->sql();
	}

	public function testOptions()
	{
		$this->select->options($this->select::OPT_ALL);
		$this->assertEquals(
			"SELECT\nALL\n",
			$this->select->sql()
		);
		$this->select->options($this->select::OPT_HIGH_PRIORITY);
		$this->assertEquals(
			"SELECT\nALL HIGH_PRIORITY\n",
			$this->select->sql()
		);
	}

	public function testExpressions()
	{
		$this->select->expressions('1');
		$this->assertEquals("SELECT\n`1`\n", $this->select->sql());
		$this->select->expressions(function () {
			return 'now()';
		});
		$this->assertEquals("SELECT\n`1`, (now())\n", $this->select->sql());
	}

	public function testColumns()
	{
		$this->select->columns('1');
		$this->assertEquals("SELECT\n`1`\n", $this->select->sql());
		$this->select->columns(function () {
			return 'now()';
		});
		$this->assertEquals("SELECT\n`1`, (now())\n", $this->select->sql());
	}

	public function testLimit()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " LIMIT 10\n",
			$this->select->limit(10)->sql()
		);
		$this->assertEquals(
			$part . " LIMIT 10 OFFSET 20\n",
			$this->select->limit(10, 20)->sql()
		);
	}

	public function testProcedure()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " PROCEDURE count_foo()\n",
			$this->select->procedure('count_foo')->sql()
		);
		$this->assertEquals(
			$part . " PROCEDURE count_bar('a', 1)\n",
			$this->select->procedure('count_bar', 'a', 1)->sql()
		);
	}

	public function testIntoOutfile()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " INTO OUTFILE '/tmp/foo-bar'\n",
			$this->select->intoOutfile('/tmp/foo-bar')->sql()
		);
		$this->assertEquals(
			$part . " INTO OUTFILE '/tmp/foo-bar' CHARACTER SET 'utf8'\n",
			$this->select->intoOutfile('/tmp/foo-bar', 'utf8')->sql()
		);
		$this->assertEquals(
			$part . " INTO OUTFILE '/tmp/foo-bar' CHARACTER SET 'utf8' FIELDS ENCLOSED BY '\\''\n",
			$this->select->intoOutfile(
				'/tmp/foo-bar',
				'utf8',
				[
					$this->select::EXP_FIELDS_ENCLOSED_BY => "'",
				]
			)->sql()
		);
		$this->assertEquals(
			$part . " INTO OUTFILE '/tmp/foo-bar' LINES TERMINATED BY ' '\n",
			$this->select->intoOutfile(
				'/tmp/foo-bar',
				null,
				[],
				[
					$this->select::EXP_LINES_TERMINATED_BY => ' ',
				]
			)->sql()
		);
	}

	public function testIntoDumpfile()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " INTO DUMPFILE '/tmp/foo-bar'\n",
			$this->select->intoDumpfile('/tmp/foo-bar')->sql()
		);
		$this->assertEquals(
			$part . " INTO DUMPFILE '/tmp/foo-bar' INTO @var1, @Var2\n",
			$this->select->intoDumpfile('/tmp/foo-bar', 'var1', 'Var2')->sql()
		);
	}

	public function testLockForUpdate()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " FOR UPDATE\n",
			$this->select->lockForUpdate()->sql()
		);
		$this->assertEquals(
			$part . " FOR UPDATE WAIT 120\n",
			$this->select->lockForUpdate(120)->sql()
		);
	}

	public function testLockInShareMode()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " LOCK IN SHARE MODE\n",
			$this->select->lockInShareMode()->sql()
		);
		$this->assertEquals(
			$part . " LOCK IN SHARE MODE WAIT 1\n",
			$this->select->lockInShareMode(1)->sql()
		);
	}

	public function testJoin()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " JOIN `t2` USING (`user_id`)\n",
			$this->select->joinUsing('t2', 'user_id')->sql()
		);
	}

	public function testWhere()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " WHERE `id` = 10\n",
			$this->select->whereEqual('id', 10)->sql()
		);
	}

	public function testHaving()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " HAVING `id` = 10\n",
			$this->select->havingEqual('id', 10)->sql()
		);
	}

	public function testOrderBy()
	{
		$part = $this->selectAllFrom('t1');
		$this->assertEquals(
			$part . " ORDER BY `name` ASC, `id`\n",
			$this->select->orderByAsc('name')->orderBy('id')->sql()
		);
	}
}
