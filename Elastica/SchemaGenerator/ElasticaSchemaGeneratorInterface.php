<?php

namespace OpenOrchestra\Elastica\SchemaGenerator;

/**
 * Interface ElasticaSchemaGeneratorInterface
 */
interface ElasticaSchemaGeneratorInterface
{
    /**
     * Create a elasticSearch linked to the object
     */
    public function createMapping();
}
