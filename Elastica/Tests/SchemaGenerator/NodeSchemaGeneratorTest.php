<?php

namespace OpenOrchestra\Elastica\Tests\SchemaGenerator;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;
use OpenOrchestra\Elastica\Factory\MappingFactory;
use OpenOrchestra\Elastica\SchemaGenerator\ElasticaSchemaGeneratorInterface;
use OpenOrchestra\Elastica\SchemaGenerator\NodeSchemaGenerator;
use Phake;

/**
 * Test NodeSchemaGeneratorTest
 */
class NodeSchemaGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeSchemaGenerator
     */
    protected $schemaGenerator;

    protected $type;
    protected $index;
    protected $client;
    protected $formMapper;
    protected $mappingFactory;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->mappingFactory = Phake::mock(MappingFactory::CLASS);

        $this->type = Phake::mock(Type::CLASS);
        $this->index = Phake::mock(Index::CLASS);
        Phake::when($this->index)->getType(Phake::anyParameters())->thenReturn($this->type);
        $this->client = Phake::mock(Client::CLASS);
        Phake::when($this->client)->getIndex(Phake::anyParameters())->thenReturn($this->index);

        $this->schemaGenerator = new NodeSchemaGenerator($this->client, 'orchestra', $this->mappingFactory);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ElasticaSchemaGeneratorInterface::CLASS, $this->schemaGenerator);
    }

    /**
     * Test create mapping
     */
    public function testCreateMapping()
    {
        $mapping = Phake::mock(Mapping::CLASS);
        Phake::when($this->mappingFactory)->create(Phake::anyParameters())->thenReturn($mapping);

        $this->schemaGenerator->createMapping();

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('node');
        Phake::verify($mapping)->setProperties(array(
            'id' => array('type' => 'string', 'include_in_all' => true),
            'elementId' => array('type' => 'string', 'include_in_all' => true),
            'nodeId' => array('type' => 'string', 'include_in_all' => true),
            'siteId' => array('type' => 'string', 'include_in_all' => true),
            'language' => array('type' => 'string', 'include_in_all' => true),
            'name' => array('type' => 'string', 'include_in_all' => true),
            'updatedAt' => array('type' => 'long', 'include_in_all' => false),
            'blocks' => array('properties' => array(
                'content' => array('type' => 'string',  'store' => true),
                'type' => array('type' => 'string', 'store' => true)
            )),
        ));

    }
}
