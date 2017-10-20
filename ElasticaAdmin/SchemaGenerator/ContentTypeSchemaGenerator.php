<?php

namespace OpenOrchestra\ElasticaAdmin\SchemaGenerator;

use Elastica\Client;
use Elastica\Request;
use OpenOrchestra\ElasticaAdmin\Factory\MappingFactory;
use OpenOrchestra\ElasticaAdmin\Mapper\FieldToElasticaTypeMapper;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class ContentTypeSchemaGenerator
 */
class ContentTypeSchemaGenerator implements DocumentToElasticaSchemaGeneratorInterface
{
    const INDEX_TYPE = 'content_';

    protected $client;
    protected $indexName;
    protected $formMapper;
    protected $mappingFactory;

    /**
     * @param Client                     $client
     * @param FieldToElasticaTypeMapper  $formMapper
     * @param string                     $indexName
     * @param MappingFactory             $mappingFactory
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(
        Client $client,
        FieldToElasticaTypeMapper $formMapper,
        $indexName,
        MappingFactory $mappingFactory,
        ContentRepositoryInterface $contentRepository
    ) {
        $this->client = $client;
        $this->indexName = $indexName;
        $this->formMapper = $formMapper;
        $this->mappingFactory = $mappingFactory;
        $this->contentRepository = $contentRepository;
    }

    /**
     * Create a elasticSearch linked to the object
     *
     * @param null|ContentTypeInterface $contentType
     */
    public function createMapping($contentType)
    {
        $temporaryName = uniqid() . $this->indexName;
        $index = $this->client->getIndex($this->indexName);
        $type = $index->getType(self::INDEX_TYPE . $contentType->getContentTypeId());

        $removeExistingMappingPropertiesCommand = "if (ctx._source._type == '".self::INDEX_TYPE . $contentType->getContentTypeId() ."') {";
        if(!empty($type->getMapping())) {
            $existingMappingProperties = $type->getMapping()[self::INDEX_TYPE . $contentType->getContentTypeId()]['properties'];
            foreach ($contentType->getFields() as $field) {
                $fiedlId = 'attribute_' . $field->getFieldId();
                if ($field->isSearchable() &&
                    array_key_exists($fiedlId, $existingMappingProperties) &&
                    $this->formMapper->map($field->getType()) != $existingMappingProperties[$fiedlId]['type']
                ) {
                    $removeExistingMappingPropertiesCommand .= "ctx._source.remove('" . $fiedlId . "');ctx._source.remove('" . $fiedlId . "_stringValue');";
                    $this->contentRepository->deleteAttributeForContentType($field->getFieldId(), $contentType->getContentTypeId());
                }
            }
        }
        $removeExistingMappingPropertiesCommand .= "}";

        $index->getClient()->request('_reindex', Request::POST, [
            "source" => [
                "index" => $this->indexName
            ],
            "dest" => [
                "index" => $temporaryName
            ],
            "script" => [
                "inline" => $removeExistingMappingPropertiesCommand
            ]
        ]);

        $temporaryIndex = $this->client->getIndex($temporaryName);
        $type = $temporaryIndex->getType(self::INDEX_TYPE . $contentType->getContentTypeId());
        $mappingProperties = array(
            'id' => array('type' => 'string', 'include_in_all' => true),
            'elementId' => array('type' => 'string', 'include_in_all' => true),
            'contentId' => array('type' => 'string', 'include_in_all' => true),
            'name' => array('type' => 'string', 'include_in_all' => true),
            'siteId' => array('type' => 'string', 'include_in_all' => true),
            'linkedToSite' => array('type' => 'boolean', 'include_in_all' => false),
            'language' => array('type' => 'string', 'include_in_all' => true),
            'contentType' => array('type' => 'string', 'include_in_all' => true),
            'keywords' => array('type' => 'string', 'include_in_all' => true),
            'updatedAt' => array('type' => 'long', 'include_in_all' => false),
        );
        /** @var FieldTypeInterface $field */
        foreach ($contentType->getFields() as $field) {
            if ($field->isSearchable()) {
                $mappingProperties['attribute_' . $field->getFieldId()] = array('type' => $this->formMapper->map($field->getType()), 'include_in_all' => false);
                $mappingProperties['attribute_' . $field->getFieldId() . '_stringValue'] = array('type' => 'string', 'include_in_all' => true);
            }
        }
        $mapping = $this->mappingFactory->create($type);
        $mapping->setProperties($mappingProperties);
        $mapping->send();

        $index->delete();
        $temporaryIndex->addAlias($this->indexName);
    }
}
