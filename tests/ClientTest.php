<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\Client;
use Thunder\SimilarWebApi\AbstractRequest;
use Thunder\SimilarWebApi\Request\Traffic;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
    {
    public function testInstance()
        {
        $instance = new Client(sha1('user_key'), 'XML');
        $this->assertInstanceOf('Thunder\SimilarWebApi\Client', $instance);
        }

    public function testInvalidInstance()
        {
        $this->setExpectedException('InvalidArgumentException');
        new Client(sha1('user_key'), 'INVALID');
        }

    public function testCallFailed()
        {
        $this->setExpectedException('RuntimeException');
        /** @var $clientMock \PHPUnit_Framework_MockObject_MockObject|Client */
        $request = new Traffic('google.com');
        $clientMock = $this->getMock('Thunder\\SimilarWebApi\\Client', array('executeCall'), array(sha1('user_key'), 'JSON'));
        $clientMock
            ->expects($this->at(0))
            ->method('executeCall')
            ->with($request->getCallUrl('JSON', sha1('user_key')))
            ->will($this->returnValue(''));
        $clientMock->getResponse($request);
        }

    public function testParserNotFound()
        {
        $this->setExpectedException('RuntimeException');
        $client = new Client('token', 'JSON');
        $reflectionObject = new \ReflectionObject($client);
        $property = $reflectionObject->getProperty('format');
        $property->setAccessible(true);
        $property->setValue($client, 'INVALID');
        $client->getResponse(new Traffic('google.com'));
        }

    /**
     * @dataProvider provideCalls
     */
    public function testCallUrls($call, array $args, $expected)
        {
        $class = 'Thunder\\SimilarWebApi\\Request\\'.$call;
        /** @var $request AbstractRequest */
        $reflectionClass = new \ReflectionClass($class);
        $request = $reflectionClass->newInstanceArgs($args);
        $this->assertEquals($expected, $request->getCallUrl('JSON', 'api_token'));
        }

    public function provideCalls()
        {
        $page = 1;
        $app = 'app.id';
        $domain = 'example.com';
        $api = 'http://api.similarweb.com/Site/'.$domain;
        $mobileApi = 'http://api.similarweb.com/Mobile/0/'.$app;
        $formatToken = 'Format=JSON&UserKey=api_token';
        $startEndMd = 'start=08-2014&end=09-2014&md=true';
        $args = $formatToken.'&'.$startEndMd.'&page='.$page;
        $argsNoPage = $formatToken.'&gr=monthly&'.$startEndMd;

        $domainArgs = array($domain);
        $proPeriodArgs = array($domain, 'monthly', '08-2014', '09-2014', true, $page);
        $proPeriodArgsNoPage = array($domain, 'monthly', '08-2014', '09-2014', true);
        $proNoPeriodArgs = array($domain, '08-2014', '09-2014', true, $page);

        $items = array(
            array('GlobalRank', $domainArgs, $api.'/globalRank?'.$formatToken),
            array('SimilarSites', $domainArgs, $api.'/similarSites?'.$formatToken),
            array('Tagging', $domainArgs, $api.'/tags?'.$formatToken),
            array('V0WebsiteCategorization', $domainArgs, $api.'/category?'.$formatToken),
            array('WebsiteCategoryRank', $domainArgs, $api.'/categoryRank?'.$formatToken),
            array('WebsiteCountryRank', $domainArgs, $api.'/countryRank?'.$formatToken),

            array('Traffic', $domainArgs, $api.'/v1/traffic?'.$formatToken),
            array('Engagement', $domainArgs, $api.'/v1/engagement?'.$formatToken),
            array('Keywords', $domainArgs, $api.'/v1/searchintelligence?'.$formatToken),
            array('SocialReferrals', $domainArgs, $api.'/v1/SocialReferringSites?'.$formatToken),

            array('AdultWebsites', $domainArgs, $api.'/v2/Category?'.$formatToken),
            array('AlsoVisited', $domainArgs, $api.'/v2/AlsoVisited?'.$formatToken),
            array('CategoryRank', $domainArgs, $api.'/v2/CategoryRank?'.$formatToken),
            array('Destinations', $domainArgs, $api.'/v2/leadingdestinationsites?'.$formatToken),
            array('EstimatedVisitors', $domainArgs, $api.'/v2/EstimatedTraffic?'.$formatToken),
            array('Referrals', $domainArgs, $api.'/v2/leadingreferringsites?'.$formatToken),
            array('SimilarWebsites', $domainArgs, $api.'/v2/SimilarSites?'.$formatToken),
            array('WebsiteCategorization', $domainArgs, $api.'/v2/Category?'.$formatToken),
            array('WebsiteTags', $domainArgs, $api.'/v2/Tags?'.$formatToken),

            array('TrafficPro', $proPeriodArgs, $api.'/v1/visits?'.$argsNoPage),
            array('EngagementPageViews', $proPeriodArgsNoPage, $api.'/v1/pageviews?'.$argsNoPage),
            array('EngagementVisitDuration', $proPeriodArgsNoPage, $api.'/v1/visitduration?'.$argsNoPage),
            array('EngagementBounceRate', $proPeriodArgsNoPage, $api.'/v1/bouncerate?'.$argsNoPage),
            array('KeywordsOrganicSearch', $proNoPeriodArgs, $api.'/v1/orgsearch?'.$args),
            array('KeywordsPaidSearch', $proNoPeriodArgs, $api.'/v1/paidsearch?'.$args),
            array('ReferralsPro', $proNoPeriodArgs, $api.'/v1/referrals?'.$args),
            array('KeywordCompetitorsOrganic', $proNoPeriodArgs, $api.'/v1/orgkwcompetitor?'.$args),
            array('KeywordCompetitorsPaid', $proNoPeriodArgs, $api.'/v1/paidkwcompetitor?'.$args),

            array('MobileApp', array(0, $app), $mobileApi.'/v1/GetAppDetails?'.$formatToken),
            array('MobileAppInstalls', array(0, $app), $mobileApi.'/v1/GetAppInstalls?'.$formatToken),
            array('MobileRelatedApps', array(0, $domain), 'http://api.similarweb.com/Mobile/0/'.$domain.'/v1/GetRelatedSiteApps?'.$formatToken),
            );

        return $items;
        }
    }
