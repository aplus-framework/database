<?php declare(strict_types=1);
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\Numeric;

final class TinyintColumn extends NumericDataType
{
	protected string $type = 'tinyint';
	protected int $maxLength = 127;
}
