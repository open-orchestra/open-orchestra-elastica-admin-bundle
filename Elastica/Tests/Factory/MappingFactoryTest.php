<?php

namespace OpenOrchestra\Elastica\Tests\Factory;

use Elastica\Type;
use Elastica\Type\Mapping;
use OpenOrchestra\Elastica\Factory\MappingFactory;
use Phake;

/**
 * Test MappingFactoryTest
 */
class MappingFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MappingFactory
     */
    protected $factory;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->factory = new MappingFactory();
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $type = Phake::mock(Type::CLASS);

        $mapping = $this->factory->create($type);

        $this->assertInstanceOf(Mapping::CLASS, $mapping);
        $this->assertSame($type, $mapping->getType());
    }
}
