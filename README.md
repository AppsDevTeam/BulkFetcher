#

## Installation

via composer:

```sh
composer require adt/bulk-fetcher
```

## Full example

```php
$bulkedResultSet = new \ADT\BulkFetcher($resultSet, 100);
$bulkedResultSet->onBeforeLoadNewData[] = function() use ($entityManager) {
	$entityManager->clear();
};

foreach ($bulkedResultSet as $key => $row) {
	// code
}
```
