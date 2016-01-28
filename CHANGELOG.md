Changelog
=========

Version 0.*
-----------

* 0.5.1
  * updated Composer dependencies
  * added new PHP versions to Travis matrix

* 0.5.0
  * introduced ClientFacade generated class for easier library usage
  * introduced generator for ClientFacade class
  * minor refactorings and documentation update

* 0.4.2
  * downgraded Symfony2 YAML librat requirement to ~2.3 for LTS users

* 0.4.1 (08.11.2014)
  * fixed issue with installation through Composer (`bin/generate` script failed to require autoloader)
  * updated README on how to automatically run `bin/generate` in your project after Composer install or update
  * refactored parsers to have cleaner and smaller methods

* 0.4.0 (06.11.2014)
  * introduced `bin/endpoints` command line script to get overview of implemented API calls
  * implemented all new calls (Pro APIs), 31 total
  * introduced Request classes for better flexibility over API changes (new APIs have different parameters)
  * `bin/generate` script is now invoked also after `composer update`
  * major refactoring of the whole library, unfortunately breaks all backward compatibility
  * first tagged release (0.3.0 was also tagged for BC reasons)

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
