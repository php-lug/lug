<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Behat\Dictionary;

use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use Lug\Bundle\ResourceBundle\Behat\Dictionary\ResourceDictionary;
use Lug\Component\Resource\Factory\FactoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait LocaleDictionary
{
    use ResourceDictionary;

    /**
     * @Given the application has its default locale configured
     */
    public function createDefaultLocale()
    {
        $this->createLocale(
            $this->getParameter('lug.locale'),
            true,
            true,
            new \DateTime('2015-01-01 00:00:00'),
            new \DateTime('2015-01-01 00:00:01')
        );
    }

    /**
     * @Given there are the locales:
     */
    public function createLocales(TableNode $table)
    {
        foreach ($table as $row) {
            $this->createLocale(
                $row['code'],
                isset($row['enabled']) ? $row['enabled'] === 'yes' : false,
                isset($row['required']) ? $row['required'] === 'yes' : false,
                isset($row['createdAt']) && !empty($row['createdAt']) ? new \DateTime($row['createdAt']) : null,
                isset($row['updatedAt']) && !empty($row['updatedAt']) ? new \DateTime($row['updatedAt']) : null
            );
        }

        $this->getLocaleManager()->flush();
    }

    /**
     * @param string         $code
     * @param bool           $enabled
     * @param bool           $required
     * @param \DateTime|null $createdAt
     * @param \DateTime|null $updatedAt
     * @param bool           $flush
     */
    private function createLocale(
        $code,
        $enabled = false,
        $required = false,
        \DateTime $createdAt = null,
        \DateTime $updatedAt = null,
        $flush = true
    ) {
        $options = [
            'code'     => $code,
            'enabled'  => $enabled,
            'required' => $required,
        ];

        if ($createdAt !== null) {
            $options['createdAt'] = $createdAt;
        }

        if ($updatedAt !== null) {
            $options['updatedAt'] = $updatedAt;
        }

        $locale = $this->getLocaleFactory()->create($options);

        $manager = $this->getLocaleManager();
        $manager->persist($locale);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * @return FactoryInterface
     */
    private function getLocaleFactory()
    {
        return $this->getFactory('locale');
    }

    /**
     * @return ObjectManager
     */
    private function getLocaleManager()
    {
        return $this->getManager('locale');
    }
}
