# SimilarWeb API PHP Client

[![Build Status](https://travis-ci.org/thunderer/SimilarWebApi.png?branch=master)](https://travis-ci.org/thunderer/SimilarWebApi)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5b82d37f-c410-4fb7-982c-ad495f488526/mini.png)](https://insight.sensiolabs.com/projects/5b82d37f-c410-4fb7-982c-ad495f488526)
[![License](https://poser.pugx.org/thunderer/similarweb-api/license.svg)](https://packagist.org/packages/thunderer/similarweb-api)
[![Dependency Status](https://www.versioneye.com/user/projects/53b70c110d5bb8be610000d2/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53b70c110d5bb8be610000d2)

## Introduction

[SimilarWeb](http://www.similarweb.com) is a project created by [SimilarGroup](http://www.similargroup.com) company. It collects and provides access to various website analytics. This is a PHP library implementing easy access to their [API](https://developer.similarweb.com/).

If you want to know what changed in each version please refer to CHANGELOG file in the root of this repository.

## Requirements:

- PHP 5.3 (namespaces),
- [Symfony's YAML](https://github.com/symfony/Yaml) library (parsing mapping data).

## Installation:

This library is available on [Packagist](https://packagist.org/packages/thunderer/similarweb-api) and uses `thunderer/similarweb-api` alias. If you use [Composer](https://getcomposer.org/) (and if you don't I don't know what you're waiting for) you can `composer require` it:

```
composer require thunderer/similarweb-api
```

Alternatively you can place it manually inside your `composer.json`:

```
(...)
"require": {
    "thunderer/similarweb-api": "dev-master"
},
(...)
```

and then run `composer install` or `composer update` as required.

> Some parts of this library are generated from configuration during `composer install`. If you're not using Composer please manually run `php bin/generate` from command line. You can read more on this topic in [Internals](#internals) section.

You can of course make it a [git submodule](http://git-scm.com/docs/git-submodule), download and place it in your project next to your regular code or something, but really, do yourself (and the whole industry) a favor and use Composer.

## Usage:

```php
use Thunder\SimilarWebApi\Request\Traffic;

// create client object
$client = new Thunder\SimilarWebApi\Client($yourUserKey, $desiredFormat);

// fetch response by passing API call name and desired domain
$response = $client->getResponse(new Traffic('kowalczyk.cc'));

// domain response class provides readable interface to get required information
/** @var $response Thunder\SimilarWebApi\Response\Traffic */
$rank = $response->getGlobalRank();

// there is also a raw response class which is used underneath
/** @var $rawResponse Thunder\SimilarWebApi\RawResponse */
$rawResponse = $response->getRawResponse();
$globalRank = $rawResponse->getValue('globalRank');

// check it by comparing both values:
assert($rank === $globalRank, 'Report an issue if you see this text.');
```

## Internals

The core of this library is a file called `mapping.yaml` which contains definitions of data returned by each API endpoint. In this section endpoint `GlobalRank` will be described and referred to as an example. This is its mapping configuration:

```
GlobalRank:
  path: globalRank
  url: /Site/{domain}/{path}?Format={format}&UserKey={token}
  values:
    rank:
      json: { field: Rank }
      xml: { field: Rank }
```

It states that there is an endpoint named `GlobalRank` which uses URL part `globalRank` and returns one value which library will refer to as `rank`, reading it either from JSON key `Rank` or XML element `Rank`.

Endpoints return associative arrays with keys containing three types of data:

- `value`: primitive value such as integer, string or date (rank: 2),
- `array`: array of primitive values of one type (months: [1, 3, 5]),
- `map`: key-value associative arrays (domains: [google.com: 3, google.pl: 7]),
- `tuple`: associative array with selected pieces of data as keys and associative values of the rest as values.

During either `composer install`, `composer update` or manual execution of `php bin/generate` command, endpoint mapping configuration is used to generate domain request and response classes with methods hiding library complexity behind readable accessors. Such approach makes it possible to have readable class API, good IDE autocompletion and highlighting possibilities with no additional programming work. When response is parsed all elements of given type are put inside their containers and those response classes act as a facade for raw response object.

```php
$response = $client->getResponse(/* ... */);
$rawResponse = $response->getRawResponse();

$response->getRank() === $rawResponse->getValue('rank');
```

## License

See LICENSE file in the root of this repository.
