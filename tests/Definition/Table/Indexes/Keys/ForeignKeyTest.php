<?php namespace Tests\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Keys\ForeignKey;
use Tests\Database\TestCase;

final class ForeignKeyTest extends TestCase
{
	protected ForeignKey $index;

	protected function setUp() : void
	{
		$this->index = new ForeignKey(static::$database, null, 'user_id');
	}

	public function testEmptyReferences() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('REFERENCES clause was not set');
		$this->index->sql();
	}

	public function testReferences() : void
	{
		$this->index->references('users', 'id');
		$this->assertSame(
			' FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)',
			$this->index->sql()
		);
	}

	public function testOnDelete() : void
	{
		$this->index->references('users', 'id')->onDelete('restrict');
		$this->assertSame(
			' FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT',
			$this->index->sql()
		);
	}

	public function testOnUpdate() : void
	{
		$this->index->references('users', 'id')->onUpdate('cascade');
		$this->assertSame(
			' FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE',
			$this->index->sql()
		);
	}

	public function testInvalidReferenceOption() : void
	{
		$this->index->references('users', 'id')->onUpdate('foo');
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid reference option: foo');
		$this->index->sql();
	}
}
