<?php declare(strict_types=1);
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Indexes\Index;

/**
 * Class UniqueKey.
 *
 * @see https://mariadb.com/kb/en/library/getting-started-with-indexes/#unique-index
 */
final class UniqueKey extends Index
{
    use Traits\Constraint;
    protected string $type = 'UNIQUE KEY';
}
