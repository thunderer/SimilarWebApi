<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\Request\KeywordsOrganicSearch;
use Thunder\SimilarWebApi\Request\MobileApp;
use Thunder\SimilarWebApi\Request\Traffic;
use Thunder\SimilarWebApi\Request\TrafficPro;
use Thunder\SimilarWebApi\Tests\Dummy\SampleRequest;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidDomain()
    {
        $this->setExpectedException('InvalidArgumentException');
        new Traffic('');
    }

    public function testInvalidApp()
    {
        $this->setExpectedException('InvalidArgumentException');
        new MobileApp(0, '');
    }

    public function testInvalidAppStore()
    {
        $this->setExpectedException('InvalidArgumentException');
        new MobileApp('invalid', 'app.id');
    }

    public function testInvalidStart()
    {
        $this->setExpectedException('InvalidArgumentException');
        new TrafficPro('example.com', 'weekly', 'invalid', '10-2014', true);
    }

    public function testInvalidEnd()
    {
        $this->setExpectedException('InvalidArgumentException');
        new TrafficPro('example.com', 'weekly', '09-2014', 'invalid', true);
    }

    public function testInvalidPeriod()
    {
        $this->setExpectedException('InvalidArgumentException');
        new TrafficPro('example.com', 'invalid', '09-2014', '10-2014', true);
    }

    public function testInvalidPage()
    {
        $this->setExpectedException('InvalidArgumentException');
        new KeywordsOrganicSearch('example.com', '09-2014', '10-2014', true, 'invalid');
    }

    public function testInvalidArg()
    {
        $this->setExpectedException('InvalidArgumentException');
        new SampleRequest();
    }
}
