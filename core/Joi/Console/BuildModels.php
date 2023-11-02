<?php

namespace Core\Joi\Console;

use Core\Joi\Build\ModelsBuilder;
use Core\Joi\Build\RoutesBuilder;
use Core\Joi\Start;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildModels extends Command
{
    protected Start $server;

    /**
     * @param $server
     */
    public function __construct(Start $server)
    {
        parent::__construct();

        $this->server = $server;
    }

    protected function configure()
    {
        $this->setName('build-models')
            ->setDescription('Builds the Cloud Valkyrie security model.')
            ->setHelp('Allows you to delete the application cache. Pass the --groups parameter to clear caches of specific groups.')
            ->addOption(
                'groups',
                'g',
                InputOption::VALUE_OPTIONAL,
                'Pass the comma separated group names if you don\'t want to clear all caches.',
                ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Config file is being read...');

        if ($input->getOption('groups')) {
            $groups = explode(",", $input->getOption('groups'));

            if (is_array($groups) && count($groups)) {
                foreach ($groups as $group) {
                    $output->writeln(sprintf('%s cache is cleared', $group));
                }
            }
        } else {
            $routesBuilder = new ModelsBuilder($output);
            $routesBuilder->setServer($this->server);
            $routesBuilder->build();

        }

        $output->writeln('Complete.');
    }
}