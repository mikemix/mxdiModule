Configuring annotation driver
=======================

The `AnnotationExtractor` is set as default extractor and it's working out-of-the-box. Following annotations are available:

* [`@InjectParams`](#injectparams-annotation)
* [`@Inject`](#inject-annotation)
* [`@InjectConfig`](#injectconfig-annotation)
* [`@InjectLazy`](#injectlazy-annotation)
* [`@InjectDoctrine`](#injectdoctrine-annotation)
* [`@InjectDoctrineRepository`](#injectdoctrinerepository-annotation)
* [`@InjectIdentity`](#injectidentity-annotation)

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

$object->setDoctrine($proxyGenerator->create('Doctrine\ORM\EntityManager', function () use ($sm) {
    return $sm->get('Doctrine\ORM\EntityManager');
}));

$object->request = $proxyGenerator->create('Zend\Http\Request', function () use ($sm) {
    return $sm->get('request');
});
```

#### InjectDoctrine annotation

`@InjectDoctrine` annotation is allowed inside `@InjectParams` annotation and properties.
This annotation injects the Doctrine\ORM\EntityManager.

#### InjectDoctrineRepository annotation

`@InjectDoctrineRepository` annotation is allowed inside `@InjectParams` annotation and properties.
This annotation injects Doctrine's repository for given entity.

```php
/**
 * @var App\Entity\UserRepository
 * @InjectDoctrineRepository("App\Entity\User")
 */
public function setRepository(UserRepository $repository) ...

// which translates to

$object->setRepository($sm->get('Doctrine\ORM\EntityManager')->getRepository('App\Entity\User'));

```

#### InjectIdentity annotation
This annotation injects ZF2's identity. This is simply an alias for `$serviceManager->get('Zend\Authentication\AuthenticationService')->getIdentity()`.

## Custom annotations

You can write custom `@Inject(type)` annotations in couple easy steps:

1. Write your custom annotation class which implements the `mxdiModule\Annotation\AnnotationInterface` interface
2. Register it in the YourModule::init() method: `\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/src/Module/Annotation/YourAnnotation.php')`
3. Voila!

# Complete example class

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
