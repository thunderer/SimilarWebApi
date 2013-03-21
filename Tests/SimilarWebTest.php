<?php
namespace Thunder\Api\SimilarWeb\Tests;

use Thunder\Api\SimilarWeb\SimilarWeb;

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
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890');
        $this->assertInstanceOf('Thunder\Api\SimilarWeb\SimilarWeb', $sw);
        }

    public function testDefaultResponseFormatHandling()
        {
        $testUserKey = 'da39a3ee5e6b4b0d3255bfef95601890';
        $sw = new SimilarWeb($testUserKey);
        $reflectionObject = new \ReflectionObject($sw);
        $defaultFormat = $reflectionObject->getProperty('format');
        $defaultFormat->setAccessible(true);
        $this->assertEquals('JSON', $defaultFormat->getValue($sw));
        $sw = new SimilarWeb($testUserKey, 'XML');
        $this->assertEquals('XML', $defaultFormat->getValue($sw));
        $this->setExpectedException('InvalidArgumentException');
        $sw = new SimilarWeb($testUserKey, 'INVALID');
        }

    public function testUserKeyHandling()
        {
        $testUserKey = 'da39a3ee5e6b4b0d3255bfef95601890';
        $sw = new SimilarWeb($testUserKey);
        $reflectionObject = new \ReflectionObject($sw);
        $userKey = $reflectionObject->getProperty('userKey');
        $userKey->setAccessible(true);
        $this->assertEquals($testUserKey, $userKey->getValue($sw));
        $anotherUserKey = 'da39a3ee5e6b4b0d3255bfef95601891';
        $sw = new SimilarWeb($anotherUserKey);
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
                $sw = new SimilarWeb($key);
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

        /* ------------------------------------------------------------------ */
        /* -- GLOBAL RANK --------------------------------------------------- */
        /* ------------------------------------------------------------------ */

        return array( /* #0 */
            array('GlobalRank', 'JSON', 'google.pl', 388, null, array(200, <<<EOT
{"Rank":388}
EOT
                )),
            array('GlobalRank', 'JSON', 'invalid', 'exception', 'RuntimeException', array(404, <<<EOT
{"Message":"Data Not Found"}
EOT
                )),
            array('GlobalRank', 'XML', 'google.pl', 388, null, array(200, <<<EOT
<GlobalRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Rank>388</Rank>
</GlobalRankResponse>
EOT
                )),
            array('GlobalRank', 'XML', 'invalid', 'exception', 'RuntimeException', array(404, <<<EOT
<Error>
    <Message>Data Not Found</Message>
</Error>
EOT
                )),

        /* ------------------------------------------------------------------ */
        /* -- COUNTRY RANK -------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array('CountryRank', 'JSON', 'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
                null,
                array(200, <<<EOT
{
 "TopCountryRanks": [
  {
   "Code": 616,
   "Rank": 1
  },
  {
   "Code": 826,
   "Rank": 22
  },
  {
   "Code": 276,
   "Rank": 69
  },
  {
   "Code": 528,
   "Rank": 54
  },
  {
   "Code": 840,
   "Rank": 1480
  }
 ]
}
EOT
                )),
            array('CountryRank', 'JSON', 'invalid', array(), null, array(200, <<<EOT
{"TopCountryRanks":[]}
EOT
                )),
            array('CountryRank', 'XML',  'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
                null,
                array(200, <<<EOT
<CountryRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <TopCountryRanks>
        <CountryRank>
            <Code>616</Code>
            <Rank>1</Rank>
        </CountryRank>
        <CountryRank>
            <Code>826</Code>
            <Rank>22</Rank>
        </CountryRank>
        <CountryRank>
            <Code>276</Code>
            <Rank>69</Rank>
        </CountryRank>
        <CountryRank>
            <Code>528</Code>
            <Rank>54</Rank>
        </CountryRank>
        <CountryRank>
            <Code>840</Code>
            <Rank>1480</Rank>
        </CountryRank>
    </TopCountryRanks>
</CountryRankResponse>
EOT
                )),
            array('CountryRank', 'XML',  'invalid', array(), null, array(200, <<<EOT
<CountryRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <TopCountryRanks />
</CountryRankResponse>
EOT
                )),
            array('CountryRank', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY RANK ------------------------------------------------- */
        /* ------------------------------------------------------------------ */

            array('CategoryRank', 'JSON', 'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 20,
                    ),
                null,
            array(200, <<<EOT
{
 "Category": "Internet_and_Telecom/Search_Engine",
 "CategoryRank": 20
}
EOT
                )),
            array('CategoryRank', 'JSON', 'invalid', -1, null, array(200, <<<EOT
{"Category":"","CategoryRank":0}
EOT
                )),
            array('CategoryRank', 'XML',  'google.pl',
                array(
                    'name' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 20,
                    ),
                null,
                array(200, <<<EOT
<CategoryRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Category>Internet_and_Telecom/Search_Engine</Category>
    <CategoryRank>20</CategoryRank>
</CategoryRankResponse>
EOT
                )),
            array('CategoryRank', 'XML',  'invalid', -1, null, array(200, <<<EOT
<CategoryRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Category/>
    <CategoryRank>0</CategoryRank>
</CategoryRankResponse>
EOT
                )),
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
                null,
                array(200, <<<EOT
{
 "Tags": [
  {
   "Name": "google",
   "Score": 0.812606952660115
  },
  {
   "Name": "search",
   "Score": 0.28651044373034
  },
  {
   "Name": "folder zakładek osobistych",
   "Score": 0.252063426499681
  },
  {
   "Name": "wyszukiwarka",
   "Score": 0.190362443330678
  },
  {
   "Name": "mobilne zakładki",
   "Score": 0.169491699143677
  },
  {
   "Name": "nazwa folderu",
   "Score": 0.169491699143677
  },
  {
   "Name": "wyszukiwarki",
   "Score": 0.15017253537674
  },
  {
   "Name": "internet",
   "Score": 0.136542773364803
  },
  {
   "Name": "wyszukiwanie",
   "Score": 0.0970751723399089
  },
  {
   "Name": "z internet explorer",
   "Score": 0.0889742436821045
  }
 ]
}
EOT
                )),
            array('Tags', 'JSON', 'invalid', array(), null, array(200, <<<EOT
{"Tags":[]}
EOT
                )),
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
                null,
                array(200, <<<EOT
<TagsResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Tags>
        <Tag>
            <Name>google</Name>
            <Score>0.812606952660115</Score>
        </Tag>
        <Tag>
            <Name>search</Name>
            <Score>0.28651044373034</Score>
        </Tag>
        <Tag>
            <Name>folder zakładek osobistych</Name>
            <Score>0.252063426499681</Score>
        </Tag>
        <Tag>
            <Name>wyszukiwarka</Name>
            <Score>0.190362443330678</Score>
        </Tag>
        <Tag>
            <Name>mobilne zakładki</Name>
            <Score>0.169491699143677</Score>
        </Tag>
        <Tag>
            <Name>nazwa folderu</Name>
            <Score>0.169491699143677</Score>
        </Tag>
        <Tag>
            <Name>wyszukiwarki</Name>
            <Score>0.15017253537674</Score>
        </Tag>
        <Tag>
            <Name>internet</Name>
            <Score>0.136542773364803</Score>
        </Tag>
        <Tag>
            <Name>wyszukiwanie</Name>
            <Score>0.0970751723399089</Score>
        </Tag>
        <Tag>
            <Name>z internet explorer</Name>
            <Score>0.0889742436821045</Score>
        </Tag>
    </Tags>
</TagsResponse>
EOT
                )),
            array('Tags', 'XML', 'invalid', array(), null, array(200, <<<EOT
<TagsResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Tags/>
</TagsResponse>
EOT
                )),
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
                null,
                array(200, <<<EOT
{
 "SimilarSites": [
  {
   "Url": "onet.pl",
   "Score": 0.616367516648356
  },
  {
   "Url": "o2.pl",
   "Score": 0.605925990843291
  },
  {
   "Url": "wp.pl",
   "Score": 0.582487427512684
  },
  {
   "Url": "allegro.pl",
   "Score": 0.580657318268566
  },
  {
   "Url": "searchgi.com",
   "Score": 0.560548224555051
  },
  {
   "Url": "interia.pl",
   "Score": 0.555901801001014
  },
  {
   "Url": "netsprint.pl",
   "Score": 0.450380462720214
  },
  {
   "Url": "pl.wikipedia.org",
   "Score": 0.446336590836237
  },
  {
   "Url": "nk.pl",
   "Score": 0.426157149302767
  },
  {
   "Url": "szukacz.pl",
   "Score": 0.401158531650336
  },
  {
   "Url": "jabago.com",
   "Score": 0.400747619584497
  },
  {
   "Url": "noamok.de",
   "Score": 0.397139587735766
  },
  {
   "Url": "gry.pl",
   "Score": 0.394227119296143
  },
  {
   "Url": "gcity.pl",
   "Score": 0.39160146821524
  },
  {
   "Url": "search.conduit.com",
   "Score": 0.390573005327159
  },
  {
   "Url": "googleblog.blogspot.com",
   "Score": 0.383130831389041
  },
  {
   "Url": "wyszukiwarka-chomikuj.pl",
   "Score": 0.373626321656848
  },
  {
   "Url": "nasza-klasa.pl/login",
   "Score": 0.365503960418973
  },
  {
   "Url": "zumi.pl",
   "Score": 0.360514900962929
  },
  {
   "Url": "tlen.pl",
   "Score": 0.360481398718276
  }
 ]
}
EOT
                )),
            array('SimilarSites', 'JSON', 'invalid', array(), null, array(200, <<<EOT
{"SimilarSites":[]}
EOT
                )),
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
                array(200, <<<EOT
<SimilarSitesResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <SimilarSites>
        <SimilarSite>
            <Score>0.616367516648356</Score>
            <Url>onet.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.605925990843291</Score>
            <Url>o2.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.582487427512684</Score>
            <Url>wp.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.580657318268566</Score>
            <Url>allegro.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.560548224555051</Score>
            <Url>searchgi.com</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.555901801001014</Score>
            <Url>interia.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.450380462720214</Score>
            <Url>netsprint.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.446336590836237</Score>
            <Url>pl.wikipedia.org</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.426157149302767</Score>
            <Url>nk.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.401158531650336</Score>
            <Url>szukacz.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.400747619584497</Score>
            <Url>jabago.com</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.397139587735766</Score>
            <Url>noamok.de</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.394227119296143</Score>
            <Url>gry.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.39160146821524</Score>
            <Url>gcity.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.390573005327159</Score>
            <Url>search.conduit.com</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.383130831389041</Score>
            <Url>googleblog.blogspot.com</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.373626321656848</Score>
            <Url>wyszukiwarka-chomikuj.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.365503960418973</Score>
            <Url>nasza-klasa.pl/login</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.360514900962929</Score>
            <Url>zumi.pl</Url>
        </SimilarSite>
        <SimilarSite>
            <Score>0.360481398718276</Score>
            <Url>tlen.pl</Url>
        </SimilarSite>
    </SimilarSites>
</SimilarSitesResponse>
EOT
                )),
            array('SimilarSites', 'XML',  'invalid', array(), null, array(200, <<<EOT
<CategoryRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Category/>
    <CategoryRank>0</CategoryRank>
</CategoryRankResponse>
EOT
                )),
            array('SimilarSites', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

        /* ------------------------------------------------------------------ */
        /* -- CATEGORY ------------------------------------------------------ */
        /* ------------------------------------------------------------------ */

            array('Category', 'JSON', 'google.pl', 'Internet_and_Telecom/Search_Engine', null,
                array(200, <<<EOT
{
 "Category": "Internet_and_Telecom/Search_Engine"
}
EOT
                )),
            array('Category', 'JSON', 'invalid', '', null, array(200, <<<EOT
{"Category":""}
EOT
            )),
            array('Category', 'XML',  'google.pl', 'Internet_and_Telecom/Search_Engine', null,
                array(200, <<<EOT
<CategoryResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Category>Internet_and_Telecom/Search_Engine</Category>
</CategoryResponse>
EOT
                )),
            array('Category', 'XML',  'invalid', '', null, array(200, <<<EOT
<CategoryResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Category/>
</CategoryResponse>
EOT
            )),
            array('Category', 'XML',  'invalid', 'exception', 'RuntimeException', array(404, '')),

            array('Category', 'XML',  'invalid', 'exception', 'RuntimeException', array(200, 'xxx')),
            array('Category', 'JSON',  'invalid', 'exception', 'RuntimeException', array(200, '}{')),

            );
        }

    /**
     * @dataProvider apiCallsProvider
     */
    public function testApiCalls($call, $format, $domain, $result, $exception, $payload)
        {
        $swMock = $this->getMock('Thunder\Api\SimilarWeb\SimilarWeb', array('executeCurlRequest'), array(
            'userKey' => 'da39a3ee5e6b4b0d3255bfef95601890',
            'format' => $format,
            ));
        $swMock
            ->expects($this->once())
            ->method('executeCurlRequest')
            ->with($swMock->getUrlTarget($call, $domain, $format))
            ->will($this->returnValue($payload));
        if('exception' == $result)
            {
            $this->setExpectedException($exception);
            }
        $actualResult = $swMock->api($call, $domain, $format);
        if('exception' != $result)
            {
            $this->assertEquals($result, $actualResult);
            }
        }

    public function invalidCallsProvider()
        {
        return array(
            array('parseGlobalRankResponse', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('parseCountryRankResponse', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('parseCategoryRankResponse', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('parseTagsResponse', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('parseSimilarSitesResponse', array('response', 'INV'), '', 'InvalidArgumentException'),
            array('parseCategoryResponse', array('response', 'INV'), '', 'InvalidArgumentException'),

            array('api', array('GlobalRank', 'invalid', null), '', 'RuntimeException'),
            array('api', array('GlobalRank', 'google.pl', 'INV'), '', 'RuntimeException'),
            array('api', array('Invalid', 'google.pl', 'JSON'), '', 'InvalidArgumentException'),
            );
        }

    /**
     * @dataProvider invalidCallsProvider
     */
    public function testForCoverage($method, array $args, $result, $exception = null)
        {
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890', 'JSON');
        if(null !== $exception)
            {
            $this->setExpectedException($exception);
            }
        $reflectionClass = new \ReflectionClass(get_class($sw));
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);
        $actualResult = $reflectionMethod->invokeArgs($sw, $args);
        $this->assertEquals($result, $actualResult);
        }

    public function testCountryData()
        {
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890');
        $data = $sw->getCountryData(616);
        $this->assertEquals(array(
            'continent' => 'EU',
            'twoLetter' => 'PL',
            'threeLetter' => 'POL',
            'numeric' => 616,
            'name' => 'Poland, Republic of',
            ), $data);
        $this->assertEquals($sw->getCountryData(null), $sw->getCountryData(null));
        }

    public function testInvalidCall()
        {
        $swMock = $this->getMock('Thunder\Api\SimilarWeb\SimilarWeb', array('executeCurlRequest'), array(
            'userKey' => 'da39a3ee5e6b4b0d3255bfef95601890',
            ));
        $swMock
            ->expects($this->once())
            ->method('executeCurlRequest')
            ->with($swMock->getUrlTarget('Invalid', 'google.pl', 'JSON'))
            ->will($this->returnValue(array(200, '')));
        $reflectionObject = new \ReflectionObject($swMock);
        $reflectionProperty = $reflectionObject->getProperty('validCalls');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($swMock, array('Invalid'));
        $this->setExpectedException('RuntimeException');
        $swMock->api('Invalid', 'google.pl');
        }
    }