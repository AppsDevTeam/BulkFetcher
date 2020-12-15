<?php

namespace ADT\BulkFetcher;

use App\Utils;
use Kdyby\Doctrine\Dql\InlineParamsBuilder;
use Kdyby\Doctrine\NativeQueryBuilder;

/**
 * Vyřešení zpomalení v MySQL kvůli vysokým offsetům podle https://stackoverflow.com/a/16935313/4837606
 */
class QueryBuilderFetcher extends AbstractFetcher {

	/** @var NativeQueryBuilder|InlineParamsBuilder */
	protected $qb;

	/** @var array */
	protected $lastRowData = NULL;

	/** @var string */
	protected $entityIdentifierColumn = NULL;

	/** @var array */
	protected $queryHints = [];

	public function setHint($name, $value)
	{
		$this->queryHints[$name] = $value;
		return $this;
	}

	/**
	 * QueryBuilderFetcher constructor.
	 * @param int $qb
	 * @param int $batch
	 * @param string $entityIdentifierColumn
	 */
	public function __construct($qb, $batch = 100, $entityIdentifierColumn = 'e.id') {
		parent::__construct($batch);

		$this->qb = $qb;
		$this->entityIdentifierColumn = $entityIdentifierColumn;

		$this->lastRowData = [
			$this->entityIdentifierColumn => 0,
		];

		$this->qb->andWhere("{$this->entityIdentifierColumn} > :".static::param($this->entityIdentifierColumn));

		$this->qb->addOrderBy("{$this->entityIdentifierColumn}", 'ASC');
	}

	protected function loadNewData() {

		foreach ($this->lastRowData as $columnName => $value) {
			$this->qb->setParameter(static::param($columnName), $value);
		}

		$query = $this->qb
			->getQuery();

		foreach ($this->queryHints as $name => $value) {
			$query->setHint($name, $value);
		}

		$data = $query
			->setFirstResult(0)
			->setMaxResults($this->limit)
			->getResult();

		if (!count($data)) {
			return $data;
		}

		$lastRow = $data[count($data)-1];


		if (!is_object($lastRow)) {
			// Doctrine vrací objekt na indexu 0
			$lastRow = $lastRow[0]; // TODO: možnost nastavit si callback, který vrátí atribut (viz dále)
		}

		foreach (array_keys($this->lastRowData) as $columnName) {
			// TODO: možnost nastavit si callback, který vrátí atribut, protože to nemusí být pole objektů

			$rootEntityAlias = substr($this->entityIdentifierColumn, 0, strpos($this->entityIdentifierColumn, '.'));    // "e.id" -> "e"
			$propertyName = str_replace(
				'.',
				'->',
				strpos($columnName, $rootEntityAlias.'.') === 0
					? substr($columnName, strpos($columnName, '.') + 1) // "e.column1" -> "column1"
					: $columnName
			);

			$this->lastRowData[$columnName] = Utils::accessNestedObjectPropertyByString($propertyName, $lastRow);
		}


		return $data;
	}

	protected static function param($param)
	{
		// TODO: možnost nastavit si název parametru, aby náhodou nekolidoval
		return 'QueryBuilderFetcher_' . str_replace('.', '__', $param);
	}

}

