<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

use A2lix\TranslationFormBundle\A2lixTranslationFormBundle;
use Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Lug\Bundle\AdminBundle\LugAdminBundle;
use Lug\Bundle\GridBundle\LugGridBundle;
use Lug\Bundle\LocaleBundle\LugLocaleBundle;
use Lug\Bundle\RegistryBundle\LugRegistryBundle;
use Lug\Bundle\ResourceBundle\LugResourceBundle;
use Lug\Bundle\StorageBundle\LugStorageBundle;
use Lug\Bundle\TranslationBundle\LugTranslationBundle;
use Lug\Bundle\UiBundle\LugUiBundle;
use Lug\Component\Resource\Model\ResourceInterface;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class AppKernel extends Kernel
{
    const ENVIRONMENT_PROD = 'prod';
    const ENVIRONMENT_DEV = 'dev';
    const ENVIRONMENT_TEST = 'test';

    /**
     * @var string
     */
    private $driver;

    /**
     * {@inheritdoc}
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        $this->driver = getenv('LUG_DRIVER') ?: ResourceInterface::DRIVER_DOCTRINE_ORM;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new MonologBundle(),
            new FOSRestBundle(),
            new JMSSerializerBundle(),
            new BazingaHateoasBundle(),
            new A2lixTranslationFormBundle(),
            new WhiteOctoberPagerfantaBundle(),
            new StofDoctrineExtensionsBundle(),

            new LugAdminBundle(),
            new LugGridBundle(),
            new LugLocaleBundle($this->driver),
            new LugResourceBundle(),
            new LugStorageBundle(),
            new LugTranslationBundle(),
            new LugUiBundle(),

            // FIXME - The registry bundle must be defined after all lug bundles
            // https://github.com/symfony/symfony/issues/13609
            new LugRegistryBundle(),

            // FIXME - The knp bundle must be defined after the lug ui bundle
            // https://github.com/symfony/symfony/issues/13609
            new KnpMenuBundle(),
        ];

        // FIXME - The doctrine bundles must be defined after the lug resource bundle
        // https://github.com/symfony/symfony/issues/13609
        if ($this->driver === ResourceInterface::DRIVER_DOCTRINE_ORM) {
            $bundles[] = new DoctrineBundle();
        } elseif ($this->driver === ResourceInterface::DRIVER_DOCTRINE_MONGODB) {
            $bundles[] = new DoctrineMongoDBBundle();
        }

        if (in_array($this->getEnvironment(), [self::ENVIRONMENT_DEV, self::ENVIRONMENT_TEST], true)) {
            $bundles[] = new DebugBundle();
            $bundles[] = new WebProfilerBundle();
            $bundles[] = new SensioDistributionBundle();

            if ($this->driver === ResourceInterface::DRIVER_DOCTRINE_ORM) {
                $bundles[] = new DoctrineFixturesBundle();
            }
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');

        $this->registerDriverConfiguration($loader);
    }

    /**
     * @param LoaderInterface $loader
     */
    private function registerDriverConfiguration(LoaderInterface $loader)
    {
        $driver = null;

        if ($this->driver === ResourceInterface::DRIVER_DOCTRINE_ORM) {
            $driver = 'doctrine.yml';
        } elseif ($this->driver === ResourceInterface::DRIVER_DOCTRINE_MONGODB) {
            $driver = 'doctrine_mongodb.yml';
        }

        if ($driver === null) {
            return;
        }

        $basePath = $this->getRootDir().'/config/bundles/';

        if (file_exists($path = $basePath.$driver)) {
            $loader->load($path);
        }

        if (file_exists($path = $basePath.$this->getEnvironment().'/'.$driver)) {
            $loader->load($path);
        }
    }
}
