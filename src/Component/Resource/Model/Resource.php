<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Model;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Resource implements ResourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $driverManager;

    /**
     * @var string
     */
    private $driverMappingPath;

    /**
     * @var string
     */
    private $driverMappingFormat;

    /**
     * @var string[]
     */
    private $interfaces;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $repository;

    /**
     * @var string|null
     */
    private $factory;

    /**
     * @var string|null
     */
    private $form;

    /**
     * @var string|null
     */
    private $choiceForm;

    /**
     * @var string|null
     */
    private $domainManager;

    /**
     * @var string|null
     */
    private $controller;

    /**
     * @var string|null
     */
    private $idPropertyPath;

    /**
     * @var string|null
     */
    private $labelPropertyPath;

    /**
     * @var ResourceInterface|null
     */
    private $translation;

    /**
     * @param string                 $name
     * @param string                 $driver
     * @param string                 $driverManager
     * @param string                 $driverMappingPath
     * @param string                 $driverMappingFormat
     * @param string|string[]        $interfaces
     * @param string                 $model
     * @param string                 $repository
     * @param string|null            $factory
     * @param string|null            $form
     * @param string|null            $choiceForm
     * @param string|null            $domainManager
     * @param string|null            $controller
     * @param string|null            $idPropertyPath
     * @param string|null            $labelPropertyPath
     * @param ResourceInterface|null $translation
     */
    public function __construct(
        $name,
        $driver,
        $driverManager,
        $driverMappingPath,
        $driverMappingFormat,
        $interfaces,
        $model,
        $repository,
        $factory = null,
        $form = null,
        $choiceForm = null,
        $domainManager = null,
        $controller = null,
        $idPropertyPath = null,
        $labelPropertyPath = null,
        ResourceInterface $translation = null
    ) {
        $this->name = $name;
        $this->interfaces = (array) $interfaces;
        $this->translation = $translation;

        $this->setDriver($driver);
        $this->setDriverManager($driverManager);
        $this->setDriverMappingPath($driverMappingPath);
        $this->setDriverMappingFormat($driverMappingFormat);
        $this->setModel($model);
        $this->setRepository($repository);
        $this->setFactory($factory);
        $this->setForm($form);
        $this->setChoiceForm($choiceForm);
        $this->setDomainManager($domainManager);
        $this->setController($controller);
        $this->setIdPropertyPath($idPropertyPath);
        $this->setLabelPropertyPath($labelPropertyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverManager()
    {
        return $this->driverManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setDriverManager($driverManager)
    {
        $this->driverManager = $driverManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverMappingPath()
    {
        return $this->driverMappingPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setDriverMappingPath($driverMappingPath)
    {
        $this->driverMappingPath = $driverMappingPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverMappingFormat()
    {
        return $this->driverMappingFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function setDriverMappingFormat($driverMappingFormat)
    {
        $this->driverMappingFormat = $driverMappingFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;
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
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoiceForm()
    {
        return $this->choiceForm;
    }

    /**
     * {@inheritdoc}
     */
    public function setChoiceForm($choiceForm)
    {
        $this->choiceForm = $choiceForm;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainManager()
    {
        return $this->domainManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomainManager($domainManager)
    {
        $this->domainManager = $domainManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPropertyPath()
    {
        return $this->idPropertyPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdPropertyPath($idPropertyPath)
    {
        $this->idPropertyPath = $idPropertyPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelPropertyPath()
    {
        return $this->labelPropertyPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelPropertyPath($labelPropertyPath)
    {
        $this->labelPropertyPath = $labelPropertyPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation()
    {
        return $this->translation;
    }
}
