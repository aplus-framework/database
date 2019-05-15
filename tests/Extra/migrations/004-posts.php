<?php

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Extra\Migration;

class PostsMigration extends Migration
{
	public function up()
	{
		$this->database->createTable()
			->table('Posts')
			->definition(static function (TableDefinition $definition) {
				$definition->column('id')->int()->primaryKey();
				$definition->column('title')->varchar(255);
			})->run();
	}

	public function down()
	{
		$this->database->dropTable()->table('Posts')->ifExists()->run();
	}
}
