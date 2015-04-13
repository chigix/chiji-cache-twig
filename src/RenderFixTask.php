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
 * Task Definition for fixing target twig render result.
 *
 * @author Richard Lea <chigix@zoho.com>
 */
interface RenderFixTask {

    /**
     * @return AbstractResourceFile The target resource to be fixed.
     */
    public function getResource();

    /**
     * The Execute Code for this task.
     * @param string $renderStr The Rendered String to be fixed.
     * @return string The Fixed string.
     */
    public function execute($renderStr);
}
