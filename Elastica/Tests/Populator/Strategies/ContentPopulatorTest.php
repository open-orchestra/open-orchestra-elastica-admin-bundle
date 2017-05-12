<?php

namespace OpenOrchestra\Elastica\Tests\Populator\Strategies;

use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Populator\ElasticaPopulatorInterface;
use OpenOrchestra\Elastica\Populator\Strategies\ContentPopulator;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Phake;

/**
 * Test ContentPopulatorTest
 */
class ContentPopulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentPopulator
     */
    protected $populator;

    protected $multipleIndexor;
    protected $contentRepository;
    protected $languages = array('en' => 'English', 'fr' => 'Francais');

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock(ContentRepositoryInterface::CLASS);
        $this->multipleIndexor = Phake::mock(MultipleDocumentIndexorInterface::CLASS);

        $this->populator = new ContentPopulator($this->multipleIndexor, $this->contentRepository, $this->languages);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ElasticaPopulatorInterface::CLASS, $this->populator);
    }

    /**
     * Test populate
     */
    public function testPopulate()
    {
        $content = Phake::mock(ContentInterface::CLASS);

        Phake::when($this->contentRepository)->findByContentTypeAndCondition(Phake::anyParameters())->thenReturn(array(
            $content,
            $content,
        ));

        $this->populator->populate();

        Phake::verify($this->contentRepository)->findByContentTypeAndCondition('en');
        Phake::verify($this->contentRepository)->findByContentTypeAndCondition('fr');
        Phake::verify($this->multipleIndexor, Phake::times(2))->indexMultiple(array($content, $content));
    }
}
