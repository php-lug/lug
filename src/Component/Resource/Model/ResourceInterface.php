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
interface ResourceInterface
{
    const DRIVER_DOCTRINE_ORM = 'doctrine/orm';
    const DRIVER_DOCTRINE_MONGODB = 'doctrine/mongodb';

    const DRIVER_MAPPING_FORMAT_ANNOTATION = 'annotation';
    const DRIVER_MAPPING_FORMAT_XML = 'xml';
    const DRIVER_MAPPING_FORMAT_YAML = 'yaml';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string[]
     */
    public function getInterfaces();

    /**
     * @return string
     */
    public function getModel();

    /**
     * @param string $model
     */
    public function setModel($model);

    /**
     * @return string|null
     */
    public function getDriver();

    /**
     * @param string|null $driver
     */
    public function setDriver($driver);

    /**
     * @return string|null
     */
    public function getDriverMappingPath();

    /**
     * @param string|null $driverMappingPath
     */
    public function setDriverMappingPath($driverMappingPath);

    /**
     * @return string|null
     */
    public function getDriverMappingFormat();

    /**
     * @param string|null $driverMappingFormat
     */
    public function setDriverMappingFormat($driverMappingFormat);

    /**
     * @return string|null
     */
    public function getDriverManager();

    /**
     * @param string|null $driverManager
     */
    public function setDriverManager($driverManager);

    /**
     * @return string|null
     */
    public function getRepository();

    /**
     * @param string|null $repository
     */
    public function setRepository($repository);

    /**
     * @return string|null
     */
    public function getFactory();

    /**
     * @param string|null $factory
     */
    public function setFactory($factory);

    /**
     * @return string|null
     */
    public function getForm();

    /**
     * @param string|null $form
     */
    public function setForm($form);

    /**
     * @return string|null
     */
    public function getChoiceForm();

    /**
     * @param string|null $choiceForm
     */
    public function setChoiceForm($choiceForm);

    /**
     * @return string|null
     */
    public function getDomainManager();

    /**
     * @param string|null $domainManager
     */
    public function setDomainManager($domainManager);

    /**
     * @return string|null
     */
    public function getController();

    /**
     * @param string|null $controller
     */
    public function setController($controller);

    /**
     * @return string|null
     */
    public function getIdPropertyPath();

    /**
     * @param string|null $idPropertyPath
     */
    public function setIdPropertyPath($idPropertyPath);

    /**
     * @return string|null
     */
    public function getLabelPropertyPath();

    /**
     * @param string|null $labelPropertyPath
     */
    public function setLabelPropertyPath($labelPropertyPath);

    /**
     * @return ResourceInterface|null
     */
    public function getTranslation();

    /**
     * @param ResourceInterface|null $translation
     */
    public function setTranslation(ResourceInterface $translation = null);
}
