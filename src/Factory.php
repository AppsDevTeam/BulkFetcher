<?php

namespace ADT\BulkFetcher;

use Doctrine\ORM\Query;

class Factory
{

	/**
	 * @param \Kdyby\Doctrine\NativeQueryBuilder|\Kdyby\Doctrine\ResultSet|Query $dataProvider
	 * @param int $batch
	 * @return NativeQueryBuilderFetcher|ResultSetFetcher
	 */
	public static function create($dataProvider, $batch = 100)
	{
		switch (true) {
			case $dataProvider instanceof \Kdyby\Doctrine\ResultSet:
				return new ResultSetFetcher($dataProvider, $batch);
				break;

			case $dataProvider instanceof \Kdyby\Doctrine\NativeQueryBuilder:
				return new NativeQueryBuilderFetcher($dataProvider, $batch);
				break;

			case $dataProvider instanceof \Doctrine\ORM\QueryBuilder:
				return new QueryBuilderFetcher($dataProvider, $batch);
				break;

			case $dataProvider instanceof Query:
				return new QueryFetcher($dataProvider, $batch);
				break;
		}
	}

}
