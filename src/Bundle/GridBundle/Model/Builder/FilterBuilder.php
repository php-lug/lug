<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Model\Builder;

use Lug\Bundle\GridBundle\Model\Filter;
use Lug\Bundle\ResourceBundle\Util\ClassUtils;
use Lug\Component\Grid\Model\Builder\FilterBuilder as BaseFilterBuilder;
use Lug\Component\Registry\Model\RegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FilterBuilder extends BaseFilterBuilder
{
    /**
     * @var RegistryInterface
     */
    private $filterFormRegistry;

    /**
     * @param RegistryInterface $filterFormRegistry
     */
    public function __construct(RegistryInterface $filterFormRegistry)
    {
        $this->filterFormRegistry = $filterFormRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $config)
    {
        return new Filter(
            $this->buildName($config),
            $this->buildLabel($config),
            $this->buildType($config),
            $this->buildForm($config),
            $this->buildFormOptions($config),
            $this->buildOptions($config)
        );
    }

    /**
     * @param mixed[] $config
     *
     * @return string
     */
    protected function buildForm(array $config)
    {
        return isset($config['form'])
            ? $config['form']
            : ClassUtils::getClass($this->filterFormRegistry[$this->buildType($config)]);
    }

    /**
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    protected function buildFormOptions(array $config)
    {
        return isset($config['form_options']) ? $config['form_options'] : [];
    }
}
