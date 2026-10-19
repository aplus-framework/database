<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Aplus\CodingStandard\Config;
use Aplus\CodingStandard\Finder;

return (new Config())->setDefaultHeaderComment(
    'Aplus Framework Database Library',
    'Natan Felles <natanfelles@gmail.com>'
)->setFinder(
    Finder::create()->in(__DIR__)
);
