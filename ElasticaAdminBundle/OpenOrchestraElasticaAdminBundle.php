<?php

namespace OpenOrchestra\ElasticaAdminBundle;

use OpenOrchestra\ElasticaAdminBundle\DependencyInjection\Compiler\ElasticaPopulatorCompilerPass;
use OpenOrchestra\ElasticaAdminBundle\DependencyInjection\Compiler\SchemaInitializerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraElasticaAdminBundle
 */
class OpenOrchestraElasticaAdminBundle extends Bundle
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

