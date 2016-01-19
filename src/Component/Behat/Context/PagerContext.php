<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Behat\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkAwareContext;
use Lug\Component\Behat\Dictionary\MinkDictionary;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PagerContext implements MinkAwareContext
{
    use MinkDictionary;

    /**
     * @param string|int $page
     *
     * @Given I follow the page ":page"
     */
    public function followPage($page)
    {
        \PHPUnit_Framework_Assert::assertNotNull(
            $link = $this->findPager()->findLink($page),
            sprintf('The page "%s" could not be found.', $page)
        );

        $link->click();
    }

    /**
     * @param string|int $count
     *
     * @Given I should see a pager with ":count" pages
     */
    public function assertPager($count)
    {
        \PHPUnit_Framework_Assert::assertCount(
            $count + ($offset = 2),
            $pages = $this->findPager()->findAll('xpath', '/li'),
            sprintf('The number of pages "%d" does not match "%d".', count($pages) - $offset, $count)
        );
    }

    /**
     * @Given I should not see a pager
     */
    public function assertNotPager()
    {
        \PHPUnit_Framework_Assert::assertNull($this->getPager(), 'The pager could be found.');
    }

    /**
     * @return NodeElement
     */
    public function findPager()
    {
        \PHPUnit_Framework_Assert::assertNotNull($pager = $this->getPager(), 'The pager could not be found.');

        return $pager;
    }

    /**
     * @return NodeElement|null
     */
    private function getPager()
    {
        $xpath = '//ul[contains(@class, "pagination")]';

        return $this->getPage()->find('xpath', $xpath);
    }
}
