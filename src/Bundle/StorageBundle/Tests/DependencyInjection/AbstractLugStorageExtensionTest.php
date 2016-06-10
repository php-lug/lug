<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\StorageBundle\Tests\DependencyInjection;

use Doctrine\Common\Cache\Cache;
use Lug\Bundle\StorageBundle\DependencyInjection\LugStorageExtension;
use Lug\Component\Storage\Model\CookieStorage;
use Lug\Component\Storage\Model\DoctrineStorage;
use Lug\Component\Storage\Model\SessionStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugStorageExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugStorageExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new LugStorageExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testCookieDisabled()
    {
        $this->compileContainer();

        $this->assertFalse($this->container->has('lug.storage.cookie'));
    }

    public function testCookieEnabled()
    {
        $this->container->set('request_stack', $this->createRequestStackMock());
        $this->compileContainer('cookie');

        $this->assertInstanceOf(CookieStorage::class, $this->container->get('lug.storage.cookie'));
    }

    public function testDoctrineDisabled()
    {
        $this->compileContainer();

        $this->assertFalse($this->container->has('lug.storage.doctrine'));
    }

    public function testDoctrineEnabled()
    {
        $this->container->set('doctrine.cache', $this->createCacheMock());
        $this->compileContainer('doctrine');

        $this->assertInstanceOf(DoctrineStorage::class, $this->container->get('lug.storage.doctrine'));
    }

    public function testSessionDisabled()
    {
        $this->compileContainer();

        $this->assertFalse($this->container->has('lug.storage.session'));
    }

    public function testSessionEnabled()
    {
        $this->container->set('session', $this->createSessionMock());
        $this->compileContainer('session');

        $this->assertInstanceOf(SessionStorage::class, $this->container->get('lug.storage.session'));
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    /**
     * @param string|null $configuration
     */
    private function compileContainer($configuration = null)
    {
        if ($configuration !== null) {
            $this->loadConfiguration($this->container, $configuration);
        }

        $this->container->compile();
    }

    /**
     * @return RequestStack|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestStackMock()
    {
        return $this->createMock(RequestStack::class);
    }

    /**
     * @return Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCacheMock()
    {
        return $this->createMock(Cache::class);
    }

    /**
     * @return SessionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSessionMock()
    {
        return $this->createMock(SessionInterface::class);
    }
}
