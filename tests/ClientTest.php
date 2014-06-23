<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
    {
    public function testInstance()
        {
        $instance = new Client(sha1('user_key'), 'XML');
        $this->assertInstanceOf('Thunder\SimilarWebApi\Client', $instance);
        }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidInstance()
        {
        new Client(sha1('user_key'), 'INVALID');
        }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCall()
        {
        $instance = new Client(sha1('user_key'), 'JSON');
        $instance->getResponse('INVALID/ENDPOINT', 'DOMAIN');
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testCallFailed()
        {
        /**
         * @var $clientMock \PHPUnit_Framework_MockObject_MockObject|Client
         */
        $clientClass = 'Thunder\\SimilarWebApi\\Client';
        $token = sha1('user_key');
        $format = 'JSON';

        $clientMock = $this->getMock($clientClass, array('executeCall'), array($token, $format));
        $clientMock
            ->expects($this->at(0))
            ->method('executeCall')
            ->with('v1/traffic', 'google.com', $format, $token)
            ->will($this->returnValue(array(500, '')));
        $clientMock->getResponse('Traffic', 'google.com');
        }
    }