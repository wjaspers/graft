service-graph
=============

A dead simple library for building service layers that dont suck.

# Why use it?
Creating objects with large public interfaces often drive me bananas. I created this so that the public interface of a "Service" is small and flexible. 






# Usage

## Define your Service
```
class DogSitter extends Service {
}
```

## Define your Action(s)
```
class WalkAction extends Action {
   public function getCallbackName()
   {
      return 'walk';
   }
   
   public function walk($name)
   {
      echo sprintf("I'm taking %s for a walk", $name);
   }
}
```

## Bind your action to the Service.
```
$service = new DogSitter;
$action = new WalkAction;
$service->register('goForAWalk', $action);
```

## Execute
```
$service->goForAWalk('Spike');
```

## Profit
`I'm taking Spike for a walk!`


# Example Scenario: The Controller
Lets take a seemingly simple controller.
```
class Controller {
   public function getAction();
   public function postAction();
   public function postSomethingElseAction();
   public function postAnotherThingAction();
   public function throwA404Action();
   public function haveWeAddedEnoughToThisYetAction();
   /* You can see how the object starts to get unmanageable. */
}
```

My solution? Make every action independent functionality.
No fuss, no bloat!

```
class Controller extends \ServiceGraph\Service\Service {
}
```

Now all the functionality for each action is separated, and
easy to replace.
```
class GetAction extends \ServiceGraph\Action\Action {
   public function getCallbackName() {
      return 'getAction';
   }
   
   public function getAction() {
      // do stuff
   }
}
```

```
$myController = new Controller;
$getAction = new GetAction;
$myController->register('getAction', $getAction);
$myController->getAction();
```

How would you handle one action calling another?
```
class Controller extends \ServiceGraph\Service\Service {
   public function register($name, \ServiceGraph\Action\Action $action) {
      parent::register($name, $action);
      $action->setController($this);
   }
}
```

```
class PostAction extends \ServiceGraph\Action\Action {
   public function getCallbackName() {
      return 'postAction';
   }
   
   public function setController($object) {
      $this->caller = $object;
   }
   
   public function postAction() {
      // do stuff ...
      return $this->caller->getAction();
   }
}
```

```
$myController = new Controller;
$getAction = new GetAction;
$postAction = new PostAction;
$myController->register('getAction', $getAction);
$myController->register('postAction', $postAction);
$myController->postAction();
```
