<?php

namespace OpenOrchestra\Elastica\Tests\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Phake;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\Elastica\EventSubscriber\UpdateContentIndexedSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateContentIndexedSubscriberTest
 */
class UpdateContentIndexedSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateContentIndexedSubscriber
     */
    protected $subscriber;

    protected $indexor;
    protected $content;
    protected $event;
    protected $status;
    protected $previousStatus;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->indexor = Phake::mock(ContentIndexor::CLASS);
        $this->content = Phake::mock(ContentInterface::CLASS);
        $this->event = Phake::mock(ContentEvent::CLASS);
        $this->status = Phake::mock(StatusInterface::CLASS);
        $this->previousStatus = Phake::mock(StatusInterface::CLASS);
        Phake::when($this->event)->getContent()->thenReturn($this->content);
        Phake::when($this->content)->getStatus()->thenReturn($this->status);
        Phake::when($this->event)->getPreviousStatus()->thenReturn($this->previousStatus);

        $this->subscriber = new UpdateContentIndexedSubscriber($this->indexor);
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
        $this->assertArrayHasKey(ContentEvents::CONTENT_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test update indexed content
     */
    public function testUpdateIndexedContent()
    {
        Phake::when($this->status)->isPublishedState()->thenReturn(true);

        $this->subscriber->updateIndexedContent($this->event);

        Phake::verify($this->indexor)->index($this->content);
    }

    /**
     * Test update index with no published document
     */
    public function testUpdateIndexedContentWithNoPublishedContent()
    {
        Phake::when($this->previousStatus)->isPublishedState()->thenReturn(true);

        $this->subscriber->updateIndexedContent($this->event);

        Phake::verify($this->indexor)->delete($this->content);
    }
}
