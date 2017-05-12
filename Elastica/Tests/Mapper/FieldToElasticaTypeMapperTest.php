<?php

namespace OpenOrchestra\Elastica\Tests\Mapper;

use OpenOrchestra\Elastica\Mapper\FieldToElasticaTypeMapper;

/**
 * Test FieldToElasticaTypeMapperTest
 */
class FieldToElasticaTypeMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldToElasticaTypeMapper
     */
    protected $mapper;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->mapper = new FieldToElasticaTypeMapper();
    }

    /**
     * @param string $formType
     *
     * @dataProvider provideType
     */
    public function testMapWithNoConfiguration($formType)
    {
        $this->assertSame('string', $this->mapper->map($formType));
    }
    
    /**
     * @return array
     */
    public function provideType()
    {
        return array(
            array('foo'),
            array('bar')
        );
    }

    /**
     * @param string $type
     *
     * @dataProvider provideType
     */
    public function testMapWithConfigurations($type)
    {
        $this->mapper->addMappingConfiguration($type, $type);

        $this->assertSame($type, $this->mapper->map($type));
    }

    /**
     * @param string $type
     *
     * @dataProvider provideType
     */
    public function testMapWithConfigurationNotFound($type)
    {
        $this->mapper->addMappingConfiguration($type, $type);

        $this->assertSame('string', $this->mapper->map('baz'));
    }
}
