<?php

namespace ADT\BulkFetcher;

class ResultSetFetcher extends AbstractFetcher {

	/** @var \Kdyby\Doctrine\ResultSet */
	protected $resultSet;

	public function __construct(\Kdyby\Doctrine\ResultSet $resultSet, $bulkCount = 100) {
		parent::__construct($bulkCount);

		$this->resultSet = $resultSet;
	}

	protected function loadNewData() {
		return $this->resultSet
			->applyPaging($this->offset, $this->limit)
			->toArray();
	}

}

