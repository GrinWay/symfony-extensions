<?php

namespace GrinWay\Extension\Extension;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use GrinWay\Extension\AbstractGrinWayExtension;
use GrinWay\Service\GrinWayServiceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use GrinWay\Extension\Config\GlobalInstanceOfDefaults;
use GrinWay\Extension\Util\YamlUtil;
use GrinWay\Extension\Contract\GrinWayExtensionInterface;

/**
* Assigns tags to the services by interfaces
* described in "%kernel.project_dir%/ <rel_path> / <filename>"
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
class GrinWayGlobalInstanceOfExtension implements GrinWayExtensionInterface
{
    public static function getExtensionRootConfigNode(): string
    {
        return GlobalInstanceOfDefaults::PREFIX;
    }

    public function load(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $pa = PropertyAccess::createPropertyAccessor();

        $interfaces = YamlUtil::getParsedYaml(
            config: $config,
            container: $builder,
            relPathKey: GlobalInstanceOfDefaults::REL_PATH_CONFIG_KEY,
            filenameKey: GlobalInstanceOfDefaults::FILENAME_CONFIG_KEY,
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

                $builder->registerForAutoconfiguration($interface)
                    ->addTag($tagName, $tagAttributes)
                    ->setAutoconfigured(true)
                ;
            }
        }
    }
}
