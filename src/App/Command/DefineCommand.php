<?php

namespace App\Command;

use Ola\RabbitMqAdminToolkitBundle\Command\VhostDefineCommand;
use Ola\RabbitMqAdminToolkitBundle\DependencyInjection\OlaRabbitMqAdminToolkitExtension;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class DefineCommand extends VhostDefineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->addArgument('config-file', InputArgument::OPTIONAL, "Path to the config file to use");

        parent::configure();

        $this->setName('define');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($input);

        parent::execute($input, $output);
    }

    protected function loadConfiguration(InputInterface $input)
    {
        if (null === $input->getArgument('config-file')) {
            $configFile = '.rabbit-mq-admin-toolkit.yml';
        } else {
            $configFile = $input->getArgument('config-file');
        }

        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException(sprintf('Unable to find config file at path "%s"', $configFile));
        }

        $parser = new Parser();
        $config = $parser->parse(file_get_contents($configFile));

        $this
            ->getContainer()
            ->get('extension_loader')
            ->load(new OlaRabbitMqAdminToolkitExtension(), array($config));
    }
}
