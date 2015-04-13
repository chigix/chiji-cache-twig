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
 * The Queue for storing ResourceFixTask.
 *
 * @author Richard Lea <chigix@zoho.com>
 */
class RenderFixTaskQueue {

    /**
     *
     * @var ResourceFixTask[]
     */
    private $queue;
    private static $instance = null;

    /**
     * 
     * @return RenderFixTaskQueue
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new RenderFixTaskQueue();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->queue = array();
    }

    /**
     * @param AbstractResourceFile $resource the next task to find matching the target resource.
     * @return RenderFixTask False if no matching task.
     */
    public function next(AbstractResourceFile $resource) {
        foreach ($this->queue as $key => $task) {
            if ($task->getResource()->getMemberId() === $resource->getMemberId()) {
                unset($this->queue[$key]);
                return $task;
            }
        }
        return FALSE;
    }

    public function push(RenderFixTask $fixTask) {
        $this->queue[uniqid()] = $fixTask;
    }

}
