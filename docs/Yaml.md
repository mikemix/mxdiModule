# Configuring YAML driver

In the `mxdimodule.local.php` file, you can uncomment the `extractor` and `extractor_options` keys to change the default extractor to Yaml as mapping source.

Make sure the `file` key under `extractor_options` points to a valid yml file with mapping information. Example configuration can look as follows:

```php
// config/autoload/mxdiModule.local.php file
// make sure config/services.yml is a valid yaml file

    'extractor' => mxdiModule\Service\YamlExtractor::class,
    'extractor_options' => ['file' => __DIR__ . '/../services.yml'],
```

## Example YAML file

```yml
# ID of the service class
App\Service\MyService:

  # FQCN if it is different from its ID
  fqcn: App\Service\MyService

  # Constructor injections
  constructor:
    - { type: mxdiModule\Annotation\Inject, value: Zend\EventManager\EventNamager, invokable: true }
    - { type: mxdiModule\Annotation\Inject, value: application}

  # Methods injections
  methods:

    # Method name and its parameters
    setDependency:
      - { type: mxdiModule\Annotation\Inject, value: request }
      - { type: mxdiModule\Annotation\Inject, value: application }

    setFactories:
      - { type: mxdiModule\Annotation\InjectConfig, value: service_manager.factories }

  # Properties injections
  properties:

    # Property name and its injection
    someProperty:
      type: mxdiModule\Annotation\InjectConfig
      value: service_manager.invokables

# Another service with ID different from its FQCN
request:
  fqcn: Zend\Http\Request

# Place here another mappings

```
