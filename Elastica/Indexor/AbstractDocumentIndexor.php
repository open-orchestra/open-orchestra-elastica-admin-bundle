<?php

namespace OpenOrchestra\Elastica\Indexor;

use Elastica\Client;
use Elastica\Index;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;

/**
 * Class AbstractDocumentIndexor
 */
abstract class AbstractDocumentIndexor implements DocumentIndexorInterface, MultipleDocumentIndexorInterface, DocumentDeletorInterface
{
    protected $client;
    protected $indexName;
    protected $transformer;

    /**
     * @param Client                              $client
     * @param ModelToElasticaTransformerInterface $transformer
     * @param string                              $indexName
     */
    public function __construct(Client $client, ModelToElasticaTransformerInterface $transformer, $indexName)
    {
        $this->client = $client;
        $this->indexName = $indexName;
        $this->transformer = $transformer;
    }

    /**
     * Index the object after a transformation
     *
     * @param mixed $document
     */
    public function index($document)
    {
        $index = $this->client->getIndex($this->indexName);

        $this->indexDocument($document, $index);

        $index->refresh();
    }

    /**
     * Index the object after a transformation
     *
     * @param array $documents
     */
    public function indexMultiple(array $documents)
    {
        $index = $this->client->getIndex($this->indexName);

        foreach ($documents as $document) {
            $this->indexDocument($document, $index);
        }

        $index->refresh();
    }

    /**
     * @param mixed $document
     * @param Index $index
     */
    abstract  protected function indexDocument($document, Index $index);
}
