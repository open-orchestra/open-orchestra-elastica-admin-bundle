<?php

namespace OpenOrchestra\Elastica\Populator;

/**
 * Interface ElasticaPopulatorInterface
 */
interface ElasticaPopulatorInterface
{
    /**
     * Perform the population of an element, the schema is already created
     */
    public function populate();
}
