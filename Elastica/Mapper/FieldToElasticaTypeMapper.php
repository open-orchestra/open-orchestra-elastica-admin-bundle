<?php

namespace OpenOrchestra\Elastica\Mapper;

/**
 * Class FieldToElasticaTypeMapper
 */
class FieldToElasticaTypeMapper
{
    protected $mapping = array();

    /**
     * Returns the Elastica type linked to a form type
     *
     * @param string $formType
     *
     * @return string
     */
    public function map($formType)
    {
        $this->mapping = array_merge(array($formType => 'string'), $this->mapping);

        return $this->mapping[$formType];
    }

    /**
     * @param string $formType
     * @param string $elasticaType
     */
    public function addMappingConfiguration($formType, $elasticaType)
    {
        $this->mapping[$formType] = $elasticaType;
    }
}
