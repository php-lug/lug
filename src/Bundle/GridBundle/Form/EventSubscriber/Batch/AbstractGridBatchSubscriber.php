<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\EventSubscriber\Batch;

use Lug\Bundle\GridBundle\Form\Type\Batch\GridBatchValueType;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractGridBatchSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param ServiceRegistryInterface   $repositoryRegistry
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(
        ServiceRegistryInterface $repositoryRegistry,
        ParameterResolverInterface $parameterResolver
    ) {
        $this->repositoryRegistry = $repositoryRegistry;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $grid = $form->getConfig()->getOption('grid');

        $this->buildForm(
            $form,
            !$this->parameterResolver->resolveApi() ? iterator_to_array($grid->getDataSource()) : []
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $values = isset($data['value']) ? $data['value'] : [];

        if (!is_array($values) || empty($values)) {
            return;
        }

        $config = $form->get('value')->getConfig();
        $choices = $config->getOption('choices');
        $indexedChoices = $config->getOption('choice_list')->getChoices();

        foreach ($values as $key => $value) {
            if (isset($indexedChoices[$value])) {
                unset($values[$key]);
            }
        }

        $this->buildForm($form, array_merge($choices, $this->findChoices(
            $config->getOption('grid')->getDefinition(),
            $this->repositoryRegistry[$config->getOption('grid')->getDefinition()->getResource()->getName()],
            $values
        )));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        ];
    }

    /**
     * @param GridInterface       $grid
     * @param RepositoryInterface $repository
     * @param mixed[]             $choices
     *
     * @return object[]
     */
    abstract protected function findChoices(GridInterface $grid, RepositoryInterface $repository, array $choices);

    /**
     * @param FormInterface $form
     * @param object[]      $choices
     */
    private function buildForm(FormInterface $form, array $choices)
    {
        $form
            ->remove('value')
            ->add('value', GridBatchValueType::class, [
                'choices' => $choices,
                'grid'    => $form->getConfig()->getOption('grid'),
            ]);
    }
}
