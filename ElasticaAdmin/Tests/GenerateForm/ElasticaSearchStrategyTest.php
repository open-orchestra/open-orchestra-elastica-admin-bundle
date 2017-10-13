<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\GenerateForm;

use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use OpenOrchestra\ElasticaAdmin\GenerateForm\ElasticaSearchStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Phake;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Test ElasticaSearchStrategyTest
 */
class ElasticaSearchStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaSearchStrategy
     */
    protected $strategy;
    protected $nodeRepository;
    protected $contextManager;
    protected $siteId = 'fakeSiteId';
    protected $language = 'fakeLanguage';
    protected $nodeId = 'fakeNodeId';
    protected $nodeName = 'fakeNodeName';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        Phake::when($node)->getId()->thenReturn($this->nodeId);
        Phake::when($node)->getName()->thenReturn($this->nodeName);
        Phake::when($this->contextManager)->getSiteId()->thenReturn($this->siteId);
        Phake::when($this->contextManager)->getSiteDefaultLanguage()->thenReturn($this->language);
        Phake::when($this->nodeRepository)->findAllSpecialPage(Phake::anyParameters())->thenReturn(array($node));

        $this->strategy = new ElasticaSearchStrategy($this->nodeRepository, $this->contextManager, array());
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(GenerateFormInterface::CLASS, $this->strategy);
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('elastica_search', $this->strategy->getName());
    }

    /**
     * @param bool   $supports
     * @param string $component
     *
     * @dataProvider provideSupportsLinkedToBlockComponent
     */
    public function testSupport($supports, $component)
    {
        $block = Phake::mock(BlockInterface::CLASS);
        Phake::when($block)->getComponent()->thenReturn($component);

        $this->assertSame($supports, $this->strategy->support($block));
    }

    /**
     * @return array
     */
    public function provideSupportsLinkedToBlockComponent()
    {
        return array(
            'elastica search block' => array(true, 'elastica_search'),
            'elastica list block' => array(false, 'elastica_list'),
            'foo block' => array(false, 'foo'),
            'bar block' => array(false, 'bar'),
        );
    }

    /**
     * Test build form
     */
    public function testBuildForm()
    {
        $builder = Phake::mock(FormBuilderInterface::CLASS);

        $this->strategy->buildForm($builder, array());

        Phake::verify($builder)->add('contentNodeId', 'choice', array(
            'label' => 'open_orchestra_elastica_admin.form.elastica_search.node',
            'choices' => array($this->nodeId => $this->nodeName),
            'constraints' => new NotBlank(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }
}
