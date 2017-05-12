<?php

namespace OpenOrchestra\Elastica\Tests\Indexor;

use Elastica\Client;
use Elastica\Document;
use Elastica\Exception\NotFoundException;
use Elastica\Index;
use Elastica\Type;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;
use OpenOrchestra\Elastica\Indexor\DocumentDeletorInterface;
use OpenOrchestra\Elastica\Indexor\DocumentIndexorInterface;
use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Indexor\NodeIndexor;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use Phake;

/**
 * Test NodeIndexorTest
 */
class NodeIndexorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeIndexor
     */
    protected $indexor;

    protected $transformer;
    protected $client;
    protected $index;
    protected $type;
    protected $node;
    protected $document;

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

        $this->document = Phake::mock(Document::CLASS);
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($this->document);
        $this->node = Phake::mock(ReadNodeInterface::CLASS);

        $this->indexor = new NodeIndexor($this->client, $this->transformer, 'orchestra');
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
     * test Index
     */
    public function testIndex()
    {
        $this->indexor->index($this->node);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('node');
        Phake::verify($this->type)->addDocument($this->document);
        Phake::verify($this->index)->refresh();
    }

    /**
     * Test index multiple
     */
    public function testIndexMultiple()
    {
        $this->indexor->indexMultiple(array($this->node, $this->node));

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index, Phake::times(2))->getType('node');
        Phake::verify($this->type, Phake::times(2))->addDocument($this->document);
        Phake::verify($this->index)->refresh();
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->indexor->delete($this->node);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('node');
        Phake::verify($this->type)->deleteDocument($this->document);
        Phake::verify($this->index)->refresh();
    }

    /**
     * Test when there is no indexed document
     *
     * @throws IndexorWrongParameterException
     */
    public function testDeleteWithNoIndexedObject()
    {
        Phake::when($this->type)->deleteDocument(Phake::anyParameters())->thenThrow(new NotFoundException());

        $this->indexor->delete($this->node);

        Phake::verify($this->client)->getIndex('orchestra');
        Phake::verify($this->index)->getType('node');
        Phake::verify($this->type)->deleteDocument($this->document);
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
