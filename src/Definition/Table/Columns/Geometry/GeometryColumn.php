<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\Geometry;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class GeometryColumn.
 *
 * @see https://mariadb.com/kb/en/geometry/
 *
 * @package database
 */
final class GeometryColumn extends Column
{
    protected string $type = 'geometry';
}
