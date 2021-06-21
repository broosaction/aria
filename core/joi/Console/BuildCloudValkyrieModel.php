<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 04 /May, 2020 @ 20:28
 */

namespace Core\joi\Console;


use Core\joi\Build\BuildRoutes;
use Core\joi\ConBuilder;
use Phpml\Classification\Linear\LogisticRegression;
use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\ModelManager;
use Phpml\Tokenization\WordTokenizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCloudValkyrieModel extends Command
{
    protected function configure()
    {
        $this->setName('build-routes')
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

        if ($input->getOption('groups'))
        {
            $groups = explode(",", $input->getOption('groups'));

            if (is_array($groups) && count($groups))
            {
                foreach ($groups as $group)
                {
                    $output->writeln(sprintf('%s cache is cleared', $group));
                }
            }
        }
        else
        {
            (new BuildRoutes($output))->build();

        }

        $output->writeln('Complete.');
    }


}