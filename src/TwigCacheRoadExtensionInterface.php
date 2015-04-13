<?php

/*
 * This file is part of the chiji-cache-twig package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Plugin\TwigCache;

use Chigi\Chiji\File\AbstractResourceFile;

/**
 * Twig Cache Road Extension
 *
 * @author Richard Lea <chigix@zoho.com>
 */
interface TwigCacheRoadExtensionInterface extends \Twig_ExtensionInterface {

    /**
     * Set the relating Project to this extension.
     * 
     * @param AbstractResourceFile $resource
     */
    public function setResource(AbstractResourceFile $resource);
}
