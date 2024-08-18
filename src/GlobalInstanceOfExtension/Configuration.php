<?php

namespace GrinWay\Extension\GlobalInstanceOfExtension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use GrinWay\Extension\AbstractGrinWayExtension;
use GrinWay\Extension\Config\GlobalInstanceOfDefaults;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(GrinWayGlobalInstanceOfExtension::PREFIX);

        $treeBuilder->getRootNode()
            ->info(''
                . 'You can copy this example: "'
                . \dirname(__DIR__)
                . DIRECTORY_SEPARATOR . 'config'
                . DIRECTORY_SEPARATOR . 'packages'
                . DIRECTORY_SEPARATOR . AbstractGrinWayExtension::PREFIX . '.yaml'
                . '"')
            ->children()

                ->scalarNode(GrinWayGlobalInstanceOfExtension::REL_PATH_KEY)
                    ->info('The relative path to directory where file with _instanceof locates to assign tags globally')
                    ->defaultValue(GlobalInstanceOfDefaults::REL_PATH)
                ->end()

                ->scalarNode(GrinWayGlobalInstanceOfExtension::FILENAME_KEY)
                    ->info('The filename with _instanceof content.')
                    ->defaultValue(GlobalInstanceOfDefaults::FILENAME)
                ->end()

            ->end()
        ;

        //$treeBuilder->setPathSeparator('/');

        return $treeBuilder;
    }

    //###> HELPERS ###
}
