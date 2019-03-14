<?php namespace Framework\Database\Definition\DataTypes\Strings;

use Framework\Database\Definition\DataTypes\Column;

abstract class StringDataType extends Column
{
	protected $charset;
	protected $collation;
	protected $length;

	/*public function length(int $length)
	{
		$this->length = $length;
		return $this;
	}*/

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

	protected function sql() : string
	{
		$sql = $this->renderName();
		$sql .= $this->renderType();
		$sql .= $this->renderCharset();
		$sql .= $this->renderCollate();
		$sql .= $this->renderNull();
		$sql .= $this->renderDefault();
		$sql .= $this->renderComment();
		$sql .= $this->renderUniqueKey();
		$sql .= $this->renderPrimaryKey();
		return $sql;
	}
}
