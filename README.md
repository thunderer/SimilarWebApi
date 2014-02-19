SimilarWeb PHP API Client
======================

[![Build Status](https://travis-ci.org/thunderer/SimilarWebApi.png?branch=master)](https://travis-ci.org/thunderer/SimilarWebApi)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5b82d37f-c410-4fb7-982c-ad495f488526/mini.png)](https://insight.sensiolabs.com/projects/5b82d37f-c410-4fb7-982c-ad495f488526)

Introduction
-----------

[SimilarWeb](http://www.similarweb.com) is a project created by [SimilarGroup](http://www.similargroup.com) company which provides various website analytics. You can check it yourself at [their website](http://www.similarweb.com). This is a PHP library implementing easy access to their API.

Requirements:
-----------

This library requires PHP 5.3 and [Symfony's YAML](https://github.com/symfony/Yaml) library.

Installation:
-----------

Add `thunderer/similarweb-php-api` to your `composer.json` and then run `install` or `update` command as required. You can also 

Usage:
-----

```php
// create client object
$client = new Thunder\SimilarWebApi\Client();

// fetch response by passing API call name and desired domain
$response = $client->getResponse('Traffic', 'kowalczyk.cc');

// response class provides clean interface to get any information you want
// you just need to know what is the 
$globalRank = $response->getValue('rank');
$trafficReach = $response->getMap('trafficReach');
```