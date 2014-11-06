<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\Client;
use Thunder\SimilarWebApi\Parser\JsonParser;
use Thunder\SimilarWebApi\AbstractRequest;
use Thunder\SimilarWebApi\AbstractResponse;
use Thunder\SimilarWebApi\Parser\XmlParser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class ParserTest extends \PHPUnit_Framework_TestCase
    {
    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionWhenResponseClassDoesNotExist()
        {
        $endpoint = new JsonParser('Invalid', array());
        $endpoint->getResponse('{"content":""}');
        }

    /**
     * @dataProvider provideEndpoints
     */
    public function testClientCalls($call, $exception, array $formats,
                                    array $valueTests, array $arrayTests,
                                    array $mapTests, array $tupleTests)
        {
        $token = sha1('user_key');
        $clientClass = 'Thunder\\SimilarWebApi\\Client';
        $args = array(
            'domain' => 'google.com',
            'app' => 'com.google.app.id',
            'page' => 1,
            'period' => 'weekly',
            'start' => '08-2014',
            'end' => '09-2014',
            'main' => 'true',
            'store' => '0',
            );

        foreach($formats as $format => $file)
            {
            $content = file_get_contents(__DIR__.'/Fixtures/'.$file);
            $format = strtoupper($format);

            $requestClass = 'Thunder\\SimilarWebApi\\Request\\'.$call;
            $reflectionClass = new \ReflectionClass($requestClass);
            $ctorArgs = array_map(function(\ReflectionParameter $parameter) use($args) {
                return $args[$parameter->name];
                }, $reflectionClass->getConstructor()->getParameters());
            /** @var $request AbstractRequest */
            $request = $reflectionClass->newInstanceArgs($ctorArgs);
            $this->assertEquals($requestClass, get_class($request));

            /** @var $clientMock \PHPUnit_Framework_MockObject_MockObject */
            $clientMock = $this->getMock($clientClass, array('executeCall'), array($token, $format));
            $clientMock
                ->expects($this->at(0))
                ->method('executeCall')
                ->with($request->getCallUrl($format, $token))
                ->will($this->returnValue($content));
            if(null !== $exception)
                {
                $this->setExpectedException($exception);
                }

            /** @var $clientMock Client */
            $response = $clientMock->getResponse($request);
            $cachedResponse = $clientMock->getResponse($request);
            if(null === $exception)
                {
                $this->assertTrue($response === $cachedResponse);
                $this->assertInstanceOf('Thunder\\SimilarWebApi\\AbstractResponse', $response);
                $this->assertInstanceOf('Thunder\\SimilarWebApi\\RawResponse', $response->getRawResponse());
                $this->runResponseTests($response, $valueTests, $arrayTests, $mapTests, $tupleTests);
                }
            }
        }

    /**
     * THIS TEST VERIFIES LIBRARY BEHAVIOR USING ACTUAL REQUESTS TO SIMILARWEB
     * API WITH YOUR API KEY. YES, EXACTLY THE SAME YOU PAID FOR WITH YOUR
     * PRECIOUS MONEY. IF YOU WANT TO RUN IT, UNCOMMENT METHOD BELOW AND SET
     * $token VARIABLE TO VALUE RECEIVED FROM SIMILARWEB DEVELOPER PANEL.
     * ALL CURRENTLY IMPLEMENTED CALLS IN THIS TEST USE ABOUT > 80 < API HITS.
     *
     *                       <<< YOU HAVE BEEN WARNED >>>
     *
     * @dataProvider provideEndpoints
     */
    /* public function testRealClientCalls($call, $exception, array $formats,
                                    array $valueTests, array $arrayTests,
                                    array $mapTests, array $tupleTests)
        {
        $token = 'YOUR_API_TOKEN_HERE';

        $args = array(
            'domain' => 'google.com',
            'app' => 'com.google.android.gm',
            'page' => 1,
            'period' => 'weekly',
            'start' => '08-2014',
            'end' => '09-2014',
            'main' => 'true',
            'store' => '0',
            );

        if($exception !== null) { return; }
        foreach($formats as $format => $file)
            {
            $format = strtoupper($format);
            $client = new Client($token, $format);

            $requestClass = 'Thunder\\SimilarWebApi\\Request\\'.$call;
            $reflectionClass = new \ReflectionClass($requestClass);
            $ctorArgs = array_map(function(\ReflectionParameter $parameter) use($args) {
                return $args[$parameter->getName()];
                }, $reflectionClass->getConstructor()->getParameters());
            $request = $reflectionClass->newInstanceArgs($ctorArgs);
            $this->assertEquals($requestClass, get_class($request));

            $response = $client->getResponse($request);
            $cachedResponse = $client->getResponse($request);
            $this->assertEmpty(array_diff(array_keys($valueTests), array_keys($response->getRawResponse()->getValues())));
            $this->assertEmpty(array_diff(array_keys($arrayTests), array_keys($response->getRawResponse()->getArrays())));
            $this->assertEmpty(array_diff(array_keys($mapTests), array_keys($response->getRawResponse()->getMaps())));
            $this->assertEmpty(array_diff(array_keys($tupleTests), array_keys($response->getRawResponse()->getTuples())));

            $this->assertTrue($response === $cachedResponse);
            $this->assertInstanceOf('Thunder\\SimilarWebApi\\AbstractResponse', $response);
            $this->assertInstanceOf('Thunder\\SimilarWebApi\\RawResponse', $response->getRawResponse());
            }
        } */

    protected function runResponseTests(AbstractResponse $response, array $valueTests, array $arrayTests, array $mapTests, array $tupleTests)
        {
        $rawResponse = $response->getRawResponse();
        foreach($valueTests as $key => $value)
            {
            $this->assertEquals($value, $rawResponse->getValue($key));
            $call = call_user_func_array(array($response, 'get'.ucfirst($key)), array());
            $this->assertEquals($value, $call, var_export($value, true).' === '.var_export($call, true));
            }
        foreach($arrayTests as $key => $testData)
            {
            foreach($testData as $name => $value)
                {
                if('count' == $name)
                    {
                    $this->assertEquals($value, count($rawResponse->getArray($key)));
                    $call = call_user_func_array(array($response, 'get'.ucfirst($key)), array());
                    $this->assertEquals($value, count($call),
                        var_export($value, true).' === '.var_export(count($call), true));
                    }
                }
            }
        foreach($mapTests as $key => $testData)
            {
            foreach($testData as $name => $value)
                {
                if('count' == $name)
                    {
                    $this->assertEquals($value, count($rawResponse->getMap($key)),
                        $key.' '.var_export($value, true).' === '.var_export(count($rawResponse->getMap($key)), true));
                    $call = call_user_func_array(array($response, 'get'.ucfirst($key)), array());
                    $this->assertEquals($value, count($call),
                        var_export($value, true).' === '.var_export(count($call), true));
                    }
                }
            }
        foreach($tupleTests as $key => $testData)
            {
            foreach($testData as $name => $value)
                {
                if('count' == $name)
                    {
                    $this->assertEquals($value, count($rawResponse->getTuple($key)));
                    $method = 'get'.ucfirst($key);
                    $this->assertTrue(method_exists($response, $method), sprintf('undefined method %s::%s', get_class($response), $method));
                    $call = call_user_func_array(array($response, $method), array());
                    $this->assertEquals($value, count($call),
                        var_export($value, true).' === '.var_export(count($call), true));
                    }
                }
            }
        }

    public function provideEndpoints()
        {
        $items = array(

            /* --- INVALID TESTS ------------------------------------------- */

            array('Traffic', 'RuntimeException', array('json' => 'invalid/invalid.json'),
                array(), array(), array(), array()),
            array('Traffic', 'RuntimeException', array('xml' => 'invalid/invalid.xml'),
                array(), array(), array(), array()),

            /* --- V0 TESTS ------------------------------------------------ */

            array('GlobalRank', null,
                array(
                    'json' => 'v0/globalRank/200_google.json',
                    'xml' => 'v0/globalRank/200_google.xml',
                    ),
                array('rank' => 2),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),

            array('SimilarSites', null, array(
                    'json' => 'v0/similarSites/200_google.json',
                    'xml' => 'v0/similarSites/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('similarSites' => array('count' => 20)),
                array(/* no tuples */),
                ),

            array('Tagging', null, array(
                    'json' => 'v0/tagging/200_google.json',
                    'xml' => 'v0/tagging/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('tags' => array('count' => 10)),
                array(/* no tuples */),
                ),

            array('V0WebsiteCategorization', null, array(
                    'json' => 'v0/websiteCategorization/200_google.json',
                    'xml' => 'v0/websiteCategorization/200_google.xml',
                    ),
                array('category' => 'Internet_and_Telecom/Search_Engine'),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
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
                array(/* no tuples */),
                ),

            array('WebsiteCountryRank', null, array(
                    'json' => 'v0/websiteCountryRanking/200_google.json',
                    'xml' => 'v0/websiteCountryRanking/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('topCountryRanks' => array('count' => 5)),
                array(/* no tuples */),
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
                    'date' => '12/2013',
                    ),
                array(/* no arrays */),
                array(
                    'topCountryShares' => array('count' => 228),
                    'trafficReach' => array('count' => 27),
                    'trafficShares' => array('count' => 6),
                    ),
                array(/* no tuples */),
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
                array(/* no tuples */),
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
                array(/* no tuples */),
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
                array('socialSources' => array('count' => 152)),
                array(/* no tuples */),
                ),

            /* --- V2 TESTS ------------------------------------------------ */

            array('AdultWebsites', null, array(
                    'json' => 'v2/adultWebsites/200_google.json',
                    'xml' => 'v2/adultWebsites/200_google.xml',
                    ),
                array('category' => 'Internet_and_Telecom/Search_Engine'),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),
            array('AdultWebsites', null, array(
                    'json' => 'v2/adultWebsites/200_sex.json',
                    'xml' => 'v2/adultWebsites/200_sex.xml',
                    ),
                array('category' => 'Adult'),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),

            array('AlsoVisited', null, array(
                    'json' => 'v2/alsoVisited/200_google.json',
                    'xml' => 'v2/alsoVisited/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('alsoVisited' => array('count' => 13)),
                array(/* no tuples */),
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
                array(/* no tuples */),
                ),

            array('Destinations', null, array(
                    'json' => 'v2/destinations/200_google.json',
                    'xml' => 'v2/destinations/200_google.xml',
                    ),
                array('startDate' => '10/2013', 'endDate' => '12/2013'),
                array('sites' => array('count' => 10)),
                array(/* no maps */),
                array(/* no tuples */),
                ),

            array('EstimatedVisitors', null, array(
                    'json' => 'v2/estimatedVisitors/200_google.json',
                    'xml' => 'v2/estimatedVisitors/200_google.xml',
                    ),
                array('estimatedVisitors' => 8788535663),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),

            array('Referrals', null, array(
                    'json' => 'v2/referrals/200_google.json',
                    'xml' => 'v2/referrals/200_google.xml',
                    ),
                array('startDate' => '10/2013', 'endDate' => '12/2013'),
                array('sites' => array('count' => 10)),
                array(/* no maps */),
                array(/* no tuples */),
                ),

            array('SimilarWebsites', null, array(
                    'json' => 'v2/similarWebsites/200_google.json',
                    'xml' => 'v2/similarWebsites/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('similarWebsites' => array('count' => 20)),
                array(/* no tuples */),
                ),

            array('WebsiteCategorization', null, array(
                    'json' => 'v2/websiteCategorization/200_google.json',
                    'xml' => 'v2/websiteCategorization/200_google.xml',
                    ),
                array('category' => 'Internet_and_Telecom/Search_Engine'),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),

            array('WebsiteTags', null, array(
                    'json' => 'v2/websiteTags/200_google.json',
                    'xml' => 'v2/websiteTags/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('tags' => array('count' => 10)),
                array(/* no tuples */),
                ),

            /* --- V1 PRO TESTS -------------------------------------------- */

            array('TrafficPro', null, array(
                    'json' => 'v1pro/traffic/200_google.json',
                    'xml' => 'v1pro/traffic/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('values' => array('count' => 7)),
                array(/* no tuples */),
                ),

            array('EngagementBounceRate', null, array(
                    'json' => 'v1pro/engagement/bouncerate/200_google.json',
                    'xml' => 'v1pro/engagement/bouncerate/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('values' => array('count' => 2)),
                array(/* no tuples */),
                ),
            array('EngagementPageViews', null, array(
                    'json' => 'v1pro/engagement/pageviews/200_google.json',
                    'xml' => 'v1pro/engagement/pageviews/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('values' => array('count' => 2)),
                array(/* no tuples */),
                ),
            array('EngagementVisitDuration', null, array(
                    'json' => 'v1pro/engagement/visitduration/200_google.json',
                    'xml' => 'v1pro/engagement/visitduration/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('values' => array('count' => 2)),
                array(/* no tuples */),
                ),

            array('ReferralsPro', null, array(
                    'json' => 'v1pro/referrals/200_google.json',
                    'xml' => 'v1pro/referrals/200_google.xml',
                    ),
                array('results' => 10, 'total' => 1058),
                array(/* no arrays */),
                array(/* no maps */),
                array('sites' => array('count' => 10)),
                ),

            array('KeywordsOrganicSearch', null, array(
                    'json' => 'v1pro/searchKeywords/organic/200_google.json',
                    'xml' => 'v1pro/searchKeywords/organic/200_google.xml',
                    ),
                array('results' => 10, 'total' => 35125),
                array(/* no arrays */),
                array(/* no maps */),
                array('terms' => array('count' => 10)),
                ),
            array('KeywordsPaidSearch', null, array(
                    'json' => 'v1pro/searchKeywords/paid/200_google.json',
                    'xml' => 'v1pro/searchKeywords/paid/200_google.xml',
                    ),
                array('results' => 10, 'total' => 1412),
                array(/* no arrays */),
                array(/* no maps */),
                array('terms' => array('count' => 10)),
                ),

            array('KeywordCompetitorsOrganic', null, array(
                    'json' => 'v1pro/searchCompetitors/organic/200_google.json',
                    'xml' => 'v1pro/searchCompetitors/organic/200_google.xml',
                    ),
                array('results' => 10, 'total' => 1435),
                array(/* no arrays */),
                array('values' => array('count' => 10)),
                array(/* no tuples */),
                ),
            array('KeywordCompetitorsPaid', null, array(
                    'json' => 'v1pro/searchCompetitors/paid/200_google.json',
                    'xml' => 'v1pro/searchCompetitors/paid/200_google.xml',
                    ),
                array('results' => 10, 'total' => 1680),
                array(/* no arrays */),
                array('values' => array('count' => 10)),
                array(/* no tuples */),
                ),

            /* --- MOBILE TESTS -------------------------------------------- */

            array('MobileApp', null, array(
                    'json' => 'mobile/app/android_gmail.json',
                    'xml' => 'mobile/app/android_gmail.xml',
                    ),
                array(
                    'title' => 'Gmail',
                    'cover' => 'https://lh5.ggpht.com/jVUU0A5NY5EzMqn9AyakWNb0mUHWAkDTjnnamSGqTiEW9FEqnq4CpIEsi-5U2wzo-eYq=w300',
                    'author' => 'Google Inc.',
                    'price' => 'Free',
                    'mainCategory' => 'Communication',
                    'mainCategoryId' => 'communication',
                    'rating' => 4.3047308921813965,
                    ),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),
            array('MobileAppInstalls', null, array(
                    'json' => 'mobile/installs/android_gmail.json',
                    'xml' => 'mobile/installs/android_gmail.xml',
                    ),
                array('min' => 1000000000, 'max' => 5000000000),
                array(/* no arrays */),
                array(/* no maps */),
                array(/* no tuples */),
                ),
            array('MobileRelatedApps', null, array(
                    'json' => 'mobile/relatedApps/200_google.json',
                    'xml' => 'mobile/relatedApps/200_google.xml',
                    ),
                array(/* no values */),
                array(/* no arrays */),
                array('apps' => array('count' => 101)),
                array(/* no tuples */),
                ),

            /* --- END TESTS ----------------------------------------------- */

            );

        return $items;
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidResponseClassJson()
        {
        $instance = new JsonParser('Traffic', array());
        $instance->getResponse('');
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidResponseClassXml()
        {
        $instance = new XmlParser('Traffic', array());
        $instance->getResponse('');
        }
    }
