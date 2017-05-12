<?php

namespace OpenOrchestra\Elastica\Tests\SchemaInitializer;

use OpenOrchestra\Elastica\SchemaInitializer\ElasticaSchemaInitializerInterface;
use OpenOrchestra\Elastica\SchemaInitializer\SchemaInitializerManager;
use Phake;

/**
 * Test SchemaInitializerManagerTest
 */
class SchemaInitializerManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SchemaInitializerManager
     */
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new SchemaInitializerManager();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(SchemaInitializerManager::CLASS, $this->manager);
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $initializer = Phake::mock(ElasticaSchemaInitializerInterface::CLASS);
        $this->manager->addInitializer($initializer);
        $this->manager->addInitializer($initializer);

        $this->manager->initialize();

        Phake::verify($initializer, Phake::times(2))->initialize();
    }
}
