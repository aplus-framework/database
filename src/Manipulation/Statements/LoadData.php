<?php namespace Framework\Database\Manipulation\Statements;

/**
 * Class LoadData.
 *
 * @see https://mariadb.com/kb/en/library/load-data-infile/
 */
class LoadData extends Statement
{
	use Traits\Set;
	public const OPT_LOW_PRIORITY = 'LOW_PRIORITY';
	public const OPT_CONCURRENT = 'CONCURRENT';
	public const OPT_LOCAL = 'LOCAL';

	protected function renderOptions() : ?string
	{
		if ( ! $this->hasOptions()) {
			return null;
		}
		$options = $this->sql['options'];
		foreach ($options as &$option) {
			$input = $option;
			$option = \strtoupper($option);
			if ( ! \in_array($option, [
				static::OPT_LOW_PRIORITY,
				static::OPT_CONCURRENT,
				static::OPT_LOCAL,
			], true)) {
				throw new \InvalidArgumentException("Invalid option: {$input}");
			}
		}
		unset($option);
		$intersection = \array_intersect(
			$options,
			[static::OPT_LOW_PRIORITY, static::OPT_CONCURRENT]
		);
		if (\count($intersection) > 1) {
			throw new \LogicException('Options LOW_PRIORITY and CONCURRENT can not be used together');
		}
		return \implode(' ', $options);
	}

	public function infile(string $filename)
	{
		$this->sql['infile'] = $filename;
		return $this;
	}

	protected function renderInfile() : string
	{
		$filename = $this->database->protectIdentifier($this->sql['infile']);
		return " INFILE {$filename}";
	}

	public function intoTable($table)
	{
		$this->sql['table'] = $table;
		return $this;
	}

	public function charset(string $charset)
	{
		$this->sql['charset'] = $charset;
		return $this;
	}

	protected function renderCharset() : ?string
	{
		if ( ! isset($this->sql['charset'])) {
			return null;
		}
		return " CHARACTER SET {$this->sql['charset']}";
	}

	public function ignoreLines(int $number)
	{
		$this->sql['ignore_lines'] = $number;
		return $this;
	}

	protected function renderIgnoreLines() : ?string
	{
		if ( ! isset($this->sql['ignore_lines'])) {
			return null;
		}
		return " IGNORE {$this->sql['ignore_lines']} LINES";
	}

	public function sql() : string
	{
		// TODO: Implement sql() method.
	}

	public function run()
	{
		// TODO: Implement run() method.
	}
}
