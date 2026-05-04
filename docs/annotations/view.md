# View, Components, Helpers

## View

The `AppView` class should annotate the helpers of the plugins and the app.

```bash
bin/cake annotate view
```

With template content like:

```php
<?php echo $this->My->foo($bar); ?>
<?php if ($this->Configure->baz()) {} ?>
```

the following would be annotated (if the `My` and `Shim.Configure` helpers are
loaded correctly):

```php
/**
 * @property \App\View\Helper\MyHelper $My
 * @property \Shim\View\Helper\ConfigureHelper $Configure
 */
class AppView extends View {
}
```

### Include plugins

Using the Configure key `'IdeHelper.includedPlugins'` you can set an array of
(loaded!) plugins to include. Those will then also be parsed and all found
helpers added to the `AppView` annotations. Setting this to `true` will
auto-include all loaded plugins.

## Components

Components should annotate any component they use.

```bash
bin/cake annotate components
```

A component containing:

```php
    /**
     * @var array
     */
    protected $components = [
        'RequestHandler',
        'Flash.Flash',
    ];
```

would get the following annotations:

```php
/**
 * @property \App\Controller\Component\CheckHttpCacheComponent $CheckHttpCache
 * @property \Flash\Controller\Component\FlashComponent $Flash
 */
```

## Helpers

Helpers should annotate any helper they use.

```bash
bin/cake annotate helpers
```

A helper containing:

```php
    /**
     * @var array
     */
    protected $helpers = [
        'Form',
    ];

    /**
     * @param \Cake\View\View $View
     * @param array $config
     */
    public function __construct(View $View, array $config = []) {
        parent::__construct($View, $config);
        $this->_View->loadHelper('Template');
    }
```

would get the following annotations:

```php
/**
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \App\View\Helper\TemplateHelper $Template
 */
```
