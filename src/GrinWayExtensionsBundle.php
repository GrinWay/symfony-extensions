<?php

namespace GrinWay\Extension;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\DependencyInjection\Definition;
use GrinWay\Extension\Extension\GrinWayGlobalInstanceOfExtension;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use GrinWay\Extension\Contract\GrinWayExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GrinWayExtensionsBundle extends AbstractBundle
{
    public const TAG = 'grin_way.extension';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->info(''
                . 'You can copy this example: "'
                . \dirname(__DIR__)
                . DIRECTORY_SEPARATOR . 'config'
                . DIRECTORY_SEPARATOR . 'packages'
                . DIRECTORY_SEPARATOR . $this->getContainerExtension()->getAlias() . '.yaml'
                . '"');

        $definition->import('./Configurator/*.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $pa = PropertyAccess::createPropertyAccessor();

        $container->import('../config/services.yaml');

        $ids = $builder->findTaggedServiceIds(self::TAG);

        foreach ($ids as $id => $attributes) {
            $class = $builder->findDefinition($id)->getClass();
            $extension = $builder->get($id);

            $extensionConfig = $pa->getValue(
                $config,
                \sprintf('[%s]', $class::getExtensionRootConfigNode()),
            );
            if (null === $extensionConfig) {
                throw new \LogicException('Looks like you forgotten to provide the whole Extension configuration.');
            }

            $enabled = $pa->getValue($extensionConfig, '[enabled]');
            if (null === $enabled) {
                throw new \LogicException('Looks like you forgotten to provide the "enabled" option of Extension.');
            }
            if (true === $enabled) {
                $extension->load(
                    $extensionConfig,
                    $container,
                    $builder,
                );
            }
            $builder->removeDefinition($id);
        }
    }

    //###> HELPER ###
}
