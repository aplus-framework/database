<?php namespace Tests\Database\Manipulation\Statements;

use Framework\Database\Manipulation\Statements\Insert;
use Framework\Database\Manipulation\Statements\Select;
use PHPUnit\Framework\TestCase;
use Tests\Database\Manipulation\ManipulationMock;

class InsertTest extends TestCase
{
	/**
	 * @var Insert
	 */
	protected $insert;

	public function setup()
	{
		$this->insert = new Insert(new ManipulationMock());
	}

	protected function prepare()
	{
		$this->insert->into('t1');
		return $this->insert->sql();
	}

	public function testInto()
	{
		$this->insert->into('t1');
		$this->assertEquals("INSERT\n INTO `t1`\n", $this->insert->sql());
	}

	public function testRenderWithoutInto()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('INTO table must be set');
		$this->insert->sql();
	}

	public function testOptions()
	{
		$this->insert->into('t1');
		$this->insert->options($this->insert::OPT_DELAYED);
		$this->assertEquals(
			"INSERT\n DELAYED\n INTO `t1`\n",
			$this->insert->sql()
		);
		$this->insert->options($this->insert::OPT_IGNORE);
		$this->assertEquals(
			"INSERT\n DELAYED IGNORE\n INTO `t1`\n",
			$this->insert->sql()
		);
	}

	public function testInvalidOption()
	{
		$this->prepare();
		$this->insert->options('foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid option: foo');
		$this->insert->sql();
	}

	public function testOptionsConflict()
	{
		$this->prepare();
		$this->insert->options($this->insert::OPT_DELAYED, $this->insert::OPT_LOW_PRIORITY);
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage(
			'Options LOW_PRIORITY, DELAYED or HIGH_PRIORITY can not be used together'
		);
		$this->insert->sql();
	}

	public function testColumns()
	{
		$this->prepare();
		$this->insert->columns('id', 'name', 'email');
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n",
			$this->insert->sql()
		);
		$this->insert->columns('ids');
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`ids`)\n",
			$this->insert->sql()
		);
	}

	public function testValue()
	{
		$this->prepare();
		$this->insert->columns('id', 'name', 'email');
		$this->insert->value(1, 'Foo', 'foo@baz.com');
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com')\n",
			$this->insert->sql()
		);
		$this->insert->value(2, 'Bar', 'bar@baz.com');
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (2, 'Bar', 'bar@baz.com')\n",
			$this->insert->sql()
		);
	}

	public function testValues()
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
		$this->insert->values(10, 'Baz', function () {
			return 'select email from foo';
		});
		$this->assertEquals(
			"INSERT\n INTO `t1`\n (`id`, `name`, `email`)\n VALUES (1, 'Foo', 'foo@baz.com'),\n (2, 'Bar', 'bar@baz.com'),\n (10, 'Baz', (select email from foo))\n",
			$this->insert->sql()
		);
	}

	public function testSelect()
	{
		$this->prepare();
		$this->insert->select(function (Select $select) {
			return $select->columns('*')->from('t2');
		});
		$this->assertEquals(
			"INSERT\n INTO `t1`\n SELECT\n*\n FROM `t2`\n\n",
			$this->insert->sql()
		);
	}

	public function testOnDuplicateKeyUpdate()
	{
		$this->prepare();
		$this->insert->onDuplicateKeyUpdate(
			['id' => null],
			['name' => 'Foo'],
			[
				'other' => function () {
					return "CONCAT('Foo', 'id')";
				},
			]
		);
		$this->assertEquals(
			"INSERT\n INTO `t1`\n ON DUPLICATE KEY UPDATE `id` = NULL, `name` = 'Foo', `other` = (CONCAT('Foo', 'id'))\n",
			$this->insert->sql()
		);
	}
}
