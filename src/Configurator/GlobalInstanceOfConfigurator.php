<?php

namespace GrinWay\Extension\Configurator;

use GrinWay\Extension\Config\GlobalInstanceOfDefaults;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition) {
	return $definition->rootNode()
		->children()
			->arrayNode(GlobalInstanceOfDefaults::PREFIX)
				->canBeDisabled()
				->addDefaultsIfNotSet()
				->children()
			
					->scalarNode(GlobalInstanceOfDefaults::REL_PATH_CONFIG_KEY)
						->info('The relative path to directory where file with _instanceof locates to assign tags globally')
						->defaultValue(GlobalInstanceOfDefaults::REL_PATH)
					->end()

					->scalarNode(GlobalInstanceOfDefaults::FILENAME_CONFIG_KEY)
						->info('The filename with _instanceof content.')
						->defaultValue(GlobalInstanceOfDefaults::FILENAME)
					->end()

				->end()
			->end()
		->end()
	;
};