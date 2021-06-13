<?php namespace Tests\Database\Definition\Table\Indexes\Keys\Traits;

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
