<?php

namespace GrinWay\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

abstract class AbstractGrinWayExtension extends ConfigurableExtension
{
    public const PREFIX = 'grin_way_extensions';

    protected function getParsedYaml(array $config, ContainerBuilder $container, string $relPathKey, string $filenameKey, bool $first = true, ?string $rootPath = null, bool $throw = false): array
    {
        $rootPath = $rootPath ?: $container->getParameter('kernel.project_dir');

        $pa = PropertyAccess::createPropertyAccessor();

        $relPath = $pa->getValue($config, '[' . $relPathKey . ']');
        $filename = $pa->getValue($config, '[' . $filenameKey . ']');

        ###> just in case
        if (null === $relPath || null === $filename) {
            if (true === $throw) {
                throw new \Exception('The relative path or filename were not got from the config.');
            } else {
                return [];
            }
        }

        $absPathToYamlInstanceOf = Path::makeAbsolute(
            path: $relPath,
            basePath: $rootPath,
        );

        $fileLocator = new FileLocator($absPathToYamlInstanceOf);
        $absPathToYamlInstanceOf = Path::normalize(
            $fileLocator->locate($filename, first: $first),
        );

        return Yaml::parseFile(
            $absPathToYamlInstanceOf,
            Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE
            | Yaml::PARSE_OBJECT
            | Yaml::PARSE_DATETIME
            | Yaml::PARSE_CONSTANT
            | Yaml::PARSE_CUSTOM_TAGS
        );
    }
}
