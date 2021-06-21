<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 28 /Oct, 2020 @ 18:45
 */

namespace Core\joi\Console;


use Core\joi\ConBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildControllers extends Command
{

    protected function configure()
    {
        $this->setName('build-controllers')
            ->setDescription('Builds the application Controllers.')
            ->setHelp('This command compiles the config/config.io file.')
            ->addOption(
                'folder',
                'f',
                InputOption::VALUE_OPTIONAL,
                'The folder containing controller Classes',
                ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Build controllers starting');

        if ($input->getOption('folder')) {


            $output->writeln(sprintf('%s is marked as the target controller directory', $input->getOption('groups')));
            $dir = str_replace('\core\joi\Console','',__DIR__);
            $test = ConBuilder::buildControllers($output,$dir,'/'.$input->getOption('groups'));
            $output->writeln($test);
        }else
        {
            $dir = str_replace('\core\joi\Console','',__DIR__);
            $test = ConBuilder::buildControllers($output,$dir);
            $output->writeln($test);
        }

        $output->writeln('Complete.');
    }

}