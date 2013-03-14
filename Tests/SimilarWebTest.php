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
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890');
        $this->assertEquals('JSON', $sw->getDefaultResponseFormat());
        $sw->setDefaultResponseFormat('XML');
        $this->assertEquals('XML', $sw->getDefaultResponseFormat());
        $this->setExpectedException('RuntimeException');
        $sw->setDefaultResponseFormat('INVALID');
        }

    public function testUserKeyHandling()
        {
        $testUserKey = 'da39a3ee5e6b4b0d3255bfef95601890';
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890');
        $this->assertEquals($testUserKey, $sw->getUserKey());
        $anotherUserKey = 'da39a3ee5e6b4b0d3255bfef95601891';
        $sw->setUserKey($anotherUserKey);
        $this->assertEquals($anotherUserKey, $sw->getUserKey());
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
                $sw->setUserKey($key);
                }
            catch(\RuntimeException $e)
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
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890');
        return array(
            array('google.pl', 'GlobalRank', array(200, '{"Rank": 1337}'), 1337),
            array('invalid', 'GlobalRank', array(404, ''), -1),
            );
        }

    /**
     * @dataProvider apiCallsProvider
     */
    public function testGlobalRankApiCall($domain, $call, $payload, $result)
        {
        $sw = new SimilarWeb('da39a3ee5e6b4b0d3255bfef95601890');
        foreach($sw->getSupportedFormats() as $format)
            {
            $swMock = $this->getMock('Thunder\Api\SimilarWeb\SimilarWeb', array('executeCurlRequest'), array(
                'userKey' => 'da39a3ee5e6b4b0d3255bfef95601890',
                ));
            $swMock
                ->expects($this->once())
                ->method('executeCurlRequest')
                ->with($sw->getApiTargetUrl($call, $domain, $format))
                ->will($this->returnValue($payload));
            $actualResult = $swMock->api($call, $domain, $format);
            $this->assertEquals($result, $actualResult);
            }
        }
    }