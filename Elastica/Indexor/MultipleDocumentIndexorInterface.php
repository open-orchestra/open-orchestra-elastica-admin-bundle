<?php

namespace OpenOrchestra\Elastica\Indexor;

/**
 * Interface MultipleDocumentIndexorInterface
 */
interface MultipleDocumentIndexorInterface
{
    /**
     * Index the object after a transformation
     *
     * @param array $objects
     */
    public function indexMultiple(array $objects);
}
