<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

/**
 * Class StringDataType.
 *
 * @package database
 */
abstract class StringDataType extends Column
{
    protected string $charset;
    protected string $collation;

    /**
     * @param string $charset
     *
     * @return static
     */
    public function charset(string $charset) : static
    {
        $this->charset = $charset;
        return $this;
    }

    protected function renderCharset() : ?string
    {
        if ( ! isset($this->charset)) {
            return null;
        }
        return ' CHARACTER SET ' . $this->database->quote($this->charset);
    }

    /**
     * @param string $collation
     *
     * @return static
     */
    public function collate(string $collation) : static
    {
        $this->collation = $collation;
        return $this;
    }

    protected function renderCollate() : ?string
    {
        if ( ! isset($this->collation)) {
            return null;
        }
        return ' COLLATE ' . $this->database->quote($this->collation);
    }

    protected function renderTypeAttributes() : ?string
    {
        return $this->renderCharset() . $this->renderCollate();
    }
}
