# mxdiModule
[![Build Status](https://travis-ci.org/mikemix/mxdiModule.svg?branch=master)](https://travis-ci.org/mikemix/mxdiModule) [![Build Status](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/5582bff8363861001500025b/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5582bff8363861001500025b) [![Latest Stable Version](https://poser.pugx.org/mikemix/mxdi-module/v/stable)](https://packagist.org/packages/mikemix/mxdi-module) [![Total Downloads](https://poser.pugx.org/mikemix/mxdi-module/downloads)](https://packagist.org/packages/mikemix/mxdi-module) [![License](https://poser.pugx.org/mikemix/mxdi-module/license)](https://packagist.org/packages/mikemix/mxdi-module)

Configure dependency injection in Zend Framework 2 using annotations.

Idea based on the [JMSDiExtraBundle](https://github.com/schmittjoh/JMSDiExtraBundle) for the Symfony2 project.


1. [Installation](#installation)
2. [Annotation reference](#annotation-reference)
3. [Complete example class](#complete-example-class)
4. [Important notes](#important-notes)
5. [Caching](#caching)
6. [Debugging](#debugging)
7. [Console commands](#console-commands)
8. [Custom annotations](#custom-annotations)


### Installation

Install with Composer.

```js
    "require": {
        "mikemix/mxdi-module": "~1.1"
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

### Annotation reference

* [`@InjectParams`](#injectparams-annotation)
* [`@Inject`](#inject-annotation)
* [`@InjectConfig`](#injectconfig-annotation)
* [`@InjectLazy`](#injectlazy-annotation)
* [`@InjectDoctrine`](#injectdoctrine-annotation)

#### InjectParams Annotation

`@InjectParams` annotation is allowed in constructor and methods. It's an array of other `@Inject` annotations.
The order of the `@Inject(type)` annotations inside the `@InjectParams` *is important* as with this order parameters will be
passed to the method/constructor. Wrong order will result in PHP's errors.

```php
/**
 * @InjectParams({
 *     @Inject("request"),
 *     @InjectConfig("service_manager.factories")
 * })
 */
public function setRequest(\Zend\Http\Request $request, array $factories) ...

// which translates to

public function setRequest(
    $serviceLocator->get('request'),
    $serviceLocator->get('config')['service_manager']['factories']
) ...
```

#### Inject Annotation

`@Inject` annotation is allowed inside `@InjectParams` annotation and properties.
This annotation injects ZF2's service from the Service Manager. Annotation requires the name of the service. Optional
boolean argument `invokable=true|false` tells the injector that requested service is simple POPO and will not fetch it
from the Serivce Manager, but simply instantiate it directly. 

```php
/**
 * @Inject("Zend\EventManager\EventManager", invokable=true)
 */
public $evm;

/**
 * @Inject("Application\Service\UserService")
 */
protected $doctrine;

// which translates to

$object->evm = new \Zend\EventManager\EventManager();
$object->doctrine = $serviceLocator->get('Application\Service\UserService');
```

#### InjectConfig Annotation

`@InjectConfig` annotation is allowed inside `@InjectParams` annotation and properties.
This annotation injects config values from ZF2's config. Annotation requires the name of the key in dotted notation.
If the requested key consists of dots, dots in the key name must be escaped with backslash.

```php
/**
 * @InjectConfig("mymodule.config.some\.dotted\.key")
 */
protected $mySettings;

// which translates to

$object->mySettings = $serviceLocator->get('config')['mymodule']['config']['some.dotted.key'];
```

#### InjectLazy Annotation

`@InjectLazy` annotation is allowed inside `@InjectParams` annotation and properties.
This annotation injects ZF2's service from the Service Manager, but it does not instantiate those objects directly.
Instead it returns a proxy. You can read more about [Lazy Services in the ZF2 docs](http://framework.zend.com/manual/current/en/modules/zend.service-manager.lazy-services.html).

Annotation requires the name of the service and its FQCN (fqcn parameter) if the service name is different from it. The class you are
trying to request must not be marked as *final*.

Generated proxy files are stored on disk for performance reasons. Make sure to copy dist config file mentioned
in the [Caching](#caching) section and set the `proxy_dir` accordingly. Default directory is `data/mxdiModule`.
Make sure this directory exists and is writable by the webserver.

You can clear generated proxies by executing console command: `php public/index.php mxdimodule proxy clear`

```php
/**
 * @InjectLazy("Doctrine\ORM\EntityManager")
 */
public function setDoctrine(EntityManager $em) ...

/**
 * @var Request
 * @InjectLazy("request", fqcn="Zend\Http\Request")
 */
protected $request;

// which translates to

$object->setDoctrine($proxyGenerator->create('Doctrine\ORM\EntityManager'));
$object->request = $proxyGenerator->create('Zend\Http\Request', function () use ($serviceLocator, $name = 'request') {
    return $serviceLocator->get($name);
});
```

#### InjectDoctrine annotation

`@InjectDoctrine` annotation is allowed inside `@InjectParams` annotation and properties.
This annotation injects the Doctrine\ORM\EntityManager.

### Complete example class

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

### Important notes

*Remember*, the service you are about to inject, like the `Injectable` class above, must not be registered in the Service Manager.
If you register it as factory or invokable, it won't go through the Abstract Factory and won't get injected. By the way, this allows you to create custom factory for the service in mention.

To speed up locate time you can request the service through the DiFactory invokable, for example:

```php
/** @var \mxdiModule\Service\DiFactory @factory */ 
$factory = $this->getServiceLocator()->get(\mxdiModule\Service\DiFactory::class);

/** @var \YourApplication\Service\SomeService $service */
$service = $factory(\YourApplication\Service\SomeService::class);
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

### Console commands

* Clear generated proxy files: `php public/index.php mxdimodule proxy clear`

### Custom annotations

You can write custom `@Inject(type)` annotations in couple easy steps:

1. Write your custom annotation class which implements the `mxdiModule\Annotation\Annotation` interface
2. Register it in the YourModule::init() method: `\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/src/Module/Annotation/YourAnnotation.php')`
3. Voila!
