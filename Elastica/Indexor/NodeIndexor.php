<?php

namespace OpenOrchestra\Elastica\Indexor;

use Elastica\Exception\NotFoundException;
use Elastica\Index;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;
use OpenOrchestra\Elastica\SchemaGenerator\NodeSchemaGenerator;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;

/**
 * Class NodeIndexor
 */
class NodeIndexor extends AbstractDocumentIndexor
{
    /**
     * @param ReadNodeInterface $node
     * @param Index             $index
     */
    protected function indexDocument($node, Index $index)
    {
        $type = $index->getType(NodeSchemaGenerator::INDEX_TYPE);
        $document = $this->transformer->transform($node);
        $type->addDocument($document);
    }

    /**
     * @param ReadNodeInterface $node
     *
     * @throws IndexorWrongParameterException
     */
    public function delete($node)
    {
        if (!$node instanceof ReadNodeInterface) {
            throw new IndexorWrongParameterException();
        }

        $index = $this->client->getIndex($this->indexName);
        $type = $index->getType(NodeSchemaGenerator::INDEX_TYPE);
        $document = $this->transformer->transform($node);
        try {
            $type->deleteDocument($document);
            $index->refresh();
        } catch (NotFoundException $e) {

        }
    }
}
