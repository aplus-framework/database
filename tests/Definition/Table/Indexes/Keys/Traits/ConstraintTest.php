<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database\Definition\Table\Indexes\Keys\Traits;

use Tests\Database\TestCase;

final class ConstraintTest extends TestCase
{
	public function testConstraint() : void
	{
		$index = new ConstraintMock(static::$database, null, 'id');
		self::assertSame(
			' constraint_mock (`id`)',
			$index->sql()
		);
		$index->constraint('foo');
		self::assertSame(
			' CONSTRAINT `foo` constraint_mock (`id`)',
			$index->sql()
		);
	}
}
