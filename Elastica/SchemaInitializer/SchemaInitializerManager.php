<?php

namespace OpenOrchestra\Elastica\SchemaInitializer;

/**
 * Class SchemaInitializerManager
 */
class SchemaInitializerManager implements ElasticaSchemaInitializerInterface
{
    protected $initializers = array();

    /**
     * @param ElasticaSchemaInitializerInterface $initializer
     */
    public function addInitializer(ElasticaSchemaInitializerInterface $initializer)
    {
        $this->initializers[] = $initializer;
    }

    /**
     * Initialize everything
     */
    public function initialize()
    {
        /** @var ElasticaSchemaInitializerInterface $initializer */
        foreach ($this->initializers as $initializer) {
            $initializer->initialize();
        }
    }
}
