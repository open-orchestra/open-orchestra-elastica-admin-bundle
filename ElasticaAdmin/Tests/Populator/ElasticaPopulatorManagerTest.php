<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\Populator;

use OpenOrchestra\ElasticaAdmin\Populator\ElasticaPopulatorInterface;
use OpenOrchestra\ElasticaAdmin\Populator\ElasticaPopulatorManager;
use Phake;

/**
 * Test ElasticaPopulatorManagerTest
 */
class ElasticaPopulatorManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaPopulatorManager
     */
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new ElasticaPopulatorManager();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ElasticaPopulatorInterface::CLASS, $this->manager);
    }

    /**
     * Test populate
     */
    public function testPopulate()
    {
        $populator = Phake::mock(ElasticaPopulatorInterface::CLASS);

        $this->manager->addPopulator($populator);
        $this->manager->addPopulator($populator);

        $this->manager->populate();

        Phake::verify($populator, Phake::times(2))->populate();
    }
}
