<?php

namespace OpenOrchestra\Elastica\Transformer;

use Elastica\Document;

/**
 * Interface ModelToElasticaTransformerInterface
 */
interface ModelToElasticaTransformerInterface
{
    /**
     * Transform the object to be indexed by Elasticsearch
     *
     * @param mixed $object
     *
     * @return Document
     */
    public function transform($object);
}
