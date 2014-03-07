service-graph
=============

A dead simple library for building service layers that dont suck.

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
