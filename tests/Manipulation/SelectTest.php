<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Select;
use Framework\Database\Result;
use Tests\Database\TestCase;

class SelectTest extends TestCase
{
	protected Select $select;

	public function setup() : void
	{
		$this->select = new Select(static::$database);
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
			"SELECT\nHIGH_PRIORITY\n",
			$this->select->sql()
		);
		$this->select->options($this->select::OPT_ALL, $this->select::OPT_HIGH_PRIORITY);
		$this->assertEquals(
			"SELECT\nALL HIGH_PRIORITY\n",
			$this->select->sql()
		);
	}

	public function testInvalidOption()
	{
		$this->select->options('al');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid option: al');
		$this->select->sql();
	}

	public function testIOptionsConflictAllAndDistinct()
	{
		$this->select->options($this->select::OPT_ALL, $this->select::OPT_DISTINCT);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Options ALL and DISTINCT can not be used together');
		$this->select->sql();
	}

	public function testIOptionsConflictSqlCacheAndSqlNoCache()
	{
		$this->select->options($this->select::OPT_SQL_CACHE, $this->select::OPT_SQL_NO_CACHE);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Options SQL_CACHE and SQL_NO_CACHE can not be used together');
		$this->select->sql();
	}

	public function testExpressions()
	{
		$this->select->expressions('1');
		$this->assertEquals("SELECT\n `1`\n", $this->select->sql());
		$this->select->expressions(static function () {
			return 'now()';
		});
		$this->assertEquals("SELECT\n `1`, (now())\n", $this->select->sql());
	}

	public function testColumns()
	{
		$this->select->columns('1');
		$this->assertEquals("SELECT\n `1`\n", $this->select->sql());
		$this->select->columns(static function () {
			return 'now()';
		});
		$this->assertEquals("SELECT\n `1`, (now())\n", $this->select->sql());
	}

	public function testEmptyExpressionsWithFrom()
	{
		$this->select->from('Users');
		$this->assertEquals("SELECT\n *\n FROM `Users`\n", $this->select->sql());
		$this->select->columns('id', 'name');
		$this->assertEquals("SELECT\n `id`, `name`\n FROM `Users`\n", $this->select->sql());
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

	public function testIntoOutfileFileExists()
	{
		$this->selectAllFrom('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('INTO OUTFILE filename must not exist: ' . __FILE__);
		$this->select->intoOutfile(__FILE__)->sql();
	}

	public function testInvalidIntoOutfileFieldOption()
	{
		$this->selectAllFrom('t1');
		$this->select->intoOutfile('/tmp/foo-bar', null, ['foo' => 'bar']);
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid INTO OUTFILE fields option: foo');
		$this->select->sql();
	}

	public function testInvalidIntoOutfileLineOption()
	{
		$this->selectAllFrom('t1');
		$this->select->intoOutfile('/tmp/foo-bar', null, [], ['foo' => 'bar']);
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid INTO OUTFILE lines option: foo');
		$this->select->sql();
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

	public function testIntoDumpfileFileExists()
	{
		$this->selectAllFrom('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('INTO DUMPFILE filepath must not exist: ' . __FILE__);
		$this->select->intoDumpfile(__FILE__)->sql();
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

	public function testInvalidLockForUpdateWait()
	{
		$this->selectAllFrom('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Invalid FOR UPDATE WAIT value: -1');
		$this->select->lockForUpdate(-1)->sql();
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

	public function testInvalidLockInShareModeWait()
	{
		$this->selectAllFrom('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Invalid LOCK IN SHARE MODE WAIT value: -10');
		$this->select->lockInShareMode(-10)->sql();
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

	public function testRun()
	{
		$this->createDummyData();
		$this->selectAllFrom('t1');
		$this->select->limit(1);
		$this->assertInstanceOf(Result::class, $this->select->run());
	}
}
