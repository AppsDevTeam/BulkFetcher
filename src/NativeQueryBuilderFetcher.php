<?php

namespace ADT\BulkFetcher;

use Kdyby\Doctrine\NativeQueryBuilder;

class NativeQueryBuilderFetcher extends AbstractFetcher {

	/** @var NativeQueryBuilder */
	protected $qb;

	public function __construct(NativeQueryBuilder $qb, $batch = 100) {
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

