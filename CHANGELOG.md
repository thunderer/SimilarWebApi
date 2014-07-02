CHANGELOG
=========

Version 0.*
-----------

* 0.3.0 (02.07.2014)

 * introduced `bin/generate` command line script to generate domain response classes
 * those classes are generated during `composer install` (`post-install-cmd` hook)
 * `Response` class was renamed to `RawResponse` (same API), former serves as a base for generated ones
 * updated dependencies to reflect current library needs

* 0.2.0 (19.02.2014)

 * All SimilarWeb API calls from all versions, 19 calls total
 * 100% PHPUnit tests coverage

* 0.1.0 (05.02.2014)

 * Basic library with all SimilarWeb API v1 calls: Traffic, Engagement, Keywords and SocialReferrals
 * 100% PHPUnit tests coverage