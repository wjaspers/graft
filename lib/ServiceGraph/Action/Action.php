<?php

namespace ServiceGraph\Action;

abstract class Action
{
    /**
     * @var string
     */
    protected $name;


    /**
     * @return string
     */
    public function getCallbackName()
    {
        return $this->name;
    }


    /**
     * Assigns the action's callback name.
     *
     * @param string $name
     * @return Action
     */
    public function setCallbackName($name)
    {
        $this->name = $name;
        return $this;
    }
}
