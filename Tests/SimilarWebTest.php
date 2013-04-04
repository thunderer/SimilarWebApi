<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\Client;

class SimilarWebTest extends \PHPUnit_Framework_TestCase
    {
    protected $instance;

    public function setUp()
        {
        }

    public function tearDown()
        {
        }

    public function testInstance()
        {
        $sw = new Client('da39a3ee5e6b4b0d3255bfef95601890');
        $this->assertInstanceOf('Thunder\SimilarWebApi\Client', $sw);
        }

    public function testDefaultResponseFormatHandling()
        {
        $testUserKey = 'da39a3ee5e6b4b0d3255bfef95601890';
        $sw = new Client($testUserKey);
        $reflectionObject = new \ReflectionObject($sw);
        $defaultFormat = $reflectionObject->getProperty('format');
        $defaultFormat->setAccessible(true);
        $this->assertEquals('JSON', $defaultFormat->getValue($sw));
        $sw = new Client($testUserKey, 'XML');
        $this->assertEquals('XML', $defaultFormat->getValue($sw));
        $this->setExpectedException('InvalidArgumentException');
        $sw = new Client($testUserKey, 'INVALID');
        }

    public function testUserKeyHandling()
        {
        $testUserKey = 'da39a3ee5e6b4b0d3255bfef95601890';
        $sw = new Client($testUserKey);
        $reflectionObject = new \ReflectionObject($sw);
        $userKey = $reflectionObject->getProperty('userKey');
        $userKey->setAccessible(true);
        $this->assertEquals($testUserKey, $userKey->getValue($sw));
        $anotherUserKey = 'da39a3ee5e6b4b0d3255bfef95601891';
        $sw = new Client($anotherUserKey);
        $this->assertEquals($anotherUserKey, $userKey->getValue($sw));
        $userKeys = array(
            '',
            'random_invalid_sequence',
            '07h3r1nv4l1ds3qu3nc3',
            '07h3r1nv4l1ds3qu3nc307h3r1nv4l1ds3qu3nc307h3r1nv4l1ds3qu3nc3',
            // 'da39a3ee5e6b4b0d3255bfef95601890', // valid honeypot :)
            );
        foreach($userKeys as $key)
            {
            $failed = false;
            try
                {
                $sw = new Client($key);
                }
            catch(\InvalidArgumentException $e)
                {
                $failed = true;
                }
            if(!$failed)
                {
                $this->fail();
                continue;
                }
            $this->assertTrue(true);
            }
        }

    public function apiCallsProvider()
        {

        return array( /* #0 */

            /* -------------------------------------------------------------- */

        /* ------------------------------------------------------------------ */
        /* -- GLOBAL RANK --------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array('GlobalRank', 'JSON', 'google.pl', 388, null, array(200, 'GlobalRank-200.json')),
            array('GlobalRank', 'JSON', 'invalid', 'exception', 'RuntimeException', array(404, 'GlobalRank-404.json')),
            array('GlobalRank', 'XML', 'google.pl', 388, null, array(200, 'GlobalRank-200.xml')),
            array('GlobalRank', 'XML', 'invalid', 'exception', 'RuntimeException', array(404, 'GlobalRank-404.xml')),

        /* ------------------------------------------------------------------ */
        /* -- COUNTRY RANK -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array('CountryRank', 'JSON', 'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
                null, array(200, 'CountryRank-200.json')),
            array('CountryRank', 'JSON', 'invalid', array(), null, array(200, 'CountryRank-404.json')),
            array('CountryRank', 'XML',  'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
                null,
                array(200, 'CountryRank-200.xml')),
            array('CountryRank', 'XML',  'invalid', array(), null, array(200, 'CountryRank-404.xml')),
            array('CountryRank', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY RANK ------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array('CategoryRank', 'JSON', 'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 20,
                    ),
                null, array(200, 'CategoryRank-200.json')),
            array('CategoryRank', 'JSON', 'invalid', array(), null, array(200, 'CategoryRank-404.json')),
            array('CategoryRank', 'XML',  'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 20,
                    ),
                null, array(200, 'CategoryRank-200.xml')),
            array('CategoryRank', 'XML',  'invalid', array(), null, array(200, 'CategoryRank-404.xml')),
            array('CategoryRank', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- WEBSITE TAGS -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array('Tags', 'JSON', 'google.pl',
                array(
                    'google' => 0.812606952660115,
                    'search' => 0.28651044373034,
                    'folder zakładek osobistych' => 0.252063426499681,
                    'wyszukiwarka' => 0.190362443330678,
                    'mobilne zakładki' => 0.169491699143677,
                    'nazwa folderu' => 0.169491699143677,
                    'wyszukiwarki' => 0.15017253537674,
                    'internet' => 0.136542773364803,
                    'wyszukiwanie' => 0.0970751723399089,
                    'z internet explorer' => 0.0889742436821045,
                    ),
                null, array(200, 'Tags-200.json')),
            array('Tags', 'JSON', 'invalid', array(), null, array(200, 'Tags-404.json')),
            array('Tags', 'XML',  'google.pl',
                array(
                    'google' => 0.812606952660115,
                    'search' => 0.28651044373034,
                    'folder zakładek osobistych' => 0.252063426499681,
                    'wyszukiwarka' => 0.190362443330678,
                    'mobilne zakładki' => 0.169491699143677,
                    'nazwa folderu' => 0.169491699143677,
                    'wyszukiwarki' => 0.15017253537674,
                    'internet' => 0.136542773364803,
                    'wyszukiwanie' => 0.0970751723399089,
                    'z internet explorer' => 0.0889742436821045,
                    ),
                null, array(200, 'Tags-200.xml')),
            array('Tags', 'XML', 'invalid', array(), null, array(200, 'Tags-404.xml')),
            array('Tags', 'XML', 'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- SIMILAR SITES ------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array('SimilarSites', 'JSON', 'google.pl',
                array(
                    'onet.pl' => 0.61636751664836,
                    'o2.pl' => 0.60592599084329,
                    'wp.pl' => 0.58248742751268,
                    'allegro.pl' => 0.58065731826857,
                    'searchgi.com' => 0.56054822455505,
                    'interia.pl' => 0.55590180100101,
                    'netsprint.pl' => 0.45038046272021,
                    'pl.wikipedia.org' => 0.44633659083624,
                    'nk.pl' => 0.42615714930277,
                    'szukacz.pl' => 0.40115853165034,
                    'jabago.com' => 0.4007476195845,
                    'noamok.de' => 0.39713958773577,
                    'gry.pl' => 0.39422711929614,
                    'gcity.pl' => 0.39160146821524,
                    'search.conduit.com' => 0.39057300532716,
                    'googleblog.blogspot.com' => 0.38313083138904,
                    'wyszukiwarka-chomikuj.pl' => 0.37362632165685,
                    'nasza-klasa.pl/login' => 0.36550396041897,
                    'zumi.pl' => 0.36051490096293,
                    'tlen.pl' => 0.36048139871828,
                    ),
                null, array(200, 'SimilarSites-200.json')),
            array('SimilarSites', 'JSON', 'invalid', array(), null, array(200, 'SimilarSites-404.json')),
            array('SimilarSites', 'XML',  'google.pl',
                array(
                    'onet.pl' => 0.61636751664836,
                    'o2.pl' => 0.60592599084329,
                    'wp.pl' => 0.58248742751268,
                    'allegro.pl' => 0.58065731826857,
                    'searchgi.com' => 0.56054822455505,
                    'interia.pl' => 0.55590180100101,
                    'netsprint.pl' => 0.45038046272021,
                    'pl.wikipedia.org' => 0.44633659083624,
                    'nk.pl' => 0.42615714930277,
                    'szukacz.pl' => 0.40115853165034,
                    'jabago.com' => 0.4007476195845,
                    'noamok.de' => 0.39713958773577,
                    'gry.pl' => 0.39422711929614,
                    'gcity.pl' => 0.39160146821524,
                    'search.conduit.com' => 0.39057300532716,
                    'googleblog.blogspot.com' => 0.38313083138904,
                    'wyszukiwarka-chomikuj.pl' => 0.37362632165685,
                    'nasza-klasa.pl/login' => 0.36550396041897,
                    'zumi.pl' => 0.36051490096293,
                    'tlen.pl' => 0.36048139871828,
                    ),
                null,
                array(200, 'SimilarSites-200.xml')),
            array('SimilarSites', 'XML',  'invalid', array(), null, array(200, 'SimilarSites-404.xml')),
            array('SimilarSites', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY ------------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array('Category', 'JSON', 'google.pl', 'Internet_and_Telecom/Search_Engine', null, array(200, 'Category-200.json')),
            array('Category', 'JSON', 'invalid', '', null, array(200, 'Category-404.json')),
            array('Category', 'XML',  'google.pl', 'Internet_and_Telecom/Search_Engine', null, array(200, 'Category-200.xml')),
            array('Category', 'XML',  'invalid', '', null, array(200, 'Category-404.xml')),
            array('Category', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

            array('Category', 'XML',  'invalid', 'exception', 'InvalidArgumentException', array(200, 'xxx')),
            array('Category', 'JSON',  'invalid', 'exception', 'InvalidArgumentException', array(200, '}{')),

            /* -------------------------------------------------------------- */

            ); // provider array
        }

    /**
     * @dataProvider apiCallsProvider
     */
    public function testApiCalls($call, $format, $domain, $result, $exception, $payload)
        {
        $swMock = $this->getMock('Thunder\SimilarWebApi\Client', array('executeRequest'), array(
            'userKey' => 'da39a3ee5e6b4b0d3255bfef95601890',
            'format' => $format,
            ));
        $contents = @file_get_contents(__DIR__.'/Responses/'.$payload[1]);
        if(false !== $contents)
            {
            $payload[1] = $contents;
            }

        $swMock
            ->expects($this->once())
            ->method('executeRequest')
            ->with($call, $domain)
            ->will($this->returnValue($payload));

        if('exception' == $result)
            {
            $this->setExpectedException($exception);
            }
        $actualResult = $swMock->api($call, $domain);
        if('exception' != $result)
            {
            $this->assertEquals($result, $actualResult);
            }

        $actualResult = $swMock->api($call, $domain);
        if('exception' != $result)
            {
            $this->assertEquals($result, $actualResult);
            }
        }

    public function invalidCallsProvider()
        {
        return array(
            array('GlobalRank', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('GlobalRank', array('}{', 'JSON'), '', 'InvalidArgumentException'),
            array('GlobalRank', array('{}', 'JSON'), '', 'RuntimeException'),
            array('GlobalRank', array('<>', 'XML'), '', 'InvalidArgumentException'),
            array('GlobalRank', array('<x />', 'XML'), '', 'RuntimeException'),

            array('CountryRank', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('CountryRank', array('{}', 'JSON'), '', 'RuntimeException'),
            array('CountryRank', array('<x />', 'XML'), '', 'RuntimeException'),

            array('CategoryRank', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('Tags', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('SimilarSites', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('Category', array('response', 'INV'), '', 'InvalidArgumentException'),
            );
        }

    /**
     * @dataProvider invalidCallsProvider
     */
    public function testForCoverage($method, array $args, $result, $exception = null)
        {
        $sw = new Client('da39a3ee5e6b4b0d3255bfef95601890', 'JSON');
        if(null !== $exception)
            {
            $this->setExpectedException($exception);
            }
        $className = 'Thunder\SimilarWebApi\\Parser\\'.$method;
        $parser = new $className();
        $actualResult = $parser->parse($args[0], $args[1]);
        $this->assertEquals($result, $actualResult);
        }

    public function testRealRequest()
        {
        $sw = new Client('da39a3ee5e6b4b0d3255bfef95601890', 'JSON');
        $this->setExpectedException('RuntimeException');
        $actualResult = $sw->api('GlobalRank', 'google.pl');
        }
    }