<?php namespace Tests\Database\Extra;

use Framework\Database\Extra\Migrator;
use Tests\Database\TestCase;

class MigratorTest extends TestCase
{
	/**
	 * @var Migrator
	 */
	protected $migrator;

	public function setup() : void
	{
		$this->migrator = new Migrator(static::$database);
		$this->migrator->addFiles([
			__DIR__ . '/migrations/001-users.php',
			__DIR__ . '/migrations/2-foo.php',
			__DIR__ . '/migrations/003-bar.php',
			__DIR__ . '/migrations/004-posts.php',
		]);
	}

	protected function tearDown() : void
	{
		static::$database->dropTable()
			->table($this->migrator->getMigrationTable())
			->ifExists()
			->run();
		static::$database->dropTable()
			->table('Posts')
			->ifExists()
			->run();
		static::$database->dropTable()
			->table('Users')
			->ifExists()
			->run();
	}

	public function testCurrentVersion()
	{
		$this->assertEquals('', $this->migrator->getCurrentVersion());
	}

	protected function migrateTo(string $version)
	{
		foreach ($this->migrator->migrateTo($version) as $item) {
		}
	}

	public function testMigrateTo()
	{
		$this->assertEquals('', $this->migrator->getCurrentVersion());
		$this->migrateTo('001');
		$this->assertEquals('001', $this->migrator->getCurrentVersion());
		$this->migrateTo('004');
		$this->assertEquals('004', $this->migrator->getCurrentVersion());
		$this->migrateTo('004');
		$this->assertEquals('004', $this->migrator->getCurrentVersion());
		$this->migrateTo('001');
		$this->assertEquals('001', $this->migrator->getCurrentVersion());
		$this->migrateTo('');
		$this->assertEquals('', $this->migrator->getCurrentVersion());
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Migration version not found: 005');
		$this->migrateTo('005');
	}

	public function testMigrateUpAndDown()
	{
		$this->assertCount(0, $this->migrator->getVersions());
		$versions = [];
		foreach ($this->migrator->migrateUp() as $version) {
			$versions[] = $version;
		}
		$this->assertEquals(['001', '004'], $versions);
		$this->assertEquals('004', $this->migrator->getCurrentVersion());
		$this->assertCount(2, $this->migrator->getVersions());
		$versions = [];
		foreach ($this->migrator->migrateDown() as $version) {
			$versions[] = $version;
		}
		$this->assertEquals(['004', '001'], $versions);
		$this->assertEquals('', $this->migrator->getCurrentVersion());
		$this->assertCount(0, $this->migrator->getVersions());
	}

	public function testPrepare()
	{
		$migrator = new Migrator(static::$database);
		$migrator->addFiles($this->migrator->getFiles());
		$migrator->setMigrationTable($this->migrator->getMigrationTable());
		$this->assertCount(0, $this->migrator->getVersions());
	}
}
