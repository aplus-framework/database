<?php namespace Tests\Database;

class ResultEntity
{
	public mixed $p1;
	public mixed $p2;

	public function __construct(mixed $p1, mixed $p2)
	{
		$this->p1 = $p1;
		$this->p2 = $p2;
	}
}
