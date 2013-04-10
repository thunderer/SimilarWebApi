README
======

[![Build Status](https://travis-ci.org/thunderer/SimilarWebApi.png?branch=master)](https://travis-ci.org/thunderer/SimilarWebApi)

SimilarWeb API Client
---------------------

This library allows you to easily handle calls to SimilarWeb API. All currently offered requests are supported:

* GlobalRank,
* CountryRank,
* CategoryRank,
* Category,
* Tags,
* SimilarSites.

Usage
-----

To see how library is used please see PHPUnit tests inside `Tests` directory.

Quick introduction:

```php
use Thunder\SimilarWebApi\Client;

$client = new Client('api-key-provided-by-similarweb', 'JSON');

$googleGlobalRankPosition = $client->api('GlobalRank', 'google.pl');
$googleGlobalRankPosition = $client->api('GlobalRank', 'google.pl'); // will be served from cache
$googleCategory = $client->api('Category', 'google.pl');
$facebookTags = $client->api('Category', 'facebook.com');

// to see the results
var_dump($googleGlobalRankPosition, $googleCategory, $facebookTags);
```