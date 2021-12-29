<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jmv_develop_media');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('entity_manager_service')->defaultValue('doctrine.orm.default_entity_manager')->end()
            ->scalarNode('media_url_generator_service')->isRequired()->end()
            ->scalarNode('filesystem_service')->isRequired()->end()
            ->booleanNode('with_resize_filter')->defaultTrue()->end()
            ->scalarNode('namer_default_id')->defaultValue('default')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
