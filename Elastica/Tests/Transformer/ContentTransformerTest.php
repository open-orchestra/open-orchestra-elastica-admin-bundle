<?php

namespace OpenOrchestra\Elastica\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Document;
use OpenOrchestra\Elastica\Transformer\ContentTransformer;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Phake;

/**
 * Test ContentTransformerTest
 */
class ContentTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTransformer
     */
    protected $transformer;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->transformer = new ContentTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ModelToElasticaTransformerInterface::CLASS, $this->transformer);
    }

    /**
     * Test transformation
     */
    public function testTransform()
    {
        $keyword = Phake::mock(KeywordInterface::CLASS);
        Phake::when($keyword)->getLabel()->thenReturn('keywordLabel');

        $attribute = Phake::mock(ContentAttributeInterface::CLASS);
        Phake::when($attribute)->getName()->thenReturn('attributeName');
        Phake::when($attribute)->getValue()->thenReturn('attributeValue');
        Phake::when($attribute)->getStringValue()->thenReturn('stringValue');
        $attribute2 = Phake::mock(ContentAttributeInterface::CLASS);
        Phake::when($attribute2)->getName()->thenReturn('attributeName2');
        Phake::when($attribute2)->getValue()->thenReturn('attributeValue2');
        Phake::when($attribute2)->getStringValue()->thenReturn('stringValue2');
        $attribute3 = Phake::mock(ContentAttributeInterface::CLASS);
        Phake::when($attribute3)->getName()->thenReturn('attributeName3');
        Phake::when($attribute3)->getValue()->thenReturn('');
        Phake::when($attribute3)->getStringValue()->thenReturn('stringValue3');
        $attribute4 = Phake::mock(ContentAttributeInterface::CLASS);
        Phake::when($attribute4)->getName()->thenReturn('attributeName4');
        Phake::when($attribute4)->getValue()->thenReturn(null);
        Phake::when($attribute4)->getStringValue()->thenReturn('stringValue4');

        $date = new \DateTime();
        $attributes = new ArrayCollection();
        $attributes->add($attribute);
        $attributes->add($attribute2);

        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getId()->thenReturn('id');
        Phake::when($content)->getContentId()->thenReturn('contentId');
        Phake::when($content)->getName()->thenReturn('name');
        Phake::when($content)->getSiteId()->thenReturn('siteId');
        Phake::when($content)->getLanguage()->thenReturn('language');
        Phake::when($content)->getContentType()->thenReturn('contentType');
        Phake::when($content)->getAttributes()->thenReturn($attributes);
        Phake::when($content)->isLinkedToSite()->thenReturn(true);
        Phake::when($content)->getUpdatedAt()->thenReturn($date);
        Phake::when($content)->getKeywords()->thenReturn(array($keyword, $keyword));

        $document = $this->transformer->transform($content);

        $this->assertInstanceOf(Document::CLASS, $document);

        $this->assertSame('contentId-language', $document->getId());
        $this->assertSame(array(
            'id' => 'contentId-language',
            'elementId' => 'id',
            'contentId' => 'contentId',
            'name' => 'name',
            'siteId' => 'siteId',
            'linkedToSite' => true,
            'language' => 'language',
            'contentType' => 'contentType',
            'keywords' => 'keywordLabel,keywordLabel',
            'updatedAt' => $date->getTimestamp(),
            'attribute_attributeName' => 'attributeValue',
            'attribute_attributeName_stringValue' => 'stringValue',
            'attribute_attributeName2' => 'attributeValue2',
            'attribute_attributeName2_stringValue' => 'stringValue2',
        ), $document->getData());
    }
}
