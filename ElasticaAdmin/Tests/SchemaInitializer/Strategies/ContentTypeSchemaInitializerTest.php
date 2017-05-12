<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\SchemaInitializer\Strategies;

use OpenOrchestra\ElasticaAdmin\SchemaGenerator\DocumentToElasticaSchemaGeneratorInterface;
use OpenOrchestra\ElasticaAdmin\SchemaInitializer\ElasticaSchemaInitializerInterface;
use OpenOrchestra\ElasticaAdmin\SchemaInitializer\Strategies\ContentTypeSchemaInitializer;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Phake;

/**
 * Test ContentTypeSchemaInitializerTest
 */
class ContentTypeSchemaInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeSchemaInitializer
     */
    protected $initializer;

    protected $contentTypeRepository;
    protected $schemaGenerator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock(ContentTypeRepositoryInterface::CLASS);
        $this->schemaGenerator = Phake::mock(DocumentToElasticaSchemaGeneratorInterface::CLASS);

        $this->initializer = new ContentTypeSchemaInitializer($this->contentTypeRepository, $this->schemaGenerator);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ElasticaSchemaInitializerInterface::CLASS, $this->initializer);
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $contentType = Phake::mock(ContentTypeInterface::CLASS);
        Phake::when($this->contentTypeRepository)->findAllNotDeletedInLastVersion()->thenReturn(array(
            $contentType,
            $contentType,
        ));

        $this->initializer->initialize();

        Phake::verify($this->schemaGenerator, Phake::times(2))->createMapping($contentType);
    }
}
