<?php
namespace Thunder\SimilarWebApi\Tests;

use Symfony\Component\Yaml\Yaml;
use Thunder\SimilarWebApi\Client;
use Thunder\SimilarWebApi\Endpoint;
use Thunder\SimilarWebApi\Response;

class EndpointTest extends \PHPUnit_Framework_TestCase
    {
    /**
     * @dataProvider provideEndpoints
     */
    public function testClientCalls($call, $exception, array $formats, array $valueTests, array $arrayTests, array $mapTests)
        {
        /**
         * @var $clientMock \PHPUnit_Framework_MockObject_MockObject|Client
         */
        $domain = 'google.com';
        $token = sha1('user_key');

        $yaml = Yaml::parse(file_get_contents(__DIR__.'/../mapping.yaml'));
        $endpoint = new Endpoint($call, $yaml[$call]);

        foreach($formats as $format => $file)
            {
            $content = file_get_contents(__DIR__.'/Fixtures/'.$file);
            $format = strtoupper($format);

            $clientMock = $this->getMock('Thunder\\SimilarWebApi\\Client', array('executeCall'), array($token, $format));
            $clientMock
                ::staticExpects($this->at(0))
                ->method('executeCall')
                ->with($endpoint->getPath(), $domain, $format, $token)
                ->will($this->returnValue(array(200, $content)));
            if(null !== $exception)
                {
                $this->setExpectedException($exception);
                }
            $response = $clientMock->getResponse($call, $domain);
            if(null === $exception)
                {
                $this->assertInstanceOf('Thunder\\SimilarWebApi\\Response', $response);
                $this->runResponseTests($response, $valueTests, $arrayTests, $mapTests);
                }
            }
        }

    protected function runResponseTests(Response $response, array $valueTests, array $arrayTests, array $mapTests)
        {
        foreach($valueTests as $key => $value)
            {
            $this->assertEquals($value, $response->getValue($key));
            }
        foreach($arrayTests as $key => $testData)
            {
            foreach($testData as $name => $value)
                {
                if('count' == $name)
                    {
                    $this->assertEquals($value, count($response->getArray($key)));
                    }
                }
            }
        foreach($mapTests as $key => $testData)
            {
            foreach($testData as $name => $value)
                {
                if('count' == $name)
                    {
                    $this->assertEquals($value, count($response->getMap($key)));
                    }
                }
            }
        }

    public function provideEndpoints()
        {
        return array(

            /* --- INVALID TESTS ------------------------------------------- */

            array('Traffic', 'RuntimeException', array('json' => 'invalid/invalid.json'),
                array(),
                array(),
                array(),
                ),
            array('Traffic', 'RuntimeException', array('xml' => 'invalid/invalid.xml'),
                array(),
                array(),
                array(),
                ),

            /* --- V0 TESTS ------------------------------------------------ */

            array('GlobalRank', null,
                array(
                    'json' => 'v0/globalRank/200_google.json',
                    'xml' => 'v0/globalRank/200_google.xml',
                    ),
                array('rank' => 2),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('SimilarSites', null, array(
                    'json' => 'v0/similarSites/200_google.json',
                    'xml' => 'v0/similarSites/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('similarSites' => array('count' => 20)),
                ),

            array('Tagging', null, array(
                    'json' => 'v0/tagging/200_google.json',
                    'xml' => 'v0/tagging/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array(
                    'tags' => array('count' => 10),
                    ),
                ),

            array('WebsiteCategorization', null, array(
                    'json' => 'v0/websiteCategorization/200_google.json',
                    'xml' => 'v0/websiteCategorization/200_google.xml',
                    ),
                array(
                    'category' => 'Internet_and_Telecom/Search_Engine',
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('WebsiteCategoryRank', null, array(
                    'json' => 'v0/websiteCategoryRank/200_google.json',
                    'xml' => 'v0/websiteCategoryRank/200_google.xml',
                    ),
                array(
                    'category' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 1,
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('WebsiteCountryRank', null, array(
                    'json' => 'v0/websiteCountryRanking/200_google.json',
                    'xml' => 'v0/websiteCountryRanking/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array(
                    'topCountryRanks' => array('count' => 5),
                    ),
                ),

            /* --- V1 TESTS ------------------------------------------------ */

            array('Traffic', null,
                array(
                    'json' => 'v1/traffic/200_google.json',
                    'xml' => 'v1/traffic/200_google.xml',
                    ),
                array(
                    'globalRank' => 2,
                    'countryRank' => 1,
                    'countryCode' => 840,
                    ),
                array(/* no arrays */),
                array(
                    'topCountryShares' => array('count' => 228),
                    'trafficReach' => array('count' => 27),
                    'trafficShares' => array('count' => 6),
                    ),
                ),

            array('Engagement', null, array(
                    'json' => 'v1/engagement/200_google.json',
                    'xml' => 'v1/engagement/200_google.xml',
                    ),
                array(
                    'averagePageViews' => 10.131644139915386,
                    'averageTimeOnSite' => 662.9348950744902,
                    'bounceRate' => 0.2576071545711606,
                    'date' => '12/2013',
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('Keywords', null, array(
                    'json' => 'v1/keywords/200_google.json',
                    'xml' => 'v1/keywords/200_google.xml',
                    ),
                array(
                    'organicSearchShare' => 0.991548209408053,
                    'paidSearchShare' => 0.008451790591947006,
                    'startDate' => '10/2013',
                    'endDate' => '12/2013',
                    ),
                array(
                    'topOrganicTerms' => array('count' => 10),
                    'topPaidTerms' => array('count' => 7),
                    ),
                array(/* no maps */),
                ),

            array('SocialReferrals', null, array(
                    'json' => 'v1/socialReferrals/200_google.json',
                    'xml' => 'v1/socialReferrals/200_google.xml',
                    ),
                array(
                    'startDate' => '10/2013',
                    'endDate' => '12/2013',
                    ),
                array(/* no arrays */),
                array(
                    'socialSources' => array('count' => 152),
                    ),
                ),

            /* --- V2 TESTS ------------------------------------------------ */

            array('AdultWebsites', null, array(
                    'json' => 'v2/adultWebsites/200_google.json',
                    'xml' => 'v2/adultWebsites/200_google.xml',
                    ),
                array(
                    'category' => 'Internet_and_Telecom/Search_Engine',
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),
            array('AdultWebsites', null, array(
                    'json' => 'v2/adultWebsites/200_sex.json',
                    'xml' => 'v2/adultWebsites/200_sex.xml',
                    ),
                array(
                    'category' => 'Adult',
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('AlsoVisited', null, array(
                    'json' => 'v2/alsoVisited/200_google.json',
                    'xml' => 'v2/alsoVisited/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array(
                    'alsoVisited' => array('count' => 13),
                    ),
                ),

            array('CategoryRank', null, array(
                    'json' => 'v2/categoryRank/200_google.json',
                    'xml' => 'v2/categoryRank/200_google.xml',
                    ),
                array(
                    'category' => 'Internet_and_Telecom/Search_Engine',
                    'rank' => 1,
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('Destinations', null, array(
                    'json' => 'v2/destinations/200_google.json',
                    'xml' => 'v2/destinations/200_google.xml',
                    ),
                array(
                    'startDate' => '10/2013',
                    'endDate' => '12/2013',
                    ),
                array(
                    'sites' => array('count' => 10),
                    ),
                array(/* no maps */),
                ),

            array('EstimatedVisitors', null, array(
                    'json' => 'v2/estimatedVisitors/200_google.json',
                    'xml' => 'v2/estimatedVisitors/200_google.xml',
                    ),
                array(
                    'estimatedVisitors' => 8788535663,
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('Referrals', null, array(
                    'json' => 'v2/referrals/200_google.json',
                    'xml' => 'v2/referrals/200_google.xml',
                    ),
                array(
                    'startDate' => '10/2013',
                    'endDate' => '12/2013',
                    ),
                array(
                    'sites' => array('count' => 10),
                    ),
                array(/* no maps */),
                ),

            array('SimilarWebsites', null, array(
                    'json' => 'v2/similarWebsites/200_google.json',
                    'xml' => 'v2/similarWebsites/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array(
                    'similarWebsites' => array('count' => 20),
                    ),
                ),

            array('WebsiteCategorization', null, array(
                    'json' => 'v2/websiteCategorization/200_google.json',
                    'xml' => 'v2/websiteCategorization/200_google.xml',
                    ),
                array(
                    'category' => 'Internet_and_Telecom/Search_Engine',
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                ),

            array('WebsiteTags', null, array(
                    'json' => 'v2/websiteTags/200_google.json',
                    'xml' => 'v2/websiteTags/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array(
                    'tags' => array('count' => 10),
                    ),
                ),

            /* --- END TESTS ----------------------------------------------- */

            );
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidFormat()
        {
        $instance = new Endpoint('Traffic', array());
        $instance->getResponse('', 'INVALID');
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoPath()
        {
        $instance = new Endpoint('Traffic', array());
        $instance->getPath();
        }
    }