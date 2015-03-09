<?php

/*
 * This file is part of the chiji-cache-twig package.
 * 
 * (c) Richard Lea <chigix@zoho.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chigi\Chiji\Plugin\TwigCache\Project;

use Chigi\Chiji\Exception\CacheBuildFileException;
use Chigi\Chiji\File\AbstractResourceFile;
use Chigi\Chiji\Project\BuildRoad;
use Chigi\Component\IO\File;

/**
 * Build Road for Twig cache.
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class TwigCacheBuildRoad extends BuildRoad {

    public function getRegex() {
        return '.+\.[a-zA-Z0-9\-_]+\.twig$';
    }

    public function buildCache(AbstractResourceFile $resource) {
        $this->getParentProject()->getCacheManager()->registerDirectory($resource->getFile()->getAbsoluteFile()->getParentFile());
        $cache_dir = $this->getParentProject()->getCacheManager()->searchCacheDir($resource->getFile()->getAbsoluteFile()->getParentFile());
        if (\is_null($cache_dir) || $cache_dir->isFile()) {
            throw new CacheBuildFileException("[" . $this->getParentProject()->getProjectName() . "]Cache Build Failed: " . $resource->getFile()->getAbsolutePath());
        }
        if (!$cache_dir->exists()) {
            $cache_dir->mkdirs();
        }
        $cache_file = new File(substr($resource->getFile()->getName(), 0, -5), $cache_dir->getAbsolutePath());
        \file_put_contents($cache_file->getAbsolutePath(), $this->compile($resource));
        $this->getParentProject()->getCacheManager()->registerCache($resource, $cache_file);
    }

    /**
     * 
     * @param AbstractResourceFile $resource The twig file to compile.
     * @return string The rendered template string.
     */
    private function compile(AbstractResourceFile $resource) {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->getSourceDir()->getAbsolutePath()), array());
        $template = $twig->loadTemplate($resource->getRelativePath($this->getSourceDir()));
        return $template->render(array());
    }

}
