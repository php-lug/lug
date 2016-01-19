<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Behat\Dictionary;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\WebAssert;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
trait MinkDictionary
{
    /**
     * @var Mink
     */
    public $mink;

    /**
     * @var mixed[]
     */
    public $minkParameters;

    /**
     * @return Mink
     */
    public function getMink()
    {
        return $this->mink;
    }

    /**
     * @param Mink $mink
     */
    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * @return mixed[]
     */
    public function getMinkParameters()
    {
        return $this->minkParameters;
    }

    /**
     * @param mixed[] $parameters
     */
    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getMinkParameter($name)
    {
        return isset($this->minkParameters[$name]) ? $this->minkParameters[$name] : null;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setMinkParameter($name, $value)
    {
        $this->minkParameters[$name] = $value;
    }

    /**
     * @param string|null $name
     *
     * @return Session
     */
    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * @param string|null $name
     *
     * @return WebAssert
     */
    public function assertSession($name = null)
    {
        return $this->getMink()->assertSession($name);
    }

    /**
     * @param string|null $name
     *
     * @return DriverInterface
     */
    public function getDriver($name = null)
    {
        return $this->getSession($name)->getDriver();
    }

    /**
     * @param string|null $name
     *
     * @return DocumentElement
     */
    public function getPage($name = null)
    {
        return $this->getSession($name)->getPage();
    }

    /**
     * @param string      $path
     * @param string|null $sessionName
     */
    public function visitPath($path, $sessionName = null)
    {
        $this->getSession($sessionName)->visit($this->locatePath($path));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function locatePath($path)
    {
        $startUrl = rtrim($this->getMinkParameter('base_url'), '/').'/';

        return strpos($path, 'http') !== 0 ? $startUrl.ltrim($path, '/') : $path;
    }

    /**
     * @param string $filename
     * @param string $filepath
     */
    public function saveScreenshot($filename = null, $filepath = null)
    {
        $filename = $filename ?: $this->getMinkParameter('browser_name').'_'.date('c').'_'.uniqid('', true).'.png';
        $filepath = $filepath ?: (ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir());

        file_put_contents($filepath.'/'.$filename, $this->getSession()->getScreenshot());
    }
}
