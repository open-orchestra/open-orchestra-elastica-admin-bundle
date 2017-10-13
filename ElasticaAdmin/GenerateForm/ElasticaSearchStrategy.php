<?php

namespace OpenOrchestra\ElasticaAdmin\GenerateForm;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractBlockStrategy
{
    protected $nodeRepository;
    protected $contextManager;

    /**
     * @param NodeRepositoryInterface     $nodeRepository,
     * @param ContextBackOfficeInterface  $contextManager,
     * @param array                       $basicBlockConfiguration
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        ContextBackOfficeInterface $contextManager,
        array $basicBlockConfiguration
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->contextManager = $contextManager;
        parent::__construct($basicBlockConfiguration);
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return 'elastica_search' === $block->getComponent();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentNodeId', 'choice', array(
            'choices' => $this->getSpecialPageList(),
            'label' => 'open_orchestra_elastica_admin.form.elastica_search.node',
            'constraints' => new NotBlank(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * get special pages list
     *
     * @return array
     */
    protected function getSpecialPageList() {
        $siteId = $this->contextManager->getSiteId();
        $language = $this->contextManager->getSiteDefaultLanguage();
        $specialPages = $this->nodeRepository->findAllSpecialPage($language, $siteId);

        $specialPageChoice = array();
        foreach ($specialPages as $node) {
            $specialPageChoice[$node->getId()] = $node->getName();
        }

        return $specialPageChoice;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'elastica_search';
    }
}
