<?php

namespace ADT\BulkFetcher;

use Doctrine\ORM\Query;

class QueryFetcher extends AbstractFetcher {

	/** @var Query */
	protected $query;

	public function __construct(Query $query, int $bulkCount = 100)
	{
		parent::__construct($bulkCount);

		$this->query = $query;
	}


	protected function loadNewData()
	{
		return $this->query
			->setFirstResult($this->offset)
			->setMaxResults($this->limit)
			->getResult();
	}

}

