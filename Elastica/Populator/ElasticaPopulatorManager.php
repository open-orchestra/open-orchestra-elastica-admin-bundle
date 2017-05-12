<?php

namespace OpenOrchestra\Elastica\Populator;

/**
 * Class ElasticaPopulatorManager
 */
class ElasticaPopulatorManager implements ElasticaPopulatorInterface
{
    protected $populators = array();

    /**
     * @var ElasticaPopulatorInterface $populator
     */
    public function addPopulator(ElasticaPopulatorInterface $populator)
    {
        $this->populators[] = $populator;
    }

    /**
     * Populate the index
     */
    public function populate()
    {
        foreach ($this->populators as $populator) {
            $populator->populate();
        }
    }
}
