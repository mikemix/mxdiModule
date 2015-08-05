# Configuring XML driver

In the `mxdimodule.local.php` file, you can uncomment the `extractor` and `extractor_options` keys to change the default extractor to Xml as mapping source.

Make sure the `file` key under `extractor_options` points to a valid xml file with mapping information. Example configuration can look as follows:

```php
// config/autoload/mxdiModule.local.php file
// make sure config/services.xml is a valid xml file

    'extractor' => mxdiModule\Service\XmlExtractor::class,
    'extractor_options' => ['file' => __DIR__ . '/../services.xml'],
```

## Example XML file

```xml
<?xml version="1.0" encoding="UTF-8"?>
<mxdiModule xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/mikemix/mxdiModule/master/resources/schema/Service3.0.xml">

    <!-- Service's FQCN is mandatory only if it is different from its ID -->
    <service id="Application\Service\MyService" fqcn="Application\Service\MyService">
        <constructor>
            <inject type="mxdiModule\Annotation\Inject">
                <param name="value">Zend\EventManager\EventManager</param>
                <param name="invokable" type="boolean">true</param>
                <param name="count" type="integer">0</param>
            </inject>
            <inject type="mxdiModule\Annotation\InjectDoctrine" />
            <inject type="mxdiModule\Annotation\InjectLazy">
                <param name="value">request</param>
                <param name="fqcn">Zend\Http\Request</param>
            </inject>
        </constructor>
        <methods>
            <method name="setFactories">
                <inject type="mxdiModule\Annotation\InjectConfig">
                    <param name="value">service_manager.factories</param>
                </inject>
            </method>
            <method name="setApplication">
                <inject type="mxdiModule\Annotation\InjectDoctrine" />
                <inject type="mxdiModule\Annotation\Inject">
                    <param name="value">Zend\EventManager\EventManager</param>
                    <param name="invokable" type="boolean">true</param>
                    <param name="count" type="integer">0</param>
                </inject>
            </method>
        </methods>
        <properties>
            <property name="invokables">
                <inject type="mxdiModule\Annotation\InjectConfig">
                    <param name="value">service_manager.factories</param>
                </inject>
            </property>
            <property name="another">
                <inject type="mxdiModule\Annotation\Inject">
                    <param name="value">Zend\EventManager\EventManager</param>
                    <param name="invokable" type="boolean">true</param>
                    <param name="count" type="integer">0</param>
                </inject>
            </property>
        </properties>
    </service>

</mxdiModule>

```
