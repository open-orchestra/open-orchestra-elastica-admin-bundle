<?php

namespace OpenOrchestra\ElasticaAdminBundle\Command;

use Elastica\Exception\ResponseException;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraDeleteIndexCommand
 */
class OrchestraDeleteIndexCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:elastica:index:drop')
            ->setDescription('Drop the index in elasticsearch');
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
        $indexName = $this->getContainer()->getParameter('open_orchestra_elastica.index.name');;
        $index = $this->getContainer()->get('open_orchestra_elastica.client.elastica')->getIndex($indexName);
        try {
            $index->delete();
            $message = 'Drop Elastica Index';
            $output->writeln(sprintf('<comment>></comment> <info>%s</info>', $message));
        } catch (ResponseException $e) {
            $message = 'No Elastica Index to drop';
            $output->writeln(sprintf('<comment>></comment> <info>%s</info>', $message));
        }
    }
}
