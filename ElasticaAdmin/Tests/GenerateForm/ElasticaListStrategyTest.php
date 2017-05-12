<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\GenerateForm;

use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use OpenOrchestra\ElasticaAdmin\GenerateForm\ElasticaListStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Phake;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Test ElasticaListStrategyTest
 */
class ElasticaListStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaListStrategy
     */
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy = new ElasticaListStrategy(array());
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
        $this->assertSame('elastica_list', $this->strategy->getName());
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
            'elastica search block' => array(false, 'elastica_search'),
            'elastica list block' => array(true, 'elastica_list'),
            'foo block' => array(false, 'foo'),
            'bar block' => array(false, 'bar'),
        );
    }

    /**
     * Test default configuration
     */
    public function testGetDefaultConfiguration()
    {
        $this->assertSame(array('maxAge' => 0, 'searchLimit' => 10), $this->strategy->getDefaultConfiguration());
    }

    /**
     * Test build form
     */
    public function testBuildForm()
    {
        $builder = Phake::mock(FormBuilderInterface::CLASS);

        $this->strategy->buildForm($builder, array());

        Phake::verify($builder)->add('searchLimit', 'integer', array(
            'label' => 'open_orchestra_elastica_admin.form.elastica_list.search_limit',
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }
}
