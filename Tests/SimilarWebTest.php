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

        /* ------------------------------------------------------------------ */
        /* -- GLOBAL RANK --------------------------------------------------- */
        /* ------------------------------------------------------------------ */

        return array(
            array('GlobalRank', 'JSON', 'google.pl', 388, array(200, <<<EOT
{"Rank":388}
EOT
                )),
            array('GlobalRank', 'JSON', 'invalid', -1, array(404, <<<EOT
{"Message":"Data Not Found"}
EOT
                )),
            array('GlobalRank', 'XML', 'google.pl', 388, array(200, <<<EOT
<GlobalRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <Rank>388</Rank>
</GlobalRankResponse>
EOT
                )),
            array('GlobalRank', 'XML', 'invalid', -1, array(404, <<<EOT
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
            array('CountryRank', 'JSON', 'invalid', array(), array(200, <<<EOT
{"TopCountryRanks":[]}
EOT
                )),
            array('CountryRank', 'XML',  'google.pl',
                array(616 => 1, 826 => 22, 276 => 69, 528 => 54, 840 => 1480),
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
            array('CountryRank', 'XML',  'invalid', array(), array(200, <<<EOT
<CountryRankResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
    <TopCountryRanks />
</CountryRankResponse>
EOT
                )),
            array('CountryRank', 'XML',  'invalid', -1, array(404, '')),
            );
        }

    /**
     * @dataProvider apiCallsProvider
     */
    public function testGlobalRankApiCall($call, $format, $domain, $result, $payload)
        {
        $swMock = $this->getMock('Thunder\Api\SimilarWeb\SimilarWeb', array('executeCurlRequest'), array(
            'userKey' => 'da39a3ee5e6b4b0d3255bfef95601890',
            ));
        $swMock
            ->expects($this->once())
            ->method('executeCurlRequest')
            ->with($swMock->getApiTargetUrl($call, $domain, $format))
            ->will($this->returnValue($payload));
        $actualResult = $swMock->api($call, $domain, $format);
        $this->assertEquals($result, $actualResult);
        }
    }