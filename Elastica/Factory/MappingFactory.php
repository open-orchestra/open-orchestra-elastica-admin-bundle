<?php

namespace OpenOrchestra\Elastica\Factory;

use Elastica\Type;
use Elastica\Type\Mapping;

/**
 * Class MappingFactory
 */
class MappingFactory
{
    /**
     * @param Type $type
     *
     * @return Mapping
     */
    public function create(Type $type)
    {
        return new Mapping($type);
    }
}
