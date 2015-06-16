# mxdiModule
[![Build Status](https://travis-ci.org/mikemix/mxdiModule.svg?branch=master)](https://travis-ci.org/mikemix/mxdiModule) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Total Downloads](https://poser.pugx.org/mikemix/mxdi-module/downloads)](https://packagist.org/packages/mikemix/mxdi-module) [![License](https://poser.pugx.org/mikemix/mxdi-module/license)](https://packagist.org/packages/mikemix/mxdi-module)

Configure dependency injection in Zend Framework 2 using annotations.

Idea based on the [JMSDiExtraBundle](https://github.com/schmittjoh/JMSDiExtraBundle) for the Symfony2 project.

## This code is still in active development and should not be used on production!

### Installation

Install with Composer.

```js
    "require": {
        "mikemix/mxdi-module": "*@dev"
    }
```

Enable via ZF2 config in `appliation.config.php` under `modules` key:

```php
return [
    //
    //
    'modules' => [
        // other modules
        'mxdiModule',
    ],
    //
    //
];
```

This will enable the module and register the Abstract Factory with the Service Manager.

### Annotation mapping

For now following injections are available:

* constructor injection via `@InjectParams` annotation
* method injection via `@InjectParams` annotation
* property injection via `@Inject` annotation
   * set `invokable=true` to bypass service manager, useful with simple POPO's

DI for private/protected methods/properties is available altough not recommended to avoid costly reflection.

### Example class:

```php
<?php
namespace mxdiModuleTest\TestObjects;

use mxdiModule\Annotation as DI;

class Injectable
{
    /** @var DependencyA */
    private $dependencyA;

    /** @var DependencyB */
    private $dependencyB;

    /** @var DependencyC */
    private $dependencyC;

    /** @var DependencyD */
    private $dependencyD;

    /**
     * Property injection
     *
     * @var DependencyE
     * @DI\Inject("dependency_e")
     */
    private $dependencyE;


    /**
     * Constructor injection.
     *
     * @param DependencyA $dep1
     * @param DependencyB $dep2
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyA"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyB")
     * })
     */
    public function __construct(DependencyA $dependencyA, DependencyB $dependencyB)
    {
        $this->dependencyA = $dependencyA;
        $this->dependencyB = $dependencyB;
    }

    /**
     * Method injection.
     *
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyC"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyD", invokable=true)
     * })
     * @param DependencyC $dependencyC
     * @param DependencyD $dependencyD
     */
    private function setDependency(DependencyC $dependencyC, DependencyD $dependencyD)
    {
        $this->dependencyC = $dependencyC;
        $this->dependencyD = $dependencyD;
    }
}
```

The `@Inject` annotation requires valid service name, registered as ZF2 service in the Service Manager.

The requested service must not be registered in the Service Manager, because it must go through the Abstract Factory of
the Module. This allows you to create custom factory for the service by the way.

The order of the `@Inject` annotations inside the `@InjectParams` is important as with this order parameters will be
passed to the method. Wrong order will result in PHP's errors.

To speed up locate time you can request the service through the DiFactory invokable, for example:

```php
/** @var \mxdiModule\Service\DiFactory @factory */ 
$factory = $this->getServiceLocator()->get(\mxdiModule\Service\DiFactory::class);

/** @var \YourApplication\Service\SomeService $service */
$service = $factory(\YourApplication\Service\SomeService::class);
```

### Example Doctrine service

```php
<?php
namespace Application\Service;

use Doctrine\ORM\EntityManager;
use mxdiModule\Annotation as DI;

class UsersService
{
    /** @var EntityManager */
    protected $em;
    
    /**
     * @DI\InjectParams({
     *     @DI\Inject("Doctrine\ORM\EntityManager")
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * @return array
     */
    public function getUsers()
    {
        return $this->em->getRepository('Application\Entity\User')->findAll();
    }
}
```

### TODO

* Caching !!!
* Injecting ZF2's configuration params for example `@Inject("%doctrine.connection.orm_default%")`
* `Required` flag for not required dependencies
* Increase test coverage and code rating
