# mxdiModule
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

* constructor injection via @InjectParams annotation
* method injection via @InjectParams annotation
* property injection via @Inject annotation

Example class:

```php
<?php
namespace mxdiModuleTest\TestObjects;

use mxdiModule\Annotation as DI;

class Injectable
{
    /** @var DependencyA */
    public $dependencyA;

    /** @var DependencyB */
    public $dependencyB;

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
    public function __construct(DependencyA $dep1, DependencyB $dep2)
    {
        $this->dependencyA = $dep1;
        $this->dependencyB = $dep2;
    }

    /**
     * Method injection.
     *
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyC"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyD")
     * })
     * @param DependencyC $dependencyC
     * @param DependencyD $dependencyD
     */
    private function setDependency(DependencyC $dependencyC, DependencyD $dependencyD)
    {
        $this->dependencyC = $dependencyC;
        $this->dependencyD = $dependencyD;
    }

    /**
     * @return DependencyA
     */
    public function getDependencyA()
    {
        return $this->dependencyA;
    }

    /**
     * @return DependencyB
     */
    public function getDependencyB()
    {
        return $this->dependencyB;
    }

    /**
     * @return DependencyC
     */
    public function getDependencyC()
    {
        return $this->dependencyC;
    }

    /**
     * @return DependencyD
     */
    public function getDependencyD()
    {
        return $this->dependencyD;
    }

    /**
     * @return DependencyE
     */
    public function getDependencyE()
    {
        return $this->dependencyE;
    }
}
```

The `@Inject` annotation requires valid service name, registered as ZF2 service in the Service Manager.
The service must not be registered in the Service Manager though, because it must go through the Abstract Factory of
the Module. This allows you to create custom factory for the service.
