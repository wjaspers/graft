<?php

namespace ServiceGraph\Tests\Service;

use ServiceGraph\Action\Action;
use ServiceGraph\Service\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    protected $service;


    public function setUp()
    {
        parent::setUp();
        $this->service = new Service;
    }


    public function tearDown()
    {
        parent::tearDown();
        unset($this->service);
    }


    /**
     * @param string $callbackName
     * @return PHPUnit_Framework_Mock_Object
     */
    protected function getMockAction($callbackName)
    {
        $mock = $this->getMockBuilder('ServiceGraph\Tests\Resources\FakeAction')
            ->setMethods(array(
                $callbackName,
            ))
            ->getMock();
        $mock->setCallbackName($callbackName);
        return $mock;
    }


    public function testHasAction()
    {
        $mockAction = $this->getMockAction('newInTown');
        $result = $this->service->hasAction('newInTown');
        $this->assertFalse($result);

        $this->service->register('hello', $mockAction);
        $result = $this->service->hasAction('hello');
        $this->assertTrue($result);
    }


    public function testRegister()
    {
        $mockAction = $this->getMockAction('doStuff');

        // Ensure we can assign an action to our service.
        $this->service->register('doStuff', $mockAction);
        $actionList = $this->service->getActionList();
        $this->assertSame($mockAction, end($actionList));
    }


    public function testRegisterThrowsExceptionIfAliasIsNotCallable()
    {
        $mockAction = $this->getMockAction('doStuff');

        $this->setExpectedException('InvalidArgumentException', 'Cannot register an Action without a useful name.');
        $this->service->register(909, $mockAction);
    }


    public function testRegisterThrowsExceptionIfCallbackIsNotCallable()
    {
        $mockAction = $this->getMockAction('doStuff');
        $mockAction->setCallbackName('theresNoWayThisWillWork');
        $this->setExpectedException('LogicException', 'Cannot register an Action without a valid callback');
        $this->service->register('doStuff', $mockAction);
    }


    public function testRegisterThrowsExceptionIfActionExists()
    {
        $mockAction = $this->getMockAction('doStuff');
        $this->service->register('doStuff', $mockAction);

        // Ensure the action can't be unexpectedly replaced.
        $this->setExpectedException('LogicException', 'Action "doStuff" is already registered!');
        $this->service->register('doStuff', $mockAction);
    }


    public function testCall()
    {
        /**
         * A fake callback to use.
         * We expect to get the two numbers added together.
         */
        $fakeCall = function ($arg1, $arg2) {
            return $arg1 + $arg2;
        };
        $arguments = array(
            1,
            2,
        );
        $mockAction = $this->getMockAction('addNumbers');
        $mockAction->expects($this->once())
            ->method('addNumbers')
            ->with($arguments[0], $arguments[1])
            ->will($this->returnCallback($fakeCall));
        $this->service->register('addNumbers', $mockAction);
        $result = $this->service->addNumbers($arguments[0], $arguments[1]);
        $this->assertSame(3, $result, 'Unexpected result returned from Service::fakeAction');
    }


    public function testCallThrowsException()
    {
        $this->setExpectedException('DomainException', 'Action "cantFindMe" not found on service "ServiceGraph\Service\Service"');
        $this->service->cantFindMe();
    }


    public function testRemove()
    {
        $mockAction = $this->getMockAction('helpMe');
        $this->service->register('helpMe', $mockAction);
        $this->service->remove('helpMe');
        $result = $this->service->hasAction('helpMe');
        $this->assertFalse($result, 'Service still has "helpMe" action.');
    }
}
