<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form\Extension;

use Lug\Bundle\ResourceBundle\Form\EventSubscriber\XmlHttpRequestSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class XmlHttpRequestExtension extends AbstractTypeExtension
{
    /**
     * @var XmlHttpRequestSubscriber
     */
    private $xmlHttpRequestSubscriber;

    /**
     * @param XmlHttpRequestSubscriber $xmlHttpRequestSubscriber
     */
    public function __construct(XmlHttpRequestSubscriber $xmlHttpRequestSubscriber)
    {
        $this->xmlHttpRequestSubscriber = $xmlHttpRequestSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['xml_http_request'] || $options['xml_http_request_trigger']) {
            return;
        }

        $builder
            ->add('_xml_http_request', HiddenType::class, ['mapped' => false, 'error_bubbling' => false])
            ->addEventSubscriber($this->xmlHttpRequestSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['xml_http_request'] = $options['xml_http_request'];

        if ($options['xml_http_request']) {
            $view->children['_xml_http_request']->setRendered();
        }

        if ($options['xml_http_request_trigger']) {
            $view->vars['attr']['data-xml-http-request-trigger'] = 'true';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'xml_http_request'         => false,
                'xml_http_request_trigger' => false,
            ])
            ->setAllowedTypes('xml_http_request', 'boolean')
            ->setAllowedTypes('xml_http_request_trigger', 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
