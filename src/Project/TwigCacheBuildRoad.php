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
use Chigi\Chiji\Plugin\TwigCache\RenderFixTaskQueue;
use Chigi\Chiji\Plugin\TwigCache\TwigCacheRoadExtensionInterface;
use Chigi\Chiji\Project\BuildRoad;
use Chigi\Component\IO\File;

/**
 * Build Road For Twig Cache.
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class TwigCacheBuildRoad extends BuildRoad {

    private $regex = '.+\.[a-zA-Z0-9\-_]+\.twig$';

    function getRegex() {
        return $this->regex;
    }

    /**
     * Set the regex for file path.
     * 
     * @param string $regex
     */
    function setRegex($regex) {
        $this->regex = $regex;
        return $this;
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
     * @var array {"obj":\Twig_Environment,"token":"md5($this->getSourceDir()->getAbsolutePath())"}
     */
    private $last_twig = null;

    private function compile(AbstractResourceFile $resource) {
        if (isset($this->last_twig["token"]) && md5($this->getSourceDir()->getAbsolutePath()) === $this->last_twig['token']) {
            // Continue to use the last twig environment object.
        } else {
            $this->last_twig = array(
                "obj" => new \Twig_Environment(new \Twig_Loader_Filesystem($this->getSourceDir()->getAbsolutePath()), array()),
                "token" => \md5($this->getSourceDir()->getAbsolutePath())
            );
            foreach ($this->extensions as $extension) {
                $this->last_twig["obj"]->addExtension($extension);
                $extension->setResource($resource);
            }
        }
        $twig = $this->last_twig["obj"];
        $template = $twig->loadTemplate($resource->getRelativePath($this->getSourceDir()));
        $template_str = $template->render(array());
        while (($fix = RenderFixTaskQueue::getInstance()->next($resource)) !== FALSE) {
            $template_str = $fix->execute($template_str);
        }
        return $template_str;
    }
    
    /**
     *
     * @var TwigCacheRoadExtensionInterface[]
     */
    private $extensions = array();
    
    public function addExtension(TwigCacheRoadExtensionInterface $extension) {
        \array_push($this->extensions, $extension);
    }

}
