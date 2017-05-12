<?php

namespace OpenOrchestra\ElasticaAdmin\SchemaInitializer\Strategies;

use OpenOrchestra\ElasticaAdmin\SchemaGenerator\ElasticaSchemaGeneratorInterface;
use OpenOrchestra\ElasticaAdmin\SchemaInitializer\ElasticaSchemaInitializerInterface;

/**
 * Class NodeSchemaInitializer
 */
class NodeSchemaInitializer implements ElasticaSchemaInitializerInterface
{
    protected $schemaGenerator;

    /**
     * @param ElasticaSchemaGeneratorInterface $schemaGenerator
     */
    public function __construct(ElasticaSchemaGeneratorInterface $schemaGenerator)
    {
        $this->schemaGenerator = $schemaGenerator;
    }

    /**
     * Initialize content type schema
     */
    public function initialize()
    {
      $this->schemaGenerator->createMapping();
    }
}
