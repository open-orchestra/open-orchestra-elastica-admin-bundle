<?php

namespace OpenOrchestra\Elastica\Tests\Indexor;

use Elastica\Client;
use Elastica\Document;
use Elastica\Exception\NotFoundException;
use Elastica\Index;
use Elastica\Type;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;
use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\Elastica\Indexor\DocumentDeletorInterface;
use OpenOrchestra\Elastica\Indexor\DocumentIndexorInterface;
use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Phake;

/**
 * Test ContentIndexorTest
 */
class ContentIndexorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentIndexor
     */
    protected $indexor;

    protected $transformer;
    protected $client;
    protected $index;
    protected $type;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->transformer = Phake::mock(ModelToElasticaTransformerInterface::CLASS);

        $this->type = Phake::mock(Type::CLASS);
        $this->index = Phake::mock(Index::CLASS);
        Phake::when($this->index)->getType(Phake::anyParameters())->thenReturn($this->type);
        $this->client = Phake::mock(Client::CLASS);
        Phake::when($this->client)->getIndex(Phake::anyParameters())->thenReturn($this->index);

        $this->indexor = new ContentIndexor($this->client, $this->transformer, 'orchestra');
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DocumentIndexorInterface::CLASS, $this->indexor);
        $this->assertInstanceOf(DocumentDeletorInterface::CLASS, $this->indexor);
        $this->assertInstanceOf(MultipleDocumentIndexorInterface::CLASS, $this->indexor);
    }

    /**
     * @param string $contentType
     *
     * @dataProvider provideContentTypes
     */
    public function testIndex($contentType)
    {
        $document = Phake::mock(Document::CLASS);
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($document);
        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getContentType()->thenReturn($contentType);

        $this->indexor->index($content);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('content_' . $contentType);
        Phake::verify($this->type)->addDocument($document);
        Phake::verify($this->index)->refresh();
    }

    /**
     * @return array
     */
    public function provideContentTypes()
    {
        return array(
            array('foo'),
            array('bar'),
        );
    }

    /**
     * @param string $contentType
     *
     * @dataProvider provideContentTypes
     */
    public function testIndexMultiple($contentType)
    {
        $document = Phake::mock(Document::CLASS);
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($document);
        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getContentType()->thenReturn($contentType);

        $this->indexor->indexMultiple(array($content, $content));

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index, Phake::times(2))->getType('content_' . $contentType);
        Phake::verify($this->type, Phake::times(2))->addDocument($document);
        Phake::verify($this->index)->refresh();
    }

    /**
     * @param string $contentType
     *
     * @dataProvider provideContentTypes
     */
    public function testDelete($contentType)
    {
        $document = Phake::mock(Document::CLASS);
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($document);
        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getContentType()->thenReturn($contentType);

        $this->indexor->delete($content);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('content_' . $contentType);
        Phake::verify($this->type)->deleteDocument($document);
        Phake::verify($this->index)->refresh();
    }

    /**
     * Test when there is no indexed document
     *
     * @throws IndexorWrongParameterException
     *
     * @dataProvider provideContentTypes
     */
    public function testDeleteWithNoIndexedObject($contentType)
    {
        $document = Phake::mock(Document::CLASS);
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($document);
        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getContentType()->thenReturn($contentType);
        Phake::when($this->type)->deleteDocument(Phake::anyParameters())->thenThrow(new NotFoundException());

        $this->indexor->delete($content);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('content_' . $contentType);
        Phake::verify($this->type)->deleteDocument($document);
        Phake::verify($this->index, Phake::never())->refresh();
    }

    /**
     * @throws IndexorWrongParameterException
     */
    public function testDeleteWithWrongObjectType()
    {
        $this->expectException(IndexorWrongParameterException::CLASS);

        $this->indexor->delete(Phake::mock('stdClass'));
    }
}
