# Configuring YAML driver

In the `mxdimodule.local.php` file, you can uncomment the `extractor` and `extractor_options` keys to change the default extractor from Annotations to Yaml as mapping source.

Make sure the `file` key under `extractor_options` points to a valid yml file with mapping information.

## Example YAML file

```yml
# Example mapping configuration for the Yaml extractor

# FQCN of the service class
App\Service\MyService:
  # Constructor injections
  constructor:
    - { name: mxdiModule\Annotation\Inject, value: Zend\EventManager\EventNamager, invokable: true }
    - { name: mxdiModule\Annotation\Inject, value: application}

  # Methods injections
  methods:

    # Method name and its parameters
    setDependency:
      - { name: mxdiModule\Annotation\Inject, value: request }
      - { name: mxdiModule\Annotation\Inject, value: application }

    setFactories:
      - { name: mxdiModule\Annotation\InjectConfig, value: service_manager.factories }

  # Properties injections
  properties:

    # Property name and its injection
    someProperty:
      name: mxdiModule\Annotation\InjectConfig
      value: service_manager.invokables

# Place here another mappings

```
