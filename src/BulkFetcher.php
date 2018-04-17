<?php

namespace ADT\BulkFetcher;

class BulkFetcher extends \Nette\Object implements \Iterator {

	/** @var \Kdyby\Doctrine\ResultSet */
	protected $resultSet;

	/**
	 * Data from database
	 * @var array
	 */
	protected $bulkData;

	/**
	 * Index of current fetched row
	 * @var integer
	 */
	protected $bulkDataIndex;

	protected $limit;

	protected $offset;

	/**
	 * @var array
	 */
	public $onBeforeLoadNewData = [];

	public function __construct(\Kdyby\Doctrine\ResultSet $resultSet, $bulkCount = 100) {
		$this->resultSet = $resultSet;
		$this->limit = $bulkCount;
	}

	public function rewind() {
		$this->offset = 0;
		$this->loadNewData();
	}

	public function current() {
		return current($this->bulkData);
	}

	public function key() {
		return key($this->bulkData);
	}

	public function next() {
		$this->bulkDataIndex++;

		if (next($this->bulkData) === FALSE) {
			// fetch next bulk

			if ($this->bulkDataIndex === $this->limit) {
				// maybe we have more data

				$this->offset += $this->limit;	// next bulk
				$this->loadNewData();
			}
		}
	}

	public function valid() {
		return current($this->bulkData) !== FALSE;
	}

	protected function loadNewData() {
		$this->onBeforeLoadNewData();

		$this->bulkDataIndex = 0;
		$this->bulkData = $this->resultSet
			->applyPaging($this->offset, $this->limit)
			->toArray();
	}

}

