<?php

namespace OpenOrchestra\Elastica\Populator\Strategies;

use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Populator\ElasticaPopulatorInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;

/**
 * Class NodePopulator
 */
class NodePopulator implements ElasticaPopulatorInterface
{
    protected $multipleIndexor;
    protected $nodeRepository;

    /**
     * @param MultipleDocumentIndexorInterface $multipleIndexor
     * @param ReadNodeRepositoryInterface      $nodeRepository
     */
    public function __construct(MultipleDocumentIndexorInterface $multipleIndexor, ReadNodeRepositoryInterface $nodeRepository)
    {
        $this->multipleIndexor = $multipleIndexor;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Populate nodes
     */
    public function populate()
    {
        $limit = 20;
        $countNodes = $this->nodeRepository->countAllPublishedByType(NodeInterface::TYPE_DEFAULT);
        for ($skip = 0; $skip < $countNodes; $skip += $limit) {
            $nodes = $this->nodeRepository->findAllPublishedByTypeWithSkipAndLimit(NodeInterface::TYPE_DEFAULT, $skip, $limit);
            $this->multipleIndexor->indexMultiple($nodes);
        }
    }
}
