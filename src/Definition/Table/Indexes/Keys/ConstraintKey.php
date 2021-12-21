<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Indexes\Keys;

use Framework\Database\Definition\Table\Constraint;
use Framework\Database\Definition\Table\Indexes\Index;

/**
 * Class ConstraintKey.
 *
 * @package database
 */
abstract class ConstraintKey extends Index
{
    use Constraint;

    protected function renderType() : string
    {
        return $this->renderConstraint() . parent::renderType();
    }
}
