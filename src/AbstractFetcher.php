<?php

namespace ADT\BulkFetcher;

abstract class AbstractFetcher implements \Iterator {

	use \Nette\SmartObject;

	/**
	 * Fetched data
	 * @var array
	 */
	protected $data;

	/**
	 * Index of current fetched row
	 * @var integer
	 */
	protected $dataIndex;

	protected $limit;

	protected $offset;

	/**
	 * @var array
	 */
	public $onBeforeFetch = [];

	public function __construct($batch = 100)
	{
		$this->limit = $batch;
	}

	public function rewind(): void
	{
		$this->offset = 0;
		$this->fetch();
	}

	public function current(): mixed
	{
		return current($this->data);
	}

	public function key(): mixed
	{
		return key($this->data);
	}

	public function next(): void
	{
		$this->dataIndex++;

		if (next($this->data) === FALSE) {
			// fetch next bulk

			if ($this->dataIndex === $this->limit) {
				// maybe we have more data

				$this->offset += $this->limit;	// next bulk
				$this->fetch();
			}
		}
	}

	public function valid(): bool
	{
		return current($this->data) !== FALSE;
	}

	protected function fetch(): void
	{
		$this->onBeforeFetch();

		$this->dataIndex = 0;
		$this->data = $this->loadNewData();
	}

	abstract protected function loadNewData();

}

