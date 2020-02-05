<?php

namespace ADT\BulkFetcher;

class QueryBuilderFetcher extends AbstractFetcher {

	/** @var \Doctrine\ORM\QueryBuilder */
	protected $qb;

	public function __construct($qb, $batch = 100) {
		parent::__construct($batch);

		$this->qb = $qb;
	}

	protected function loadNewData() {
		return $this->qb
			->getQuery()
			->setFirstResult($this->offset)
			->setMaxResults($this->limit)
			->getResult();
	}

}

