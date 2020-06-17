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
        $this->setName('build-cv-model')
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
            ini_set('memory_limit', '8G');
            ini_set('max_execution_time','9600');
            $arr_text = [];
            $arr_label = [];
            //bad guys
            $dir = str_replace('\sys\joi\Console','',__DIR__);
            $handle = fopen($dir.'/datasets/badqueries.txt', 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $line = fgets($handle);
                    $arr_text[] = $line;
                    $arr_label[] = 'bad';
                }
            }
            $baddatasets = count($arr_text);
            $output->writeln('done loading Bad datasets of '. $baddatasets);

            $handle = fopen($dir.'/datasets/goodqueries.txt', 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $line = fgets($handle);
                    $arr_text[] = $line;
                    $arr_label[] = 'good';
                }
            }
            $output->writeln('done loading Good datasets of '. (count($arr_text)-$baddatasets));

            $tokenize = new WordTokenizer();
            $vectorizer = new TokenCountVectorizer($tokenize);

            $vectorizer->fit($arr_text);
            $output->writeln('done Fitting the datasets to word tokenizer');
            $vocabulary = $vectorizer->getVocabulary();
            $arr_transform = $arr_text;
            $vectorizer->transform($arr_transform);

            $transformer = new TfIdfTransformer($arr_transform);
            $output->writeln('TfIdfTransformer been loaded');
            $transformer->transform($arr_transform);
            $classifier = new LogisticRegression();
            $output->writeln('Traning about to start');
            $classifier->train($arr_transform, $arr_label);

            $filepath = 'C:\wamp64\htdocs\cloud\datasets\cv_query_ml.model';
            $modelManager = new ModelManager();
            $modelManager->saveToFile($classifier, $filepath);
            $output->writeln('Traning done.');

            $arr_testset = [
                '/scripts/edit_image.php?dn',
                '/in/',
                '/joi?api=js'
            ];


            $vectorizer->transform($arr_testset);
            $transformer->transform($arr_testset);
            $result = $classifier->predict($arr_testset);


            $output->writeln(\Nette\Utils\Json::encode($result));
        }

        $output->writeln('Complete.');

    }

}