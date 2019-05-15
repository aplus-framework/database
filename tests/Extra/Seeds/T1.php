<?php namespace Tests\Database\Extra\Seeds;

use Framework\Database\Extra\Seeder;

class T1 extends Seeder
{
	public function run()
	{
		echo __CLASS__ . \PHP_EOL;
		$this->call(T2::class);
		$this->call(new T2($this->database));
		$this->call([
			T2::class,
			new T2($this->database),
		]);
	}
}
