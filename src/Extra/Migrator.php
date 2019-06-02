<?php namespace Framework\Database\Extra;

use Framework\Autoload\Autoloader;
use Framework\Autoload\Locator;
use Framework\Database\Database;
use Framework\Database\Definition\Table\TableDefinition;

/**
 * Class Migrator.
 */
class Migrator
{
	/**
	 * Migrations Table name.
	 *
	 * @var string
	 */
	protected $migrationTable = 'Migrations';
	/**
	 * Added files.
	 *
	 * @var array
	 */
	protected $files = [];
	/**
	 * @var Database
	 */
	protected $database;
	/**
	 * @var Locator
	 */
	protected $locator;

	/**
	 * Migrator constructor.
	 *
	 * @param Database     $database
	 * @param Locator|null $locator
	 */
	public function __construct(Database $database, Locator $locator = null)
	{
		$this->database = $database;
		$this->locator = $locator ?: new Locator(new Autoloader());
		$this->prepare();
	}

	/**
	 * Add migrations files.
	 *
	 * @param array $filenames
	 *
	 * @return $this
	 */
	public function addFiles(array $filenames)
	{
		foreach ($filenames as $filename) {
			$this->files[$this->getFileVersion($filename)] = $filename;
		}
		\ksort($this->files);
		return $this;
	}

	/**
	 * Get Migration files.
	 *
	 * @return array
	 */
	public function getFiles() : array
	{
		return $this->files;
	}

	public function setMigrationTable(string $table)
	{
		$this->migrationTable = $table;
		return $this;
	}

	public function getMigrationTable() : string
	{
		return $this->migrationTable;
	}

	protected function getFileVersion(string $file) : string
	{
		return $this->getFileParts($file)[0];
	}

	private function getFileParts(string $file) : array
	{
		$file = \substr($file, \strrpos($file, \DIRECTORY_SEPARATOR) + 1);
		return \explode('-', $file, 2);
	}

	protected function getFileName(string $file) : string
	{
		return \substr($this->getFileParts($file)[1], 0, -4);
	}

	protected function prepare()
	{
		$exists = $this->database->query(
			'SHOW TABLES LIKE ' . $this->database->quote($this->getMigrationTable())
		)->fetch();
		if ($exists) {
			return;
		}
		$this->database->createTable()
			->table($this->getMigrationTable())
			->definition(static function (TableDefinition $definition) {
				$definition->column('version')->varchar(32)->primaryKey();
				$definition->column('name')->varchar(255)->notNull();
				$definition->column('migratedAt')->datetime()->notNull();
			})->run();
	}

	/**
	 * Get current migrated version from Database.
	 *
	 * @return string
	 */
	public function getCurrentVersion() : string
	{
		return $this->database->select()
			->columns('version')
			->from($this->getMigrationTable())
			->orderByDesc(static function () {
				return 'CAST(`version` AS SIGNED INTEGER)';
			})
			->orderByAsc('name')
			->limit(1)
			->run()
			->fetch()->version ?? '';
	}

	/**
	 * Get Migrations list from Database.
	 *
	 * @return array
	 */
	public function getVersions() : array
	{
		return $this->database->select()
			->from($this->getMigrationTable())
			->orderByAsc(static function () {
				return 'CAST(`version` AS SIGNED INTEGER)';
			})
			->orderByAsc('name')
			->run()
			->fetchAll();
	}

	/**
	 * Migrate down all Migration files.
	 *
	 * @return \Generator
	 */
	public function migrateDown() : \Generator
	{
		yield from $this->migrateTo('');
	}

	/**
	 * Migrate up all Migration files.
	 *
	 * @return \Generator
	 */
	public function migrateUp() : \Generator
	{
		yield from $this->migrateTo(\array_key_last($this->getFiles()));
	}

	/**
	 * Migrate to specific version.
	 *
	 * @param string $version
	 *
	 * @throws \InvalidArgumentException if migration version is not found
	 *
	 * @return \Generator
	 */
	public function migrateTo(string $version) : \Generator
	{
		$current_version = $this->getCurrentVersion();
		if ($version === $current_version) {
			return;
		}
		if ($version !== '' && ! isset($this->getFiles()[$version])) {
			throw new \InvalidArgumentException("Migration version not found: {$version}");
		}
		$direction = 'up';
		if ($version < $current_version) {
			$direction = 'down';
			$this->database->delete()
				->from($this->getMigrationTable())
				->whereGreaterThan('version', $version)
				->run();
		}
		$files = $direction === 'up'
			? $this->getRangeUp($current_version, $version)
			: $this->getRangeDown($current_version, $version);
		yield from $this->migrate($files, $direction);
	}

	protected function getRangeDown(string $current, string $target) : array
	{
		$files = [];
		foreach ($this->getFiles() as $version => $file) {
			if ($version <= $current && $version > $target) {
				$files[$version] = $file;
			}
		}
		\krsort($files);
		return $files;
	}

	protected function getRangeUp(string $current, string $target) : array
	{
		$files = [];
		foreach ($this->getFiles() as $version => $file) {
			if ($version > $current && $version <= $target) {
				$files[$version] = $file;
			}
		}
		return $files;
	}

	protected function migrate(array $files, string $direction) : \Generator
	{
		foreach ($files as $version => $file) {
			$className = $this->locator->getClassName($file);
			if ($className === false) {
				continue;
			}
			require_once $file;
			$class = new \ReflectionClass($className);
			if ( ! $class->isInstantiable() || ! $class->isSubclassOf(Migration::class)) {
				continue;
			}
			(new $className($this->database))->{$direction}();
			if ($direction === 'up') {
				$this->database->insert()
					->into($this->getMigrationTable())
					->set([
						'version' => $version,
						'name' => $this->getFileName($file),
						'migratedAt' => \gmdate('Y-m-d H:i:s'),
					])->run();
			}
			yield $version;
		}
	}
}
