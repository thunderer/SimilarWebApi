<?php
namespace Thunder\SimilarWebApi\Tests;

use Thunder\SimilarWebApi\RawResponse;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $instance = new RawResponse('', array(), array(), array(), array());
        $this->assertInstanceOf('Thunder\SimilarWebApi\RawResponse', $instance);
    }

    public function testMaps()
    {
        $instance = new RawResponse('', array(), array(), array(
            'one' => array('a' => 'b', 'c' => 'd', 'e' => 'f'),
            'two' => array('a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h'),
        ), array());
        $this->assertEquals(2, count($instance->getMaps()));
        $this->assertEquals(3, count($instance->getMap('one')));
        $this->assertEquals(4, count($instance->getMap('two')));
        $this->assertEquals('b', $instance->getMapValue('one', 'a'));
        $this->assertEquals('a', $instance->getMapKey('one', 'b'));
    }

    public function testArrays()
    {
        $instance = new RawResponse('', array(), array(
            'one' => array(1),
            'two' => array(2, 2),
            'three' => array(3, 3, 3),
        ), array(), array());
        $this->assertEquals(3, count($instance->getArrays()));
        $this->assertEquals(1, count($instance->getArray('one')));
        $this->assertEquals(2, count($instance->getArray('two')));
        $this->assertEquals(3, count($instance->getArray('three')));
    }

    public function testValues()
    {
        $instance = new RawResponse('', array(
            'one' => 'oneValue',
            'two' => 'twoValue',
            'three' => 3,
        ), array(), array(), array());
        $this->assertEquals(3, count($instance->getValues()));
        $this->assertEquals('oneValue', $instance->getValue('one'));
        $this->assertEquals('twoValue', $instance->getValue('two'));
        $this->assertEquals(3, $instance->getValue('three'));
    }

    public function testTuples()
    {
        $instance = new RawResponse('', array(), array(), array(), array(
            'xxx' => array('first' => 'one', 'second' => 'two'),
            'yyy' => array('third' => 'three', 'fourth' => 'four', 'fifth' => 'five'),
        ));
        $this->assertEquals(2, count($instance->getTuples()));
        $this->assertEquals(2, count($instance->getTuple('xxx')));
        $this->assertEquals(3, count($instance->getTuple('yyy')));
    }

    public function testRaw()
    {
        $instance = new RawResponse('response', array(), array(), array(), array());
        $this->assertEquals('response', $instance->getRaw());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapNotFound()
    {
        $instance = new RawResponse('', array(), array(), array(), array());
        $instance->getMap('invalid');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testArrayNotFound()
    {
        $instance = new RawResponse('', array(), array(), array(), array());
        $instance->getArray('invalid');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testValueNotFound()
    {
        $instance = new RawResponse('', array(), array(), array(), array());
        $instance->getValue('invalid');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTupleNotFound()
    {
        $instance = new RawResponse('', array(), array(), array(), array());
        $instance->getTuple('invalid');
    }
}
