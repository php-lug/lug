<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\Type\Filter;

use Lug\Bundle\GridBundle\Form\EventSubscriber\Filter\ResourceFilterSubscriber;
use Lug\Component\Grid\Filter\Type\ResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceFilterType extends AbstractFilterType
{
    /**
     * @var ResourceFilterSubscriber
     */
    private $resourceFilterSubscriber;

    /**
     * @param ResourceFilterSubscriber $resourceFilterSubscriber
     */
    public function __construct(ResourceFilterSubscriber $resourceFilterSubscriber)
    {
        $this->resourceFilterSubscriber = $resourceFilterSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('type', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(function ($choice) use ($options) {
                        return $options['label_prefix'].'.type.'.$choice;
                    }, $choices = ResourceType::getTypes()),
                    $choices
                ),
                'choices_as_values'        => true,
                'xml_http_request_trigger' => true,
            ])
            ->addEventSubscriber($this->resourceFilterSubscriber));
    }
}
