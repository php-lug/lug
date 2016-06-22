<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Lug\Bundle\ResourceBundle\DataFixtures\ORM\AbstractDriverFixture;
use Symfony\Component\Intl\Intl;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLocaleFixture extends AbstractDriverFixture
{
    /**
     * {@inheritdoc}
     */
    protected function doLoad(ObjectManager $manager)
    {
        $defaultCode = $this->getContainer()->getParameter('lug.locale');

        foreach (array_keys(Intl::getLocaleBundle()->getLocaleNames()) as $code) {
            $locale = $this->getFactory()->create([
                'code'     => $code,
                'enabled'  => $code === $defaultCode,
                'required' => $code === $defaultCode,
            ]);

            $manager->persist($locale);
            $this->setReference('lug.locale.'.$code, $locale);
        }

        $manager->flush();
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'locale';
    }
}
