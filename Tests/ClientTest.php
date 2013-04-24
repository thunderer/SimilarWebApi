<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
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

            array(1, 'GlobalRank', 'JSON', 'google.pl', 388, null, array(200, 'V1/GlobalRank-200.json')),
            array(1, 'GlobalRank', 'JSON', 'invalid', 'exception', 'RuntimeException', array(404, 'V1/GlobalRank-404.json')),
            array(1, 'GlobalRank', 'XML', 'google.pl', 388, null, array(200, 'V1/GlobalRank-200.xml')),
            array(1, 'GlobalRank', 'XML', 'invalid', 'exception', 'RuntimeException', array(404, 'V1/GlobalRank-404.xml')),

        /* ------------------------------------------------------------------ */
        /* -- COUNTRY RANK -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(1, 'CountryRank', 'JSON', 'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
                null, array(200, 'V1/CountryRank-200.json')),
            array(1, 'CountryRank', 'JSON', 'invalid', array(), null, array(200, 'V1/CountryRank-404.json')),
            array(1, 'CountryRank', 'XML',  'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
                null,
                array(200, 'V1/CountryRank-200.xml')),
            array(1, 'CountryRank', 'XML',  'invalid', array(), null, array(200, 'V1/CountryRank-404.xml')),
            array(1, 'CountryRank', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY RANK ------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(1, 'CategoryRank', 'JSON', 'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 20,
                    ),
                null, array(200, 'V1/CategoryRank-200.json')),
            array(1, 'CategoryRank', 'JSON', 'invalid', array(), null, array(200, 'V1/CategoryRank-404.json')),
            array(1, 'CategoryRank', 'XML',  'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 20,
                    ),
                null, array(200, 'V1/CategoryRank-200.xml')),
            array(1, 'CategoryRank', 'XML',  'invalid', array(), null, array(200, 'V1/CategoryRank-404.xml')),
            array(1, 'CategoryRank', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- WEBSITE TAGS -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(1, 'Tags', 'JSON', 'google.pl',
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
                null, array(200, 'V1/Tags-200.json')),
            array(1, 'Tags', 'JSON', 'invalid', array(), null, array(200, 'V1/Tags-404.json')),
            array(1, 'Tags', 'XML',  'google.pl',
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
                null, array(200, 'V1/Tags-200.xml')),
            array(1, 'Tags', 'XML', 'invalid', array(), null, array(200, 'V1/Tags-404.xml')),
            array(1, 'Tags', 'XML', 'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- SIMILAR SITES ------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array(1, 'SimilarSites', 'JSON', 'google.pl',
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
                null, array(200, 'V1/SimilarSites-200.json')),
            array(1, 'SimilarSites', 'JSON', 'invalid', array(), null, array(200, 'V1/SimilarSites-404.json')),
            array(1, 'SimilarSites', 'XML',  'google.pl',
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
                array(200, 'V1/SimilarSites-200.xml')),
            array(1, 'SimilarSites', 'XML',  'invalid', array(), null, array(200, 'V1/SimilarSites-404.xml')),
            array(1, 'SimilarSites', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY ------------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array(1, 'Category', 'JSON', 'google.pl', 'Internet_and_Telecom/Search_Engine', null, array(200, 'V1/Category-200.json')),
            array(1, 'Category', 'JSON', 'invalid', '', null, array(200, 'V1/Category-404.json')),
            array(1, 'Category', 'XML',  'google.pl', 'Internet_and_Telecom/Search_Engine', null, array(200, 'V1/Category-200.xml')),
            array(1, 'Category', 'XML',  'invalid', '', null, array(200, 'V1/Category-404.xml')),
            array(1, 'Category', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),
            array(1, 'Category', 'XML',  'invalid', 'exception', 'InvalidArgumentException', array(200, 'xxx')),
            array(1, 'Category', 'JSON',  'invalid', 'exception', 'InvalidArgumentException', array(200, '}{')),


        /* ------------------------------------------------------------------ */
        /* ------------------------------------------------------------------ */
        /* -- V2 ------------------------------------------------------------ */
        /* ------------------------------------------------------------------ */
        /* ------------------------------------------------------------------ */

        /* ------------------------------------------------------------------ */
        /* -- SIMILAR SITES ------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array(2, 'SimilarSites', 'JSON', 'google.pl',
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
                null, array(200, 'V2/SimilarSites/200.json')),
            array(2, 'SimilarSites', 'JSON', 'invalid', array(), null, array(200, 'V2/SimilarSites/404.json')),
            array(2, 'SimilarSites', 'XML',  'google.pl',
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
                array(200, 'V2/SimilarSites/200.xml')),
            array(2, 'SimilarSites', 'XML',  'invalid', array(), null, array(200, 'V2/SimilarSites/404.xml')),
            array(2, 'SimilarSites', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY RANK ------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'CategoryRank', 'JSON', 'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 22,
                ),
                null, array(200, 'V2/CategoryRank/200.json')),
            array(2, 'CategoryRank', 'JSON', 'invalid', array(), null, array(200, 'V2/CategoryRank/404.json')),
            array(2, 'CategoryRank', 'XML',  'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 22,
                ),
                null, array(200, 'V2/CategoryRank/200.xml')),
            array(2, 'CategoryRank', 'XML',  'invalid', array(), null, array(200, 'V2/CategoryRank/404.xml')),
            array(2, 'CategoryRank', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY ------------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array(2, 'Category', 'JSON', 'google.pl', 'Internet_and_Telecom/Search_Engine', null, array(200, 'V2/Category/200.json')),
            array(2, 'Category', 'JSON', 'invalid', '', null, array(200, 'V2/Category/404.json')),
            array(2, 'Category', 'XML',  'google.pl', 'Internet_and_Telecom/Search_Engine', null, array(200, 'V2/Category/200.xml')),
            array(2, 'Category', 'XML',  'invalid', '', null, array(200, 'V2/Category/404.xml')),
            array(2, 'Category', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),
            array(2, 'Category', 'XML',  'invalid', 'exception', 'InvalidArgumentException', array(200, 'xxx')),
            array(2, 'Category', 'JSON',  'invalid', 'exception', 'InvalidArgumentException', array(200, '}{')),

        /* ------------------------------------------------------------------ */
        /* -- TAGS ---------------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'Tags', 'JSON', 'google.pl',
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
                null, array(200, 'V2/Tags/200.json')),
            array(2, 'Tags', 'JSON', 'invalid', array(), null, array(200, 'V2/Tags/404.json')),
            array(2, 'Tags', 'XML',  'google.pl',
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
                null, array(200, 'V2/Tags/200.xml')),
            array(2, 'Tags', 'XML', 'invalid', array(), null, array(200, 'V2/Tags/404.xml')),
            array(2, 'Tags', 'XML', 'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- ALSO VISITED -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'AlsoVisited', 'JSON', 'google.pl',
                array(
                    'onet.pl' => 0.022534717683465,
                    'allegro.pl' => 0.021907064376399,
                    'wp.pl' => 0.017901736823318,
                    'nk.pl' => 0.014344759268064,
                    'interia.pl' => 0.0091479517056112,
                    'gazeta.pl' => 0.0066533440412334,
                    'pl.wikipedia.org' => 0.0059897697192657,
                    'tablica.pl' => 0.0047117062793466,
                    'gry.pl' => 0.0045691124123167,
                    'pudelek.pl' => 0.0045582905721128,
                    'poczta.o2.pl' => 0.0041373591347806,
                    'zalukaj.tv' => 0.0041012903549944,
                    'chomikuj.pl' => 0.00398814024866,
                    'accounts.youtube.com' => 0.0039560483760792,
                    'kwejk.pl' => 0.0035445142358,
                    'demotywatory.pl' => 0.0033251667771115,
                    'besty.pl' => 0.0027078186158167,
                    'ipko.pl' => 0.0026801638724117,
                    'delta-search.com' => 0.0025830806284545,
                    'otomoto.pl' => 0.0025786148613123,
                    ),
                null, array(200, 'V2/AlsoVisited/200.json')),
            array(2, 'AlsoVisited', 'JSON', 'invalid', array(), null, array(200, 'V2/AlsoVisited/404.json')),
            array(2, 'AlsoVisited', 'XML',  'google.pl',
                array(
                    'onet.pl' => 0.022534717683465,
                    'allegro.pl' => 0.021907064376399,
                    'wp.pl' => 0.017901736823318,
                    'nk.pl' => 0.014344759268064,
                    'interia.pl' => 0.0091479517056112,
                    'gazeta.pl' => 0.0066533440412334,
                    'pl.wikipedia.org' => 0.0059897697192657,
                    'tablica.pl' => 0.0047117062793466,
                    'gry.pl' => 0.0045691124123167,
                    'pudelek.pl' => 0.0045582905721128,
                    'poczta.o2.pl' => 0.0041373591347806,
                    'zalukaj.tv' => 0.0041012903549944,
                    'chomikuj.pl' => 0.00398814024866,
                    'accounts.youtube.com' => 0.0039560483760792,
                    'kwejk.pl' => 0.0035445142358,
                    'demotywatory.pl' => 0.0033251667771115,
                    'besty.pl' => 0.0027078186158167,
                    'ipko.pl' => 0.0026801638724117,
                    'delta-search.com' => 0.0025830806284545,
                    'otomoto.pl' => 0.0025786148613123,
                    ),
                null,
                array(200, 'V2/AlsoVisited/200.xml')),
            array(2, 'AlsoVisited', 'XML',  'invalid', array(), null, array(200, 'V2/AlsoVisited/404.xml')),
            array(2, 'AlsoVisited', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- ENGAGEMENT -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'Engagement', 'JSON', 'google.pl',
                array(
                    'averagePageViews' => 6.88994932858508,
                    'averageTimeOnSite' => 719.559369107179,
                    'bounceRate' => 0.242775199273553,
                    'date' => 1362092400,
                    ),
                null, array(200, 'V2/Engagement/200.json')),
            array(2, 'Engagement', 'JSON', 'invalid', array(), null, array(200, 'V2/Engagement/404.json')),
            array(2, 'Engagement', 'XML',  'google.pl',
                array(
                    'averagePageViews' => 6.88994932858508,
                    'averageTimeOnSite' => 719.559369107179,
                    'bounceRate' => 0.242775199273553,
                    'date' => 1362092400,
                    ),
                null,
                array(200, 'V2/Engagement/200.xml')),
            array(2, 'Engagement', 'XML',  'invalid', array(), null, array(200, 'V2/Engagement/404.xml')),
            array(2, 'Engagement', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- LEADING DESTINATION ------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'LeadingDestination', 'JSON', 'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'sites' => array(
                        'youtube.com',
                        'facebook.com',
                        'google.com',
                        'wikipedia.org',
                        'onet.pl',
                        'allegro.pl',
                        'wp.pl',
                        'chomikuj.pl',
                        'nk.pl',
                        'tekstowo.pl',
                        ),
                    ),
                null, array(200, 'V2/LeadingDestination/200.json')),
            array(2, 'LeadingDestination', 'JSON', 'invalid', array(), null, array(200, 'V2/LeadingDestination/404.json')),
            array(2, 'LeadingDestination', 'XML',  'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'sites' => array(
                        'youtube.com',
                        'facebook.com',
                        'google.com',
                        'wikipedia.org',
                        'onet.pl',
                        'allegro.pl',
                        'wp.pl',
                        'chomikuj.pl',
                        'nk.pl',
                        'tekstowo.pl',
                        ),
                    ),
                null, array(200, 'V2/LeadingDestination/200.xml')),
            array(2, 'LeadingDestination', 'XML',  'invalid', array(), null, array(200, 'V2/LeadingDestination/404.xml')),
            array(2, 'LeadingDestination', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- LEADING REFERRING --------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'LeadingReferring', 'JSON', 'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'sites' => array(
                        'v9.com',
                        '22find.com',
                        '22apple.com',
                        'mozilla.org',
                        'wp.pl',
                        'starterek.pl',
                        'onet.pl',
                        'allegro.pl',
                        'start24.pl',
                        'interia.pl',
                        ),
                    ),
                null, array(200, 'V2/LeadingReferring/200.json')),
            array(2, 'LeadingReferring', 'JSON', 'invalid', array(), null, array(200, 'V2/LeadingReferring/404.json')),
            array(2, 'LeadingReferring', 'XML',  'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'sites' => array(
                        'v9.com',
                        '22find.com',
                        '22apple.com',
                        'mozilla.org',
                        'wp.pl',
                        'starterek.pl',
                        'onet.pl',
                        'allegro.pl',
                        'start24.pl',
                        'interia.pl',
                        ),
                    ),
                null, array(200, 'V2/LeadingReferring/200.xml')),
            array(2, 'LeadingReferring', 'XML',  'invalid', array(), null, array(200, 'V2/LeadingReferring/404.xml')),
            array(2, 'LeadingReferring', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- SEARCH INTELLIGENCE ------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'SearchIntelligence', 'JSON', 'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'organicSearchShare' => 0.999855885567263,
                    'paidSearchShare' => 0.000144114432736875,
                    'topOrganicTerms' => array(
                        'google',
                        'google.pl',
                        'google pl',
                        'www.google.pl',
                        'tłumacz',
                        'tlumacz',
                        'gogle',
                        'tłumacz google',
                        'tlumacz google',
                        'google tłumacz'
                        ),
                    'topPaidTerms' => array(
                        'google',
                        'google street view',
                        'reklama owocach',
                        'www.google.pl',
                        'google pl',
                        'google.pl',
                        'pozycjonowanie stron',
                        'adwords',
                        'pozycjonowanie w google',
                        'google.pl/'
                        ),
                    ),
                null, array(200, 'V2/SearchIntelligence/200.json')),
            array(2, 'SearchIntelligence', 'JSON', 'invalid', array(), null, array(200, 'V2/SearchIntelligence/404.json')),
            array(2, 'SearchIntelligence', 'XML',  'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'organicSearchShare' => 0.999855885567263,
                    'paidSearchShare' => 0.000144114432736875,
                    'topOrganicTerms' => array(
                        'google',
                        'google.pl',
                        'google pl',
                        'www.google.pl',
                        'tłumacz',
                        'tlumacz',
                        'gogle',
                        'tłumacz google',
                        'tlumacz google',
                        'google tłumacz'
                        ),
                    'topPaidTerms' => array(
                        'google',
                        'google street view',
                        'reklama owocach',
                        'www.google.pl',
                        'google pl',
                        'google.pl',
                        'pozycjonowanie stron',
                        'adwords',
                        'pozycjonowanie w google',
                        'google.pl/'
                        ),
                    ),
                null, array(200, 'V2/SearchIntelligence/200.xml')),
            array(2, 'SearchIntelligence', 'XML',  'invalid', array(), null, array(200, 'V2/SearchIntelligence/404.xml')),
            array(2, 'SearchIntelligence', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- SOCIAL REFERRING ---------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'SocialReferring', 'JSON', 'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'socialSources' => array(
                        'Facebook' => 0.582044720352275,
                        'Youtube' => 0.252626360630494,
                        'Nk.pl' => 0.0818276171811314,
                        'Badoo' => 0.0461216725026514,
                        'Ask.fm' => 0.0254566924352841,
                        ),
                    ),
                null, array(200, 'V2/SocialReferring/200.json')),
            array(2, 'SocialReferring', 'JSON', 'invalid', array(), null, array(200, 'V2/SocialReferring/404.json')),
            array(2, 'SocialReferring', 'XML',  'google.pl',
                array(
                    'startDate' => 1356994800,
                    'endDate' => 1362092400,
                    'socialSources' => array(
                        'Facebook' => 0.582044720352275,
                        'Youtube' => 0.252626360630494,
                        'Nk.pl' => 0.0818276171811314,
                        'Badoo' => 0.0461216725026514,
                        'Ask.fm' => 0.0254566924352841,
                        ),
                    ),
                null, array(200, 'V2/SocialReferring/200.xml')),
            array(2, 'SocialReferring', 'XML',  'invalid', array(), null, array(200, 'V2/SocialReferring/404.xml')),
            array(2, 'SocialReferring', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- TRAFFIC ------------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array(2, 'Traffic', 'JSON', 'google.pl',
                array(
                    'date' => 1362092400,
                    'globalRank' => 47,
                    'countryCode' => 616,
                    'countryRank' => 1,
                    'topCountryShares' => array(
                        616 => 0.87201268109186,
                        826 => 0.066084874704297,
                        276 => 0.013280485332793,
                        528 => 0.0077104475468773,
                        372 => 0.0069448717128673,
                        ),
                    'trafficReach' => array(
                        1349042400 => 0.0082713353518225,
                        1349647200 => 0.0083012573763436,
                        1350252000 => 0.0081864439594422,
                        1350856800 => 0.0083550591611191,
                        1351465200 => 0.0084298514559342,
                        1352070000 => 0.0085853301422004,
                        1352674800 => 0.0088959938449278,
                        1353279600 => 0.0090126752420599,
                        1353884400 => 0.0087763225987977,
                        1354489200 => 0.0082619469633513,
                        1355094000 => 0.008111782609877,
                        1355698800 => 0.0079406500975396,
                        1356303600 => 0.0078490379300229,
                        1356908400 => 0.0079690019440621,
                        1357513200 => 0.0079649263715143,
                        1358118000 => 0.0077223565655589,
                        1358722800 => 0.0076907780341072,
                        1359327600 => 0.0076129641516562,
                        1359932400 => 0.0075872309280783,
                        1360537200 => 0.0072768775104841,
                        1361142000 => 0.0072730040305958,
                        1361746800 => 0.0073418670172292,
                        1362351600 => 0.0077368862662028,
                        1362956400 => 0.0077401219480802,
                        1363561200 => 0.0077017983165331,
                        1364166000 => 0.0076309302669743,
                        ),
                    'trafficShares' => array(
                        'Search' => 0.031281117150751,
                        'Social' => 0.0058172151352293,
                        'Mail' => 0.0087514759729459,
                        'Paid Referrals' => 0.00013150485574774,
                        'Direct' => 0.89573161858738,
                        'Referrals' => 0.058287068297944,
                        ),
                    ),
                null, array(200, 'V2/Traffic/200.json')),
            array(2, 'Traffic', 'JSON', 'invalid', array(), null, array(200, 'V2/Traffic/404.json')),
            array(2, 'Traffic', 'XML',  'google.pl',
                array(
                    'date' => 1362092400,
                    'globalRank' => 47,
                    'countryCode' => 616,
                    'countryRank' => 1,
                    'topCountryShares' => array(
                        616 => 0.87201268109186,
                        826 => 0.066084874704297,
                        276 => 0.013280485332793,
                        528 => 0.0077104475468773,
                        372 => 0.0069448717128673,
                        ),
                    'trafficReach' => array(
                        1349042400 => 0.0082713353518225,
                        1349647200 => 0.0083012573763436,
                        1350252000 => 0.0081864439594422,
                        1350856800 => 0.0083550591611191,
                        1351465200 => 0.0084298514559342,
                        1352070000 => 0.0085853301422004,
                        1352674800 => 0.0088959938449278,
                        1353279600 => 0.0090126752420599,
                        1353884400 => 0.0087763225987977,
                        1354489200 => 0.0082619469633513,
                        1355094000 => 0.008111782609877,
                        1355698800 => 0.0079406500975396,
                        1356303600 => 0.0078490379300229,
                        1356908400 => 0.0079690019440621,
                        1357513200 => 0.0079649263715143,
                        1358118000 => 0.0077223565655589,
                        1358722800 => 0.0076907780341072,
                        1359327600 => 0.0076129641516562,
                        1359932400 => 0.0075872309280783,
                        1360537200 => 0.0072768775104841,
                        1361142000 => 0.0072730040305958,
                        1361746800 => 0.0073418670172292,
                        1362351600 => 0.0077368862662028,
                        1362956400 => 0.0077401219480802,
                        1363561200 => 0.0077017983165331,
                        1364166000 => 0.0076309302669743,
                        ),
                    'trafficShares' => array(
                        'Search' => 0.031281117150751,
                        'Social' => 0.0058172151352293,
                        'Mail' => 0.0087514759729459,
                        'Paid Referrals' => 0.00013150485574774,
                        'Direct' => 0.89573161858738,
                        'Referrals' => 0.058287068297944,
                        ),
                    ),
                null, array(200, 'V2/Traffic/200.xml')),
            array(2, 'Traffic', 'XML',  'invalid', array(), null, array(200, 'V2/Traffic/404.xml')),
            array(2, 'Traffic', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

            /* -------------------------------------------------------------- */

            ); // provider array
        }

    /**
     * @dataProvider apiCallsProvider
     */
    public function testApiCalls($version, $call, $format, $domain, $result, $exception, $payload)
        {
        $swMock = $this->getMock('Thunder\SimilarWebApi\Client', array('executeRequest'), array(
            'userKey' => 'da39a3ee5e6b4b0d3255bfef95601890',
            'format' => $format,
            'version' => $version,
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
            array(1, 'GlobalRank', array('response', 'INV'), '', 'InvalidArgumentException'),
            array(1, 'GlobalRank', array('}{', 'JSON'), '', 'InvalidArgumentException'),
            array(1, 'GlobalRank', array('{}', 'JSON'), '', 'RuntimeException'),
            array(1, 'GlobalRank', array('<>', 'XML'), '', 'InvalidArgumentException'),
            array(1, 'GlobalRank', array('<x />', 'XML'), '', 'RuntimeException'),

            array(1, 'CountryRank', array('response', 'INV'), '', 'InvalidArgumentException'),
            array(1, 'CountryRank', array('{}', 'JSON'), '', 'RuntimeException'),
            array(1, 'CountryRank', array('<x />', 'XML'), '', 'RuntimeException'),

            array(1, 'CategoryRank', array('response', 'INV'), '', 'InvalidArgumentException'),

            array(1, 'Tags', array('response', 'INV'), '', 'InvalidArgumentException'),
            array(1, 'SimilarSites', array('response', 'INV'), '', 'InvalidArgumentException'),

            array(1, 'Category', array('response', 'INV'), '', 'InvalidArgumentException'),
            array(1, 'Category', array('{}', 'JSON'), '', 'RuntimeException'),
            array(1, 'Category', array('<x />', 'XML'), '', 'RuntimeException'),

            array(2, 'Category', array('response', 'INV'), '', 'InvalidArgumentException'),
            array(2, 'Category', array('{}', 'JSON'), '', 'RuntimeException'),
            array(2, 'Category', array('<x />', 'XML'), '', 'RuntimeException'),
            );
        }

    /**
     * @dataProvider invalidCallsProvider
     */
    public function testForCoverage($version, $method, array $args, $result, $exception = null)
        {
        $sw = new Client('da39a3ee5e6b4b0d3255bfef95601890', 'JSON', $version);
        if(null !== $exception)
            {
            $this->setExpectedException($exception);
            }
        $className = 'Thunder\SimilarWebApi\\Parser\\V'.$version.'\\'.$method;
        $parser = new $className();
        $actualResult = $parser->parse($args[0], $args[1]);
        $this->assertEquals($result, $actualResult);
        }

    public function testInvalidVersion()
        {
        $this->setExpectedException('InvalidArgumentException');
        $client = new Client('da39a3ee5e6b4b0d3255bfef95601890', 'JSON', 3);
        }

    public function testInvalidParser()
        {
        $client = new Client('da39a3ee5e6b4b0d3255bfef95601890', 'JSON', 2);
        $this->setExpectedException('RuntimeException');
        $client->api('Invalid', 'google.pl', true);
        }

    public function testRealRequest()
        {
        $sw = new Client('da39a3ee5e6b4b0d3255bfef95601890', 'JSON', 1);
        $this->setExpectedException('RuntimeException');
        $actualResult = $sw->api('GlobalRank', 'google.pl');
        }
    }