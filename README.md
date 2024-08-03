# grinway/symfony-extensions

## Description

These extensions will help you with your Symfony applicaiton:
| Extension | Description |
| ------------- | ------------- |
| [GrinWayGlobalInstanceOfExtension](https://github.com/GrinWay/symfony-extensions/blob/main/src/GlobalInstanceOfExtension/GrinWayGlobalInstanceOfExtension.php) | Global setting of `_instanceof.yaml` |

## Usage

#### GrinWayGlobalInstanceOfExtension

In your `%kernel.project_dir%/src/Kernel.php`

```php

use GrinWay\Extension\GlobalInstanceOfExtension\GrinWayGlobalInstanceOfExtension;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;
    
    protected function build(ContainerBuilder $container): void
    {
        ###> DO THIS ###
        $container->registerExtension(new GrinWayGlobalInstanceOfExtension());
        ###< DO THIS ###
    }
}
```

Then create `_instanceof.yaml` file (default by the path: `%kernel.project_dir%/config/_instanceof.yaml`)
and write down there something usual, like this:

```yaml
###> MESSENGER ###
App\Contract\Messenger\CommandBusHandlerInterface:
    tags:
    -   name:   'messenger.message_handler'
        bus:    'command.bus'
    -   name:   'app.command_bus_handler'

App\Contract\Messenger\EventBusHandlerInterface:
    tags:
    -   name:   'messenger.message_handler'
        bus:    'event.bus'
    -   name:   'app.event_bus_handler'
###< MESSENGER ###

###> EVENT LISTENER ###
App\Contract\EventListener\KernelBeforeLocaleEventListenerInterface:
    tags:
    -   name: kernel.event_listener
        # to allow to change to locale of the Request
        priority: 127
###< EVENT LISTENER ###
```

If you don't like the path `%kernel.project_dir%/config/_instanceof.yaml`
you can customize it in the following way:

1) create by the path `%kernel.project_dir%/config/packages/` (exactly by this path) the file `grin_way_extensions.yaml` (actually any filename)
2) override the default values:
```yaml
grin_way_global_instance_of:
    rel_path: config
    filename: _instanceof.yaml
```

There're at least two advantages when you use a separate `_instanceof.yaml` file:
1) You got rid of an unnecessary content in your `services.yaml`
2) If you use imports in `services.yaml` it allow you not to write _instanceof structure in every of those files, cuz you already have this structure applied globally!

```yaml
# `%kernel.project_dir%/config/services.yaml`

imports:
    -   resource: 'services_yaml/services'
    -   resource: 'services_yaml/parameters'
```