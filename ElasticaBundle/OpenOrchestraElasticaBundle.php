<?php

namespace OpenOrchestra\ElasticaBundle;

use OpenOrchestra\ElasticaBundle\DependencyInjection\Compiler\ElasticaPopulatorCompilerPass;
use OpenOrchestra\ElasticaBundle\DependencyInjection\Compiler\SchemaInitializerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraElasticaBundle
 */
class OpenOrchestraElasticaBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SchemaInitializerCompilerPass());
        $container->addCompilerPass(new ElasticaPopulatorCompilerPass());
    }
}
