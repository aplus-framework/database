<?php namespace Tests\Database\Manipulation;

use Framework\Database\Manipulation\Insert;
use Framework\Database\Manipulation\Select;
use Tests\Database\TestCase;

class InsertTest extends TestCase
{
	protected Insert $insert;

	public function setup() : void
	{
		$this->insert = new Insert(static::$database);
	}

	protected function prepare() : void
	{
		$this->insert->into('t1');
	}

	public function testIntoOnly() : void
	{
		$this->insert->into('t1');
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'The INSERT INTO must be followed by VALUES, SET or SELECT statement'
		);
		$this->insert->sql();
	}

	public function testRenderWithoutInto() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('INTO table must be set');
		$this->insert->sql();
	}

	public function testOptions() : void
	{
		$this->insert->into('t1')->set(['id' => 1]);
		$this->insert->options($this->insert::OPT_DELAYED);
		$this->assertEquals(
			"INSERT\n DELAYED\n INTO `t1`\n SET `id` = 1\n",
			$this->insert->sql()
		);
		$this->insert->options($this->insert::OPT_IGNORE);
		$this->assertEquals(
			"INSERT\n IGNORE\n INTO `t1`\n SET `id` = 1\n",
			$this->insert->sql()
		);
		$this->insert->options($this->insert::OPT_DELAYED, $this->insert::OPT_IGNORE);
		$this->assertEquals(
			"INSERT\n DELAYED IGNORE\n INTO `t1`\n SET `id` = 1\n",
			$this->insert->sql()
		);
	}

	public function testInvalidOption() : void
	{
		$this->prepare();
		$this->insert->set(['id' => 1]);
		$this->insert->options('foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid option: foo');
		$this->insert->sql();
	}

	public function testOptionsConflict() : void
	{
		$this->prepare();
		$this->insert->options($this->insert::OPT_DELAYED, $this->insert::OPT_LOW_PRIORITY);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Options LOW_PRIORITY, DELAYED or HIGH_PRIORITY can not be used together'
		);
		$this->insert->sql();
	}

	public function testValues() : void
	{
		$this->prepare();
		$this->insert->columns('id', 'name', 'email');
		$this->insert->values(1, 'Foo', 'foo@baz.com');
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com')\n",
			$this->insert->sql()
		);
		$this->insert->values(2, 'Bar', 'bar@baz.com');
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com')\n",
			$this->insert->sql()
		);
		$this->insert->values(10, 'Baz', static function () {
			return 'select email from foo';
		});
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com'),\n (10, 'Baz', (select email from foo))\n",
			$this->insert->sql()
		);
	}

	public function testSet() : void
	{
		$this->prepare();
		$this->insert->set([
			'id' => 1,
			'name' => 'Foo',
			'other' => static function () {
				return "CONCAT('Foo', ' ', 1)";
			},
		]);
		$this->assertEquals(
			"INSERT\n INTO `t1`\n SET `id` = 1, `name` = 'Foo', `other` = (CONCAT('Foo', ' ', 1))\n",
			$this->insert->sql()
		);
	}

	public function testSetWithColumns() : void
	{
		$this->prepare();
		$this->insert->columns('id');
		$this->insert->set(['id' => 1]);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SET statement is not allowed when columns are set');
		$this->insert->sql();
	}

	public function testSetWithValues() : void
	{
		$this->prepare();
		$this->insert->values('id');
		$this->insert->set(['id' => 1]);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SET statement is not allowed when VALUES is set');
		$this->insert->sql();
	}

	public function testSelect() : void
	{
		$this->prepare();
		$this->insert->select(static function (Select $select) {
			return $select->columns('*')->from('t2');
		});
		$this->assertEquals(
			"INSERT\n INTO `t1`\n SELECT\n *\n FROM `t2`\n\n",
			$this->insert->sql()
		);
	}

	public function testSelectWithValues() : void
	{
		$this->prepare();
		$this->insert->values('id');
		$this->insert->select(static function (Select $select) {
			return $select->columns('*')->from('t2');
		});
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SELECT statement is not allowed when VALUES is set');
		$this->insert->sql();
	}

	public function testSelectWithSet() : void
	{
		$this->prepare();
		$this->insert->set(['id' => 1]);
		$this->insert->select(static function (Select $select) {
			return $select->columns('*')->from('t2');
		});
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('SELECT statement is not allowed when SET is set');
		$this->insert->sql();
	}

	public function testOnDuplicateKeyUpdate() : void
	{
		$this->prepare();
		$this->insert->set(['id' => 1]);
		$this->insert->onDuplicateKeyUpdate([
			'id' => null,
			'name' => 'Foo',
			'other' => static function () {
				return "CONCAT('Foo', 'id')";
			},
		]);
		$this->assertEquals(
			"INSERT\n INTO `t1`\n SET `id` = 1\n ON DUPLICATE KEY UPDATE `id` = NULL, `name` = 'Foo', `other` = (CONCAT('Foo', 'id'))\n",
			$this->insert->sql()
		);
	}

	public function testRun() : void
	{
		$this->createDummyData();
		$this->prepare();
		$this->assertEquals(
			1,
			$this->insert->set(['c2' => 'foo'])->run()
		);
	}
}
