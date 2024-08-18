<?php

namespace GrinWay\Extension\Contract;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

interface GrinWayExtensionInterface
{
    public function load(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void;

    public static function getExtensionRootConfigNode(): string;
}
