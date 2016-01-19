<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Model;

use Lug\Component\Grid\Exception\OptionNotFoundException;
use Lug\Component\Grid\Model\Filter as BaseFilter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Filter extends BaseFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $form;

    /**
     * @var mixed[]
     */
    private $formOptions;

    /**
     * @param string  $name
     * @param string  $label
     * @param string  $type
     * @param string  $form
     * @param mixed[] $formOptions
     * @param mixed[] $options
     */
    public function __construct($name, $label, $type, $form, array $formOptions = [], array $options = [])
    {
        parent::__construct($name, $label, $type, $options);

        $this->form = $form;
        $this->formOptions = $formOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFormOptions()
    {
        return !empty($this->formOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFormOption($name)
    {
        return isset($this->formOptions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOption($name)
    {
        if (!$this->hasFormOption($name)) {
            throw new OptionNotFoundException(sprintf('The filter form option "%s" could not be found.', $name));
        }

        return $this->formOptions[$name];
    }
}
