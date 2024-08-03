<?php

namespace GrinWay\Extension\GlobalInstanceOfExtension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use GrinWay\Extension\AbstractGrinWayExtension;

class Configuration implements ConfigurationInterface
{
    public function __construct(
        private $relPath,
        private $filename,
    ) {
    }

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
                    ->defaultValue($this->relPath)
                ->end()

                ->scalarNode(GrinWayGlobalInstanceOfExtension::FILENAME_KEY)
                    ->info('The filename with _instanceof content.')
                    ->defaultValue($this->filename)
                ->end()

            ->end()
        ;

        //$treeBuilder->setPathSeparator('/');

        return $treeBuilder;
    }

    //###> HELPERS ###
}
