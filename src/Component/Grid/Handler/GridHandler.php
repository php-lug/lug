<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Handler;

use Lug\Component\Grid\Filter\FiltererInterface;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Grid\Slicer\SlicerInterface;
use Lug\Component\Grid\Sort\SorterInterface;
use Lug\Component\Grid\View\GridViewFactoryInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridHandler implements GridHandlerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @var GridViewFactoryInterface
     */
    private $gridViewFactory;

    /**
     * @var FiltererInterface
     */
    private $filterer;

    /**
     * @var SorterInterface
     */
    private $sorter;

    /**
     * @var SlicerInterface
     */
    private $slicer;

    /**
     * @param ServiceRegistryInterface $repositoryRegistry
     * @param GridViewFactoryInterface $gridViewFactory
     * @param FiltererInterface        $filterer
     * @param SorterInterface          $sorter
     * @param SlicerInterface          $slicer
     */
    public function __construct(
        ServiceRegistryInterface $repositoryRegistry,
        GridViewFactoryInterface $gridViewFactory,
        FiltererInterface $filterer,
        SorterInterface $sorter,
        SlicerInterface $slicer
    ) {
        $this->repositoryRegistry = $repositoryRegistry;
        $this->gridViewFactory = $gridViewFactory;
        $this->filterer = $filterer;
        $this->sorter = $sorter;
        $this->slicer = $slicer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GridInterface $grid, array $filters = [], array $sorting = [], array $slicing = [])
    {
        $repository = $this->repositoryRegistry[$grid->getResource()->getName()];
        $dataSourceBuilder = $repository->createDataSourceBuilder($grid->getOptions());

        $this->filterer->filter($dataSourceBuilder, $grid, $filters);
        $this->sorter->sort($dataSourceBuilder, $grid, $sorting);
        $this->slicer->slice($dataSourceBuilder, $grid, $slicing);

        return $this->gridViewFactory->create($grid, $dataSourceBuilder);
    }
}
