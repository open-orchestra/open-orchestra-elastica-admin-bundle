<?php

namespace OpenOrchestra\ElasticaAdmin\Indexor;

use OpenOrchestra\ElasticaAdmin\Exception\IndexorWrongParameterException;

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
