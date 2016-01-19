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
    private $controller;

    /**
     * @var string
     */
    private $factory;

    /**
     * @var string
     */
    private $repository;

    /**
     * @var string
     */
    private $domainManager;

    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $choiceForm;

    /**
     * @var string
     */
    private $idPropertyPath;

    /**
     * @var string
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
     * @param string                 $controller
     * @param string                 $factory
     * @param string                 $repository
     * @param string                 $domainManager
     * @param string                 $form
     * @param string                 $choiceForm
     * @param string                 $idPropertyPath
     * @param string                 $labelPropertyPath
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
        $controller,
        $factory,
        $repository,
        $domainManager,
        $form,
        $choiceForm,
        $idPropertyPath,
        $labelPropertyPath,
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
        $this->setController($controller);
        $this->setFactory($factory);
        $this->setRepository($repository);
        $this->setDomainManager($domainManager);
        $this->setForm($form);
        $this->setChoiceForm($choiceForm);
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
