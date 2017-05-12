<?php

namespace OpenOrchestra\Elastica\Tests\Populator\Strategies;

use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Populator\ElasticaPopulatorInterface;
use OpenOrchestra\Elastica\Populator\Strategies\NodePopulator;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Phake;

/**
 * Test NodePopulatorTest
 */
class NodePopulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodePopulator
     */
    protected $populator;

    protected $multipleIndexor;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock(ReadNodeRepositoryInterface::CLASS);
        $this->multipleIndexor = Phake::mock(MultipleDocumentIndexorInterface::CLASS);

        $this->populator = new NodePopulator($this->multipleIndexor, $this->nodeRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ElasticaPopulatorInterface::CLASS, $this->populator);
    }

    /**
     * Test populate
     */
    public function testPopulate()
    {
        $node = Phake::mock(ReadNodeInterface::CLASS);

        Phake::when($this->nodeRepository)->findAllPublishedByTypeWithSkipAndLimit(Phake::anyParameters())->thenReturn(array(
            $node,
            $node,
        ));
        Phake::when($this->nodeRepository)->countAllPublishedByType(Phake::anyParameters())->thenReturn(2);

        $this->populator->populate();

        Phake::verify($this->multipleIndexor)->indexMultiple(array($node, $node));
    }
}
