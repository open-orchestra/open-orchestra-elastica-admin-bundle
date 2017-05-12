<?php

namespace OpenOrchestra\Elastica\SchemaGenerator;

use Elastica\Client;
use OpenOrchestra\Elastica\Factory\MappingFactory;

/**
 * Class NodeSchemaGenerator
 */
class NodeSchemaGenerator implements ElasticaSchemaGeneratorInterface
{
    const INDEX_TYPE = 'node';

    protected $client;
    protected $indexName;
    protected $mappingFactory;

    /**
     * @param Client                    $client
     * @param string                    $indexName
     * @param MappingFactory            $mappingFactory
     */
    public function __construct(Client $client, $indexName, MappingFactory $mappingFactory)
    {
        $this->client = $client;
        $this->indexName = $indexName;
        $this->mappingFactory = $mappingFactory;
    }

    /**
     * Create a elasticSearch linked to the object
     */
    public function createMapping()
    {
        $index = $this->client->getIndex($this->indexName);
        $type = $index->getType(self::INDEX_TYPE);

        $mappingProperties = array(
            'id' => array('type' => 'string', 'include_in_all' => true),
            'elementId' => array('type' => 'string', 'include_in_all' => true),
            'nodeId' => array('type' => 'string', 'include_in_all' => true),
            'siteId' => array('type' => 'string', 'include_in_all' => true),
            'language' => array('type' => 'string', 'include_in_all' => true),
            'name' => array('type' => 'string', 'include_in_all' => true),
            'updatedAt' => array('type' => 'long', 'include_in_all' => false),
            'blocks' => array('properties' => array(
                'content' => array('type' => 'string',  'store' => true),
                'type' => array('type' => 'string', 'store' => true)
            )),
        );

        $mapping = $this->mappingFactory->create($type);
        $mapping->setProperties($mappingProperties);

        $mapping->send();
    }
}
