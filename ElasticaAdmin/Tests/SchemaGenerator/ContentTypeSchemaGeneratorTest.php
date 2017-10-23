<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\SchemaGenerator;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Client;
use Elastica\Index;
use Elastica\Request;
use Elastica\Type;
use Elastica\Type\Mapping;
use OpenOrchestra\ElasticaAdmin\Factory\MappingFactory;
use OpenOrchestra\ElasticaAdmin\Mapper\FieldToElasticaTypeMapper;
use OpenOrchestra\ElasticaAdmin\SchemaGenerator\ContentTypeSchemaGenerator;
use OpenOrchestra\ElasticaAdmin\SchemaGenerator\DocumentToElasticaSchemaGeneratorInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Phake;

/**
 * Test ContentTypeSchemaGeneratorTest
 */
class ContentTypeSchemaGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeSchemaGenerator
     */
    protected $schemaGenerator;

    protected $type;
    protected $index;
    protected $client;
    protected $formMapper;
    protected $elasticaType;
    protected $mappingFactory;
    protected $contentRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->mappingFactory = Phake::mock(MappingFactory::CLASS);

        $this->elasticaType = 'foo';
        $this->formMapper = Phake::mock(FieldToElasticaTypeMapper::CLASS);
        Phake::when($this->formMapper)->map(Phake::anyParameters())->thenReturn($this->elasticaType);

        $this->type = Phake::mock(Type::CLASS);
        $this->index = Phake::mock(Index::CLASS);
        Phake::when($this->index)->getType(Phake::anyParameters())->thenReturn($this->type);
        Phake::when($this->type)->getMapping()->thenReturn(array(
            'content_contentTypeId' => array(
                'properties' => array(
                    'attribute_fieldId1' => array('type' => 'date')
                )
            )
        ));

        $this->client = Phake::mock(Client::CLASS);
        Phake::when($this->client)->getIndex(Phake::anyParameters())->thenReturn($this->index);
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        $this->schemaGenerator = new ContentTypeSchemaGenerator(
            $this->client,
            $this->formMapper,
            'orchestra',
            $this->mappingFactory,
            $this->contentRepository
        );
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DocumentToElasticaSchemaGeneratorInterface::CLASS, $this->schemaGenerator);
    }

    /**
     * @param string $fieldType
     *
     * @dataProvider provideFieldTypeAndIndexedFieldType
     */
    public function testCreateMapping($fieldType)
    {
        $mapping = Phake::mock(Mapping::CLASS);
        Phake::when($this->mappingFactory)->create(Phake::anyParameters())->thenReturn($mapping);

        $field1 = Phake::mock(FieldTypeInterface::CLASS);
        Phake::when($field1)->getFieldId()->thenReturn('fieldId1');
        Phake::when($field1)->isSearchable()->thenReturn(true);
        Phake::when($field1)->getType()->thenReturn($fieldType);
        $field2 = Phake::mock(FieldTypeInterface::CLASS);
        Phake::when($field2)->getFieldId()->thenReturn('fieldId2');
        Phake::when($field2)->isSearchable()->thenReturn(false);
        Phake::when($field2)->getType()->thenReturn('text');

        $fields = new ArrayCollection(array($field1, $field2));

        $contentType = Phake::mock(ContentTypeInterface::CLASS);
        Phake::when($contentType)->getContentTypeId()->thenReturn('contentTypeId');
        Phake::when($contentType)->getFields()->thenReturn($fields);

        $this->schemaGenerator->createMapping($contentType);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->client)->request('_reindex', Request::POST, [
                "source" => [
                    "index" => 'orchestra'
                ],
                "dest" => [
                    "index" => $this->schemaGenerator->getTemporaryIndexName()
                ],
                "script" => [
                    "inline" => "if (ctx._source._type == 'content_contentTypeId') {ctx._source.remove('attribute_fieldId1');ctx._source.remove('attribute_fieldId1_stringValue');}"
                ]
            ]
        );
        Phake::verify($this->index, Phake::times(2))->getType('content_contentTypeId');
        Phake::verify($mapping)->setProperties(array(
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
            'attribute_fieldId1' => array('type' => $this->elasticaType, 'include_in_all' => false),
            'attribute_fieldId1_stringValue' => array('type' => 'string', 'include_in_all' => true),
        ));
        Phake::verify($field1, Phake::times(2))->isSearchable();
        Phake::verify($field2, Phake::times(2))->isSearchable();
        Phake::verify($this->formMapper, Phake::times(2))->map($fieldType);
    }

    /**
     * @return array
     */
    public function provideFieldTypeAndIndexedFieldType()
    {
        return array(
            'Field type text' => array('text'),
            'Field type textarea' => array('textarea'),
            'Field type tinyMce' => array('oo_tinymce'),
        );
    }
}
