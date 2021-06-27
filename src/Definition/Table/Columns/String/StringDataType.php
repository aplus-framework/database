<?php declare(strict_types=1);
/*
 * This file is part of The Framework Database Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Database\Definition\Table\Columns\String;

use Framework\Database\Definition\Table\Columns\Column;

abstract class StringDataType extends Column
{
	protected string $charset;
	protected string $collation;

	/**
	 * @param string $charset
	 *
	 * @return $this
	 */
	public function charset(string $charset)
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
	 * @return $this
	 */
	public function collate(string $collation)
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
