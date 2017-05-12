<?php

namespace OpenOrchestra\ElasticaAdmin\SchemaGenerator;

/**
 * Interface DocumentToElasticaSchemaGeneratorInterface
 */
interface DocumentToElasticaSchemaGeneratorInterface
{
    /**
     * Create a elasticSearch linked to the object
     *
     * @param mixed $document
     */
    public function createMapping($document);
}
