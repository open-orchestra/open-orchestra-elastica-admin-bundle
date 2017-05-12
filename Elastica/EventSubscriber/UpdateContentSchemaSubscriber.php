<?php

namespace OpenOrchestra\Elastica\EventSubscriber;

use OpenOrchestra\Elastica\SchemaGenerator\ContentTypeSchemaGenerator;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateContentSchemaSubscriber
 */
class UpdateContentSchemaSubscriber implements EventSubscriberInterface
{
    protected $schemaGenerator;

    /**
     * @param ContentTypeSchemaGenerator $schemaGenerator
     */
    public function __construct(ContentTypeSchemaGenerator $schemaGenerator)
    {
        $this->schemaGenerator = $schemaGenerator;
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function updateContentTypeSchema(ContentTypeEvent $event)
    {
        $this->schemaGenerator->createMapping($event->getContentType());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentTypeEvents::CONTENT_TYPE_UPDATE => 'updateContentTypeSchema',
            ContentTypeEvents::CONTENT_TYPE_CREATE => 'updateContentTypeSchema',
        );
    }
}
