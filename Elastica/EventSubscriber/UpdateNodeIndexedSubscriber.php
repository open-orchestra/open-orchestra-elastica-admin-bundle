<?php

namespace OpenOrchestra\Elastica\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\NodeIndexor;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;

/**
 * Class UpdateNodeIndexedSubscriber
 */
class UpdateNodeIndexedSubscriber implements EventSubscriberInterface
{
    protected $nodeIndexor;

    /**
     * @param NodeIndexor                 $nodeIndexor
     */
    public function __construct(NodeIndexor $nodeIndexor)
    {
        $this->nodeIndexor = $nodeIndexor;
    }

    /**
     * @param NodeEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function updateIndexedNode(NodeEvent $event)
    {
        $node = $event->getNode();
        if (true === $node->getStatus()->isPublishedState()
        ) {
            $this->nodeIndexor->index($node);
        } elseif (true === $event->getPreviousStatus()->isPublishedState()) {
            $this->nodeIndexor->delete($node);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateIndexedNode'
        );
    }
}
