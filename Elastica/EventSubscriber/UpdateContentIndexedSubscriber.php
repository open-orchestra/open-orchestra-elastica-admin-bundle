<?php

namespace OpenOrchestra\Elastica\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;

/**
 * Class UpdateContentIndexedSubscriber
 */
class UpdateContentIndexedSubscriber implements EventSubscriberInterface
{
    protected $contentIndexor;

    /**
     * @param ContentIndexor             $contentIndexor
     */
    public function __construct(ContentIndexor $contentIndexor)
    {
        $this->contentIndexor = $contentIndexor;
    }

    /**
     * @param ContentEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function updateIndexedContent(ContentEvent $event)
    {
        $content = $event->getContent();
        if (true === $content->getStatus()->isPublishedState()
        ) {
            $this->contentIndexor->index($content);
        } elseif (null !== $event->getPreviousStatus() && true === $event->getPreviousStatus()->isPublishedState()) {
            $this->contentIndexor->delete($content);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_CHANGE_STATUS => 'updateIndexedContent',
            ContentEvents::CONTENT_UPDATE => 'updateIndexedContent',
        );
    }
}
