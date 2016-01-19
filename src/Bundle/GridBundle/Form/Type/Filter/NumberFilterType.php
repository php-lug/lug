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

use Lug\Bundle\GridBundle\Form\EventSubscriber\Filter\NumberFilterSubscriber;
use Lug\Component\Grid\Filter\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class NumberFilterType extends AbstractFilterType
{
    /**
     * @var NumberFilterSubscriber
     */
    private $numberFilterSubscriber;

    /***
     * @param NumberFilterSubscriber $numberFilterSubscriber
     */
    public function __construct(NumberFilterSubscriber $numberFilterSubscriber)
    {
        $this->numberFilterSubscriber = $numberFilterSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('type', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(function ($choice) use ($options) {
                        return $options['label_prefix'].'.type.'.$choice;
                    }, $choices = NumberType::getTypes()),
                    $choices
                ),
                'choices_as_values'        => true,
                'xml_http_request_trigger' => true,
            ])
            ->addEventSubscriber($this->numberFilterSubscriber));
    }
}
