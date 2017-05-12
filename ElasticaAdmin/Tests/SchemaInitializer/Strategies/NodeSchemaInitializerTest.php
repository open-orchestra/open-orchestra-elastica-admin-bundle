<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\SchemaInitializer\Strategies;

use OpenOrchestra\ElasticaAdmin\SchemaGenerator\ElasticaSchemaGeneratorInterface;
use OpenOrchestra\ElasticaAdmin\SchemaInitializer\ElasticaSchemaInitializerInterface;
use OpenOrchestra\ElasticaAdmin\SchemaInitializer\Strategies\NodeSchemaInitializer;
use Phake;

/**
 * Test NodeSchemaInitializerTest
 */
class NodeSchemaInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeSchemaInitializer
     */
    protected $initializer;

    protected $schemaGenerator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->schemaGenerator = Phake::mock(ElasticaSchemaGeneratorInterface::CLASS);

        $this->initializer = new NodeSchemaInitializer($this->schemaGenerator);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ElasticaSchemaInitializerInterface::CLASS, $this->initializer);
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->initializer->initialize();

        Phake::verify($this->schemaGenerator)->createMapping();
    }
}
