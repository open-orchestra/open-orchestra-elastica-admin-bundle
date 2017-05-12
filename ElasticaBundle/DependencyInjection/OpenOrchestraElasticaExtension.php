<?php

namespace OpenOrchestra\ElasticaBundle\DependencyInjection;

use Elastica\Client;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraElasticaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $client = new Definition(Client::CLASS, array(array('host' => $config['host'], 'port' => $config['port'])));
        $container->setDefinition('open_orchestra_elastica.client.elastica', $client);

        $container->setParameter('open_orchestra_elastica.index.name', $config['index_name']);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array(
             'indexor',
             'transformer',
             'mapper',
             'schema_generator',
             'subscriber',
             'factory',
             'schema_initializer',
             'populator'
         ) as $file) {
            $loader->load($file . '.yml');
        }
    }
}
