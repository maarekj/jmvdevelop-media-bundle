<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class JmvDevelopMediaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $container->setAlias('jmv_develop_media.filesystem', (string) $config['filesystem_service']);
        $container->setAlias('jmv_develop_media.entity_manager', (string) $config['entity_manager_service']);
        $container->setAlias('jmv_develop_media.media_url_generator', (string) $config['media_url_generator_service']);

        $withResizeFilter = (bool) $config['with_resize_filter'];
        if (true === $withResizeFilter) {
            $loader->load('resize_filter.yaml');
        }
    }
}
