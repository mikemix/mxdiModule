# mxdiModule
[![Build Status](https://travis-ci.org/mikemix/mxdiModule.svg?branch=master)](https://travis-ci.org/mikemix/mxdiModule) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Total Downloads](https://poser.pugx.org/mikemix/mxdi-module/downloads)](https://packagist.org/packages/mikemix/mxdi-module) [![License](https://poser.pugx.org/mikemix/mxdi-module/license)](https://packagist.org/packages/mikemix/mxdi-module)

Configure dependency injection in Zend Framework 2 using annotations.

Idea based on the [JMSDiExtraBundle](https://github.com/schmittjoh/JMSDiExtraBundle) for the Symfony2 project.

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

This will enable the module and register the Abstract Factory in the ZF2's Service Manager.

### Annotation mapping

For now following injections are available:

* constructor injection via `@InjectParams` annotation
* method injection via `@InjectParams` annotation
* property injection via `@Inject` annotation
   * example usage: `@Inject("service_name")` where service_name is registered in the ZF2's Service Manager
   * set `invokable=true` to bypass service manager, useful with simple POPO's
* ZF2 configuration injection via `@InjectConfig` annotation
* Lazy object injection via `@InjectLazy` annotation
   * set `fqcn="service\fqcn"` if its name in the Service Manager is different from its FQCN. For example, to lazily inject the ZF2's request object: `@InjectLazy("request", "Zend\Http\Request")`. If your service is for example registered as `Application\Service\SomeService` then simple `@InjectLazy("Application\Service\SomeService")` will do.

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
     * ZF2 configuration injection
     *
     * @var string
     * @DI\InjectConfig("doctrine.connection.orm_default.params")
     */
    private $doctrineConnectionSettings = [];

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

*Remember*, the service you are about to inject, like the `Injectable` class above, must not be registered in the Service Manager.
If you register it as factory or invokable, it won't go through the Abstract Factory and won't get injected. By the way, this allows you to create custom factory for the service in mention.

The order of the `@Inject` annotations inside the `@InjectParams` *is important* as with this order parameters will be
passed to the method/constructor. Wrong order will result in PHP's errors.

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

class UserService
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

### Caching

Annotation parsing is very heavy. You should enable the cache on production servers.

You can set up caching easily with any custom or pre-existing ZF2 cache adapter. Copy the dist configuration file
to your `config/autoload` directory, for example:

`cp vendor/mikemix/mxdi-module/config/mxdimodule.local.php.dist config/autoload/mxdimodule.local.php`

and override the `cache_adapter` and `cache_options` keys for your needs. You can find more information about
available out-of-the-box adapters at the [ZF2 docs site](http://framework.zend.com/manual/current/en/modules/zend.cache.storage.adapter.html).

### Debugging

If you get *ServiceNotCreated* exception most probably one of your injections is not registered in the ZF2's Service
 Manager. In the exception stack you will see some more detailed information. For instance look for *CannotGetValue*
 exceptions.
