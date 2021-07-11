<?php
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Database;

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
