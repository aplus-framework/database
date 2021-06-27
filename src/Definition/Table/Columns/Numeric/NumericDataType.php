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

use Framework\Database\Definition\Table\Columns\Column;

abstract class NumericDataType extends Column
{
	protected bool $signed = false;
	protected bool $unsigned = false;
	protected bool $zerofill = false;
	protected bool $autoIncrement = false;

	/**
	 * @see https://mariadb.com/kb/en/library/auto_increment/
	 *
	 * @return $this
	 */
	public function autoIncrement()
	{
		$this->autoIncrement = true;
		return $this;
	}

	protected function renderAutoIncrement() : ?string
	{
		if ( ! $this->autoIncrement) {
			return null;
		}
		return ' AUTO_INCREMENT';
	}

	/**
	 * @return $this
	 */
	public function signed()
	{
		$this->signed = true;
		return $this;
	}

	protected function renderSigned() : ?string
	{
		if ( ! $this->signed) {
			return null;
		}
		return ' signed';
	}

	/**
	 * @return $this
	 */
	public function unsigned()
	{
		$this->unsigned = true;
		return $this;
	}

	protected function renderUnsigned() : ?string
	{
		if ( ! $this->unsigned) {
			return null;
		}
		return ' unsigned';
	}

	/**
	 * @return $this
	 */
	public function zerofill()
	{
		$this->zerofill = true;
		return $this;
	}

	protected function renderZerofill() : ?string
	{
		if ( ! $this->zerofill) {
			return null;
		}
		return ' zerofill';
	}

	protected function renderTypeAttributes() : ?string
	{
		return $this->renderSigned()
			. $this->renderUnsigned()
			. $this->renderZerofill()
			. $this->renderAutoIncrement();
	}
}
