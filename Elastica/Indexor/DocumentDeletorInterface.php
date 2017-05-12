<?php

namespace OpenOrchestra\Elastica\Indexor;

use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;

/**
 * Interface DocumentDeletorInterface
 */
interface DocumentDeletorInterface
{
    /**
     * @param mixed $content
     *
     * @throws IndexorWrongParameterException
     */
    public function delete($content);
}
