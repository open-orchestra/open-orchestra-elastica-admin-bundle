<?php

namespace OpenOrchestra\Elastica\Tests\EventSubscriber;

use OpenOrchestra\Elastica\EventSubscriber\UpdateContentSchemaSubscriber;
use OpenOrchestra\Elastica\SchemaGenerator\ContentTypeSchemaGenerator;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Phake;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateContentSchemaSubscriberTest
 */
class UpdateContentSchemaSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateContentSchemaSubscriber
     */
    protected $subscriber;

    protected $schemaGenerator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->schemaGenerator = Phake::mock(ContentTypeSchemaGenerator::CLASS);

        $this->subscriber = new UpdateContentSchemaSubscriber($this->schemaGenerator);
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
    public function testSubscribedEvents()
    {
        $this->assertArrayHasKey(ContentTypeEvents::CONTENT_TYPE_CREATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentTypeEvents::CONTENT_TYPE_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test update content type schema
     */
    public function testUpdateContentTypeSchema()
    {
        $contentType = Phake::mock(ContentTypeInterface::CLASS);
        $event = Phake::mock(ContentTypeEvent::CLASS);
        Phake::when($event)->getContentType()->thenReturn($contentType);

        $this->subscriber->updateContentTypeSchema($event);

        Phake::verify($this->schemaGenerator)->createMapping($contentType);
    }
}
