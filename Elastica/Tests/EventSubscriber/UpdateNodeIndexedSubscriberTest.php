<?php

namespace OpenOrchestra\Elastica\Tests\EventSubscriber;

use OpenOrchestra\Elastica\EventSubscriber\UpdateNodeIndexedSubscriber;
use OpenOrchestra\Elastica\Indexor\NodeIndexor;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Phake;

/**
 * Class UpdateNodeIndexedSubscriberTest
 */
class UpdateNodeIndexedSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateNodeIndexedSubscriber
     */
    protected $subscriber;

    protected $indexor;
    protected $node;
    protected $status;
    protected $previousStatus;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->indexor = Phake::mock(NodeIndexor::CLASS);
        $this->node = Phake::mock(NodeInterface::CLASS);
        $this->event = Phake::mock(NodeEvent::CLASS);
        $this->status = Phake::mock(StatusInterface::CLASS);
        $this->previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($this->event)->getNode()->thenReturn($this->node);
        Phake::when($this->node)->getStatus()->thenReturn($this->status);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($this->previousStatus);

        $this->subscriber = new UpdateNodeIndexedSubscriber($this->indexor);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::CLASS, $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testSubscribedEVents()
    {
        $this->assertArrayHasKey(NodeEvents::NODE_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test update indexed node
     */
    public function testUpdateIndexedNode()
    {
        Phake::when($this->status)->isPublishedState()->thenReturn(true);

        $this->subscriber->updateIndexedNode($this->event);

        Phake::verify($this->indexor)->index($this->node);
    }

    /**
     * Test update index with no published document
     */
    public function testUpdateIndexedNodeWithNoPublishedNode()
    {
        Phake::when($this->previousStatus)->isPublishedState()->thenReturn(true);

        $this->subscriber->updateIndexedNode($this->event);

        Phake::verify($this->indexor)->delete($this->node);
    }
}
