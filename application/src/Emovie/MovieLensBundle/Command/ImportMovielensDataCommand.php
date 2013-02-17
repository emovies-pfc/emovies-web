<?php
namespace Emovie\MovieLensBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Emovie\UserBundle\Entity\User;
use Doctrine\ORM\Query;
use Emovie\MovieLensBundle\File\MovieLensFile;
use Emovie\MovieLensBundle\File\Exception\FileNotFoundException;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class ImportMovielensDataCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('movielens:import')
            ->addArgument('data-folder', InputArgument::REQUIRED)
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getConnection()
            ->executeQuery('SET wait_timeout = 60 * 60 * 24')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dataFolder = $input->getArgument('data-folder');

        try {
            $moviesFile  = new MovieLensFile($dataFolder . '/movies.dat');
            $ratingsFile = new MovieLensFile($dataFolder . '/ratings.dat');

            $this->runImporter($moviesFile, $output, 'movie');
            $this->runImporter($ratingsFile, $output, 'rating');

            $output->writeln("<info>Ran all imports successfully.</info>");

            return 0;
        } catch (FileNotFoundException $e) {
            $output->writeln('<error>The data folder doesn\'t contain all the expected files.</error>');
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return 1;
        }
    }

    private function runImporter(MovieLensFile $file, OutputInterface $output, $importerType)
    {
        $plural = $importerType . 's';
        /** @var $importer \Emovie\MovieLensBundle\Importer\Importer */
        $importer = $this->getContainer()->get('emovie_movie_lens.importer.' . $importerType);

        $output->writeln("<info>Starting {$plural} import...</info>");
        $importer->importFromFile($file);
        $output->writeln("<info>Finished importing {$plural}.</info>");
    }
}
