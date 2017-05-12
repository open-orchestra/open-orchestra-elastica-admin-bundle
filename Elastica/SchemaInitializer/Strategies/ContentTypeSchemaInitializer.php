<?php

namespace OpenOrchestra\Elastica\SchemaInitializer\Strategies;

use OpenOrchestra\Elastica\SchemaGenerator\DocumentToElasticaSchemaGeneratorInterface;
use OpenOrchestra\Elastica\SchemaInitializer\ElasticaSchemaInitializerInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeSchemaInitializer
 */
class ContentTypeSchemaInitializer implements ElasticaSchemaInitializerInterface
{
    protected $contentTypeRepository;
    protected $schemaGenerator;

    /**
     * @param ContentTypeRepositoryInterface             $contentTypeRepository
     * @param DocumentToElasticaSchemaGeneratorInterface $schemaGenerator
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository, DocumentToElasticaSchemaGeneratorInterface $schemaGenerator)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->schemaGenerator = $schemaGenerator;
    }

    /**
     * Initialize content type schema
     */
    public function initialize()
    {
        $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion();

        foreach ($contentTypes as $contentType) {
            $this->schemaGenerator->createMapping($contentType);
        }
    }
}
