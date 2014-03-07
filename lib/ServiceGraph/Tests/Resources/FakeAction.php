<?php

namespace ServiceGraph\Tests\Resources;

use ServiceGraph\Action\Action;

class FakeAction extends Action
{
    public function fakeAction($arg1, $arg2)
    {
        return $arg1 + $arg2;
    }
}
