<?php

namespace OpenOrchestra\ElasticaAdmin\EventSubscriber;

use OpenOrchestra\Backoffice\Event\FieldFormEvent;
use OpenOrchestra\Backoffice\Form\FieldFormEvents;
use OpenOrchestra\ElasticaAdmin\Mapper\FieldToElasticaTypeMapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class ContentTypeFieldChangeSubscriber
 */
class ContentTypeFieldChangeSubscriber implements EventSubscriberInterface
{
    protected $mapper;

    /**
     * @param FieldToElasticaTypeMapper $mapper
     * @param TranslatorInterface       $translator
     */
    public function __construct(
        FieldToElasticaTypeMapper $mapper,
        TranslatorInterface $translator
    ) {
        $this->mapper = $mapper;
        $this->translator = $translator;
    }

    /**
     * add alert on content type field change type
     *
     * @param FieldFormEvent $event
     */
    public function addChangeTypeAlert(FieldFormEvent $event)
    {
        $builder = $event->getBuilder();
        $formConfig = $builder->get('type')->getFormConfig();

        $builder->add('type', 'choice', array(
            'choices' => $formConfig->getOption('choices'),
            'choice_label' => $formConfig->getOption('choice_label'),
            'choice_attr' => function($val, $key, $index) {
                return [
                    'data-value' => $this->mapper->map($val),
                ];
            },
            'label' => 'open_orchestra_backoffice.form.field_type.type',
            'attr' => array(
                'class' => 'patch-submit-change alert-value-change',
                'data-title' => $this->translator->trans('open_orchestra_elastica_admin.form.field_type.change.title'),
                'data-message' => $this->translator->trans('open_orchestra_elastica_admin.form.field_type.change.message'),
            ),
            'sub_group_id' => 'property',
        ));
   }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FieldFormEvents::FIELD_FORM_CREATION => 'addChangeTypeAlert',
        );
    }
}
