<?php

namespace OpenOrchestra\Elastica\SchemaInitializer\Strategies;

use OpenOrchestra\Elastica\SchemaGenerator\ElasticaSchemaGeneratorInterface;
use OpenOrchestra\Elastica\SchemaInitializer\ElasticaSchemaInitializerInterface;

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
