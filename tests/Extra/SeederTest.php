<?php namespace Tests\Database\Extra;

use Tests\Database\Extra\Seeds\T1;
use Tests\Database\Extra\Seeds\T2;
use Tests\Database\TestCase;

class SeederTest extends TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRunCall()
	{
		\ob_start();
		(new T1(static::$database))->run();
		$flush = \ob_get_clean();
		$this->assertEquals(
			T1::class . \PHP_EOL
			. T2::class . \PHP_EOL
			. T2::class . \PHP_EOL
			. T2::class . \PHP_EOL
			. T2::class . \PHP_EOL,
			$flush
		);
	}
}
