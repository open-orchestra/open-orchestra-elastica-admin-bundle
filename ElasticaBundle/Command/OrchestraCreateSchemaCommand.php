<?php

namespace OpenOrchestra\ElasticaBundle\Command;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraCreateSchemaCommand
 */
class OrchestraCreateSchemaCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:elastica:schema:create')
            ->setDescription('Load the schema from the content types');
    }

    /**
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('open_orchestra_elastica.schema_initializer.manager')->initialize();
    }
}
