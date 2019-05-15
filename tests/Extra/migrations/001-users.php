<?php

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Extra\Migration;

class UsersMigration extends Migration
{
	public function up()
	{
		$this->database->createTable()
			->table('Users')
			->definition(static function (TableDefinition $definition) {
				$definition->column('id')->int()->primaryKey();
				$definition->column('name')->varchar(32);
			})->run();
	}

	public function down()
	{
		$this->database->dropTable()->table('Users')->ifExists()->run();
	}
}
