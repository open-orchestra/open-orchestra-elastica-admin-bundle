<?php

namespace OpenOrchestra\ElasticaAdmin\Indexor;

/**
 * Interface DocumentIndexorInterface
 */
interface DocumentIndexorInterface
{
    /**
     * Index the object after a transformation
     *
     * @param mixed $object
     */
    public function index($object);
}
