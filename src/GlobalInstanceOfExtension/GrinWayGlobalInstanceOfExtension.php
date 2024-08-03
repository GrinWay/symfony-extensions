<?php

namespace GrinWay\Extension\GlobalInstanceOfExtension;

use GrinWay\Extension\AbstractGrinWayExtension;
use GrinWay\Service\GrinWayServiceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
* Assigns tags to the classes that implement interface
* described in "%kernel.project_dir%/ <relPath> / <filename>"
* only do this for all the project
*
* _instanceof.yaml has the same syntax as _instanceof of services.yaml
*
* # if one element that's a TAG_NAME
* INTERFACE1:
*    tags:
*    -  TAG_NAME1
*    -  TAG_NAME2
*
* # TAG_NAMES named as "name"
* INTERFACE2:
*    tags:
*    -  name: TAG_NAME1
*    -  name: TAG_NAME2
*
* # TAG_NAMES named as "name"
* INTERFACE3:
*    tags:
*    -  name: TAG_NAME1
*       dop_attr: NAME
*    -  name: TAG_NAME2
*
* # TAG_NAMES as keys
* INTERFACE4:
*    tags:
*       TAG_NAME1:
*           dop_attr: NAME
*       TAG_NAME2:
*           dop_attr: NAME
*/
class GrinWayGlobalInstanceOfExtension extends AbstractGrinWayExtension
{
    public const PREFIX = 'grin_way_global_instance_of';
    public const REL_PATH_KEY = 'rel_path';
    public const FILENAME_KEY = 'filename';

    public function getAlias(): string
    {
        return self::PREFIX;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration(
            relPath: 'config',
            filename: '_instanceof.yaml',
        );
        $config = $this->processConfiguration($configuration, $configs);

        $pa = PropertyAccess::createPropertyAccessor();

        $interfaces = $this->getParsedYaml(
            config: $config,
            container: $container,
            relPathKey: self::REL_PATH_KEY,
            filenameKey: self::FILENAME_KEY,
        );

        if (!\is_array($interfaces)) {
            return;
        }
        foreach ($interfaces as $interface => $instanceAndTheirTags) {
            $tagsAndAttributes = $pa->getValue($instanceAndTheirTags, '[tags]');

            if (null === $interface) {
                continue;
            }

            foreach ($tagsAndAttributes as $tagKey => $tagAttributes) {
                if (\is_int($tagKey)) {
                    if (\is_string($tagAttributes)) {
                        $tagName = $tagAttributes;
                        $tagAttributes = [];
                    } else {
                        $tagName = $pa->getValue($tagAttributes, '[name]');
                        unset($tagAttributes['name']);
                    }
                } else {
                    $tagName = $tagKey;
                }

                if (!\is_string($tagName)) {
                    $message = \sprintf(
                        'Name of the tag must me string and must be: "key" or [name] of assotiative element or one element in tag array',
                    );
                    throw new \Exception($message);
                }
                if (empty($tagAttributes)) {
                    $tagAttributes = [];
                }

                $container->registerForAutoconfiguration($interface)
                    ->addTag($tagName, $tagAttributes)
                    ->setAutoconfigured(true)
                ;
            }
        }
    }
}
