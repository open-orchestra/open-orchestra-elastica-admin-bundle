<?php

namespace OpenOrchestra\ElasticaAdmin\SchemaGenerator;

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
