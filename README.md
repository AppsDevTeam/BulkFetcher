# BulkFetcher

`\ADT\BulkFetcher\Factory` can be used with:

 - `\Kdyby\Doctrine\ResultSet`
 - `\Kdyby\Doctrine\NativeQueryBuilder`

## Installation

via composer:

```sh
composer require adt/bulk-fetcher
```

## Full example

Whole batch is in transaction.

```php

$qb = $entityManager->createQueryBuilder('user');

try {
	$entityManager->beginTransaction();	
	
	$data = \ADT\BulkFetcher\Factory::create($qb, 100);
	$data->onBeforeFetch[] = function() use ($entityManager) {
		$entityManager->commit();
		$entityManager->clear();
		$entityManager->beginTransaction();
	};
	
	foreach ($data as $key => $row) {
		// code
	}
	
	$entityManager->commit();

} catch (\Exception $e) {
	$entityManager->rollback();
	throw $e;
}
```
