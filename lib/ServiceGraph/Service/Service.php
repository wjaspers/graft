<?php

namespace ServiceGraph\Service;

use DomainException;
use InvalidArgumentException;
use LogicException;
use ServiceGraph\Action\Action;

class Service
{
    /**
     * @var array
     */
    protected $actionList = array();


    /**
     * Locates an action on the Service.
     *
     * @param string $name
     * @return Action|null
     */
    public function getAction($name)
    {
        if ($this->hasAction($name)) {
            return $this->actionList[$name];
        } 

        return null;
    }


    /**
     * Determines if an action is registered.
     *
     * @param string $name
     * @return boolean
     */
    public function hasAction($name)
    {
        return array_key_exists($name, $this->actionList);
    }


    /**
     * Returns the actions registered on this service.
     *
     * @return array
     */
    public function getActionList()
    {
        return $this->actionList;
    }


    /**
     * Executes an action on the service.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws DomainException
     */
    public function __call($name, array $arguments)
    {
        $action = $this->getAction($name);
        if ($action) {
            $callable = array($action, $name);
            return call_user_func_array($callable, $arguments);
        }

        $message = 'Action "%s" not found on service "%s"';
        $message = sprintf($message, $name, get_called_class());
        throw new DomainException($message);
    }


    /**
     * Registers an action on this service.
     *
     * @param string $name
     * @param Action $action
     * @return Service
     * @throws DomainException
     */
    public function register($name, Action $action)
    {
        // Check that the intended method name is valid.
        if (! is_callable($name, true)) {
            throw new InvalidArgumentException('Cannot register an Action without a useful name.');
        }

        $callable = array(
            $action,
            $action->getCallbackName(),
        );

        // Make sure the callback exists.
        if (! is_callable($callable)) {
            throw new LogicException('Cannot register an Action without a valid callback');
        }

        // Make sure we dont already have an action by this name.
        if ($this->hasAction($name)) {
            $message = 'Action "%s" is already registered!';
            $message = sprintf($message, $name);
            throw new LogicException($message);
        }

        $this->actionList[$name] = $action;
        return $this;
    }


    /**
     * Removes an action from this service.
     * 
     * @param Action|string $name
     * @return Service
     */
    public function remove($name)
    {
        if ($this->hasAction($name)) {
            unset($this->actionList[$name]);
        }

        return $this;
    }
}
