<?php

namespace OpenOrchestra\Elastica\Populator\Strategies;

use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Populator\ElasticaPopulatorInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class ContentPopulator
 */
class ContentPopulator implements ElasticaPopulatorInterface
{
    protected $languages;
    protected $multipleIndexor;
    protected $contentRepository;

    /**
     * @param MultipleDocumentIndexorInterface $multipleIndexor
     * @param ContentRepositoryInterface       $contentRepository
     * @param array                            $languages
     */
    public function __construct(MultipleDocumentIndexorInterface $multipleIndexor, ContentRepositoryInterface $contentRepository, array $languages = array())
    {
        $this->languages = $languages;
        $this->multipleIndexor = $multipleIndexor;
        $this->contentRepository = $contentRepository;
    }

    /**
     * Populate contents
     */
    public function populate()
    {
        foreach ($this->languages as $language => $key) {
            $contents = $this->contentRepository->findByContentTypeAndCondition($language);

            $this->multipleIndexor->indexMultiple($contents);
        }
    }

}
