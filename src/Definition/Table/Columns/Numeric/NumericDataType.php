<?php namespace Framework\Database\Definition\Table\Columns\Numeric;

use Framework\Database\Definition\Table\Columns\Column;

abstract class NumericDataType extends Column
{
	protected $signed;
	protected $unsigned;
	protected $zerofill;
	protected $autoIncrement;

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
		if ( ! isset($this->autoIncrement)) {
			return null;
		}
		return ' AUTO_INCREMENT';
	}

	public function signed()
	{
		$this->signed = true;
		return $this;
	}

	protected function renderSigned() : ?string
	{
		if ( ! isset($this->signed)) {
			return null;
		}
		return ' signed';
	}

	public function unsigned()
	{
		$this->unsigned = true;
		return $this;
	}

	protected function renderUnsigned() : ?string
	{
		if ( ! isset($this->unsigned)) {
			return null;
		}
		return ' unsigned';
	}

	public function zerofill()
	{
		$this->zerofill = true;
		return $this;
	}

	protected function renderZerofill() : ?string
	{
		if ( ! isset($this->zerofill)) {
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
