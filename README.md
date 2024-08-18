# grinway/symfony-extensions

## Description

These extensions will help you with your Symfony applicaiton:
| Extension | Description |
| ------------- | ------------- |
| [GrinWayGlobalInstanceOfExtension](https://github.com/GrinWay/symfony-extensions/blob/main/src/GlobalInstanceOfExtension/GrinWayGlobalInstanceOfExtension.php) | Global setting of `_instanceof.yaml` |

## Installation

### Step 1: Require the bundle

In your `%kernel.project_dir%/composer.json`

```json
"require": {
    "grinway/symfony-extensions": "VERSION"
},
"repositories": [
    {
        "type": "path",
        "url": "./bundles/grinway/symfony-extensions"
    }
]
```

### Step 2: Download the bundle

### [Before git clone](https://github.com/GrinWay/docs/blob/main/docs/bundles_grin_symfony%20mkdir.md)

```console
git clone "https://github.com/GrinWay/symfony-extensions.git"
```

```console
cd "../../"
```

```console
composer require "grinway/symfony-extensions"
```

### [Binds](https://github.com/GrinWay/docs/blob/main/docs/borrow-services.yaml-section.md)

## Usage

#### GrinWayGlobalInstanceOfExtension

Create `_instanceof.yaml` file (default by the path: `%kernel.project_dir%/config/_instanceof.yaml`)
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
# Got it by executing: php bin/console config:dump-reference GrinWayExtensionsBundle

grin_way_extensions:
    global_instance_of:
        enabled:              true

        # The relative path to directory where file with _instanceof locates to assign tags globally
        rel_path:             config

        # The filename with _instanceof content.
        filename:             _instanceof.yaml
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