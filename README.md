# mxdiModule
[![Build Status](https://travis-ci.org/mikemix/mxdiModule.svg?branch=master)](https://travis-ci.org/mikemix/mxdiModule) [![Build Status](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/mikemix/mxdiModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mikemix/mxdiModule/?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/5582bff8363861001500025b/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5582bff8363861001500025b) [![Latest Stable Version](https://poser.pugx.org/mikemix/mxdi-module/v/stable)](https://packagist.org/packages/mikemix/mxdi-module) [![Total Downloads](https://poser.pugx.org/mikemix/mxdi-module/downloads)](https://packagist.org/packages/mikemix/mxdi-module) [![License](https://poser.pugx.org/mikemix/mxdi-module/license)](https://packagist.org/packages/mikemix/mxdi-module)

Configure dependency injection in Zend Framework 2 using annotations/yaml etc.

Idea based on the [JMSDiExtraBundle](https://github.com/schmittjoh/JMSDiExtraBundle) for the Symfony2 project.


1. [Installation](#installation)
2. [Changing mapping driver](#changing-mapping-driver)
2. [Important notes](#important-notes)
3. [Caching](#caching)
4. [Debugging](#debugging)
5. [Console commands](#console-commands)
6. [Working with Plugin Managers](#working-with-plugin-managers)

### Installation

1. Install with Composer: `composer require mikemix/mxdi-module:~2.2`

2. Enable via ZF2 config in `appliation.config.php` under `modules` key:

```php
return [
    //
    //
    'modules' => [
        'mxdiModule',
        // other modules
    ],
    //
    //
];
```

This will enable the module and register the Abstract Factory in the ZF2's Service Manager.

### Changing mapping driver

The default mapping driver is `AnnotationExtractor` as source of mapping information for the module. You can change it however to other. Available extractors are:

* `AnnotationExtractor` which uses annotations inside your classes. See the [Annotation docs](mxdiModule/docs/Annotations.md) for annotations reference and examples.
* `YamlExtractor` which uses a yml file. See the [Yaml](mxdiModule/docs/Yaml.md) docs for examples.

There's *no difference* between choosing annotation driver or YAML driver, because the mapping information in the end is converted to *plain php* and stored *inside the cache*.

### Important notes

*Remember*, the service you are about to inject must not be registered in the Service Manager.
If you register it as factory or invokable, it won't go through the Abstract Factory and won't get injected. By the way, this allows you to create custom factory for the service in mention.

To speed up locate time you can request the service through the DiFactory invokable, for example:

```php
/** @var \mxdiModule\Service\DiFactory @factory */ 
$factory = $this->getServiceLocator()->get(\mxdiModule\Service\DiFactory::class);

/** @var \YourApplication\Service\SomeService $service */
$service = $factory(\YourApplication\Service\SomeService::class);
```

### Caching

Parsing mapping sources is very heavy. You *should* enable the cache on production servers.

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
* Clear annotation parsing cache: `php public/index.php mxdimodule cache clear`

### Working with Plugin Managers

By default the Abstract Factory is not registered for abstract plugin managers like `FormElementManager` (form management),
`PluginManager` (controller management) etc. for performance reasons. You can register the Abstract Factory for yourself if you want to.

For example, to enable injections in controllers:

```php
// example config file module/Application/config/module.config.php

return [
    // other keys
    //
    'controllers' => [
        'abstract_factories' => [
            // annotations now enabled
            mxdiModule\Factory\DiAbstractPluginFactory::class,
        ],
        'invokables' => [
            // your controllers
        ],
    ],
    //
    //
];
