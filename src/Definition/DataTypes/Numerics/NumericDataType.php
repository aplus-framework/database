<?php namespace Framework\Database\Definition\DataTypes\Numerics;

use Framework\Database\Definition\DataTypes\Column;

abstract class NumericDataType extends Column
{
	protected $signed;
	protected $unsigned;
	protected $zerofill;
	protected $length;
	protected $autoIncrement;
	/*public function length($length)
	{
		$this->length = $length;
		return $this;
	}*/

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
		return ' SIGNED';
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
		return ' UNSIGNED';
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
		return ' ZEROFILL';
	}

	protected function sql() : string
	{
		$sql = $this->renderName();
		$sql .= $this->renderType();
		$sql .= $this->renderLength();
		$sql .= $this->renderSigned();
		$sql .= $this->renderUnsigned();
		$sql .= $this->renderZerofill();
		$sql .= $this->renderNull();
		$sql .= $this->renderAutoIncrement();
		$sql .= $this->renderDefault();
		$sql .= $this->renderComment();
		$sql .= $this->renderUniqueKey();
		$sql .= $this->renderPrimaryKey();
		return $sql;
	}
}
