<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Columns\String;

use Tests\Database\TestCase;

final class StringDataTypeTest extends TestCase
{
	protected StringDataTypeMock $column;

	protected function setUp() : void
	{
		$this->column = new StringDataTypeMock(static::$database);
	}

	public function testCharset() : void
	{
		self::assertSame(
			" mock CHARACTER SET 'utf8' NOT NULL",
			$this->column->charset('utf8')->sql()
		);
	}

	public function testCollate() : void
	{
		self::assertSame(
			" mock COLLATE 'utf8_general_ci' NOT NULL",
			$this->column->collate('utf8_general_ci')->sql()
		);
	}

	public function testFull() : void
	{
		self::assertSame(
			" mock CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL",
			$this->column->collate('utf8_general_ci')->charset('utf8')->sql()
		);
	}
}
