<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/16/2019
 * Time: 00:51
 */

namespace Core\Joi\Console;


use Core\Joi\Build\ConBuilder;
use Core\Joi\Start;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
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
        $this->setName('build-config')
            ->setDescription('Builds the application Configuration files.')
            ->setHelp('This command compiles the config/config.io file.')
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
            $dir = str_replace('\core\joi\Console', '', __DIR__);
            $test = ConBuilder::readENV($dir, '/config/config.io');
            $output->writeln($test);
        }

        $output->writeln('Complete.');
    }
}