<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 9:57
 */

namespace Core\Security\CloudValkyrie\Scanners;


use Core\Joi\System\Exceptions\ClassNotFoundHttpException;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Contracts\SecurityEventHandler;
use Core\Security\CloudValkyrie\Defaults\DefaultEvents;
use Core\Security\CloudValkyrie\ScanResults;


abstract class Scanner
{
    protected $scanners = [];

    protected $threats = [];

    protected ScanResults $scanResults;

    public  $allEventsHandler;

    public function register($name,  $callback)
    {

        if ($name === null || $name === '') {
            return false;
        }

        $class = $this->loadClass($this->getClass($callback));

        if (!$this->isValidEventScan($class)) {
            return false;
        }

        $this->scanners[$name]['fn'] = $class;
        return true;
    }

    /**
     * adds a event listener for a single scan
     * @param $name
     * @param SecurityEventHandler $callback
     * @return bool
     */
    public function addEventListener($name, $callback)
    {

        $class = $this->loadClass($this->getClass($callback));

        if (!$this->isValidHandler($class)) {
            return false;
        }

        if($name === '*' || $name === DefaultEvents::PROTECTION_ALL){
            $this->allEventsHandler = $class;
        }else{
            if (!empty($this->scanners[$name])) {
                return false;
            }
            $this->scanners[$name]['handle'] = $class;
        }

        return true;
    }

    protected function getEventScan(EventScan $callback): EventScan
    {
        return $callback;
    }


    /**
     * @param $handler
     * @param $event
     */
    public function fireEvent($handler, $event)
    {
        if($handler !== null){
            if ($event === 'before') {
                $handler->beforeScan();
            } else {
                $handler->afterScan($this->scanResults);
            }
        }
    }

    /**
     *
     */
    public function getResults()
    {
        return $this->threats;
    }


    public function getClass($callback): ?string
    {
        if (is_array($callback) === true && count($callback) > 0) {
            return $callback[0];
        }

        if (is_string($callback) === true) {
            if ($callback[0] !== '\\') {
                $callback = '\\' . $callback;
            }
            return $callback;
        }

        return null;
    }

    /**
     * Load class
     *
     * @param string $class
     * @return object
     * @throws ClassNotFoundHttpException
     */
    protected function loadClass(string $class)
    {
        if (class_exists($class) === false) {
            throw new ClassNotFoundHttpException($class, null, sprintf('Class "%s" does not exist', $class), 404, null);
        }

        return new $class();
    }

    /**
     * checkes if the handler is valid
     *
     * @param object $class
     */
    protected function isValidHandler(object $class): bool
    {
        if ($class instanceof SecurityEventHandler) {
            return true;
        }
        return false;
    }

    /**
     * checkes if the Event Scan is valid
     *
     * @param object $class
     */
    protected function isValidEventScan(object $class): bool
    {
        if ($class instanceof EventScan) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function getAllEventsHandler()
    {
        if (isset($this->allEventsHandler)) {
            return $this->allEventsHandler;
        }
        return null;
    }


}