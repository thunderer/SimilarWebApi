<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\RawResponse;

class ResponseTest extends \PHPUnit_Framework_TestCase
    {
    public function testInstance()
        {
        $instance = RawResponse::create('', array(), array(), array());
        $this->assertInstanceOf('Thunder\SimilarWebApi\RawResponse', $instance);
        }

    public function testMaps()
        {
        $instance = RawResponse::create('', array(), array(), array(
            'one' => array('a' => 'b', 'c' => 'd', 'e' => 'f'),
            'two' => array('a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h'),
            ));
        $this->assertEquals(2, count($instance->getMaps()));
        $this->assertEquals(3, count($instance->getMap('one')));
        $this->assertEquals(4, count($instance->getMap('two')));
        $this->assertEquals('b', $instance->getMapValue('one', 'a'));
        $this->assertEquals('a', $instance->getMapKey('one', 'b'));
        }

    public function testArrays()
        {
        $instance = RawResponse::create('', array(), array(
            'one' => array(1),
            'two' => array(2, 2),
            'three' => array(3, 3, 3),
            ), array());
        $this->assertEquals(3, count($instance->getArrays()));
        $this->assertEquals(1, count($instance->getArray('one')));
        $this->assertEquals(2, count($instance->getArray('two')));
        $this->assertEquals(3, count($instance->getArray('three')));
        }

    public function testValues()
        {
        $instance = RawResponse::create('', array(
            'one' => 'oneValue',
            'two' => 'twoValue',
            'three' => 3,
            ), array(), array());
        $this->assertEquals(3, count($instance->getValues()));
        $this->assertEquals('oneValue', $instance->getValue('one'));
        $this->assertEquals('twoValue', $instance->getValue('two'));
        $this->assertEquals(3, $instance->getValue('three'));
        }

    public function testRaw()
        {
        $instance = RawResponse::create('response', array(), array(), array());
        $this->assertEquals('response', $instance->getRaw());
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapNotFound()
        {
        $instance = RawResponse::create('', array(), array(), array());
        $instance->getMap('invalid');
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testArrayNotFound()
        {
        $instance = RawResponse::create('', array(), array(), array());
        $instance->getArray('invalid');
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testValueNotFound()
        {
        $instance = RawResponse::create('', array(), array(), array());
        $instance->getValue('invalid');
        }
    }