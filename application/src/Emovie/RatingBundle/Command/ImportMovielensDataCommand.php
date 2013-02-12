<?php
namespace Emovie\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Emovie\UserBundle\Entity\User;
use Doctrine\ORM\Query;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class ImportMovielensDataCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    public function configure()
    {
        $this
            ->setName('movielens:import')
            ->addArgument('data-folder', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dataFolder = $input->getArgument('data-folder');

        $moviesFile  = $dataFolder . '/movies.dat';
        $ratingsFile = $dataFolder . '/ratings.dat';

        if (!file_exists($moviesFile) || !file_exists($ratingsFile)) {
            $output->writeln('<error>The data folder doesn\'t contain all the expected files.</error>');
            $output->writeln('<info>Expected files:</info>');
            $output->writeln("\t<info>$moviesFile</info>");
            $output->writeln("\t<info>$ratingsFile</info>");
            return 1;
        }
        /** @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager   = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->connection = $entityManager->getConnection();
        $this->connection->executeQuery('SET wait_timeout = 60 * 60 * 24');

        // $this->importMovies($moviesFile, $output);
        $this->importRatings($ratingsFile, $output);

        $output->writeln("<info>Finished all imports successfully.</info>");

        return 0;
    }

    protected function importMovies($moviesFile, OutputInterface $output)
    {
        /** @var $entityManager \Doctrine\ORM\EntityManager */
        $output->writeln('<info>Starting movies import...</info>');
        $moviesHandler = fopen($moviesFile, 'r');
        $totalLines    = $this->getLineCount($moviesHandler);
        $readLines     = 0;

        $insertMovieQuery =
            $this->connection->prepare('INSERT IGNORE INTO movie(movielensId, name) VALUES (:movielens_id, :name)');

        while ($line = fgets($moviesHandler)) {
            list($movielensId, $name, $tags) = explode('::', $line);

            // Insert using MySQL INSERT IGNORE.
            $insertMovieQuery->execute(array('movielens_id' => $movielensId, 'name' => $name));

            ++$readLines;

            if ($readLines % 1000 === 0) {
                $percentageComplete = $readLines/$totalLines * 100;
                $output->writeln("<info>Read {$readLines}/{$totalLines} ({$percentageComplete}%)</info>");
            }
        }

        $output->writeln('<info>Finished importing movies...</info>');
    }

    protected function importRatings($ratingsFile, OutputInterface $output)
    {
        /** @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager    = $this->getContainer()->get('doctrine.orm.entity_manager');
        $movieRepository  = $entityManager->getRepository('EmovieMovieBundle:Movie');
        $userRepository   = $entityManager->getRepository('EmovieUserBundle:User');

        $output->writeln('<info>Starting ratings import...</info>');

        $ratingsHandler = fopen($ratingsFile, 'r');
        $readLines      = 0;
        $totalLines     = 10000054; // $this->getLineCount($ratingsHandler);
        $currentParam   = 0;

        // Caching variables.
        $memcached = new \Memcached();
        $memcached->addServer('127.0.0.1', 11211);

        $previousUserMovielensId = null;
        $userId                  = null;

        $query = 'INSERT IGNORE INTO rating(movie_id, user_id, score) VALUES ' . substr(str_repeat('(?,?,?),', 10000054), 0, 1);

        $insertRatingQuery =
            $this->connection->prepare($query);

        unset($query);

        $userQuery = $userRepository->createQueryBuilder('u')
            ->select('u.id')
            ->where('u.movielensId = :movielensId')
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_SINGLE_SCALAR);

        $movieQuery = $movieRepository->createQueryBuilder('m')
            ->select('m.id')
            ->where('m.movielensId = :movielensId')
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_SINGLE_SCALAR);

        $firstTime = $time = microtime(true);

        while ($line = fgets($ratingsHandler)) {
            list($movielensUserId, $movielensMovieId, $score, $timestamp) = explode('::', $line);

            // The movie should always be found, since they're created in the previous step.
            $movieId = $memcached->get('movie_' . $movielensMovieId);

            if (!$movieId) {
                $movieId = $movieQuery->execute(array('movielensId' => $movielensMovieId));
                $memcached->set('movie_' . $movielensMovieId, $movieId);
            }

            if ($movielensUserId != $previousUserMovielensId) {
                try {
                    $userId  = $userQuery->execute(array('movielensId' => $movielensUserId));
                } catch (\Doctrine\ORM\NoResultException $exception) {
                    $output->writeln("<info>Importing user \"$movielensUserId\"...</info>");

                    // Since users are managed by FOS User Bundle, we need to create the entities representing them.
                    $user = new User();

                    $user->setMovielensId($movielensUserId);
                    $user->setEmail("movielens_$movielensUserId@movielens.com");
                    $user->setUsername("movielens_$movielensUserId");
                    $user->setPlainPassword("movielens_$movielensUserId");

                    $entityManager->persist($user);
                    $entityManager->flush($user);

                    $userId = $user->getId();
                }
            }

            $insertRatingQuery->bindValue(++$currentParam, $movieId);
            $insertRatingQuery->bindValue(++$currentParam, $userId);
            $insertRatingQuery->bindValue(++$currentParam, $score);

            ++$readLines;
            if ($readLines % 1000 === 0) {
                $percentageComplete = number_format($readLines/$totalLines * 100, 2);
                $output->writeln("<info>Read {$readLines}/{$totalLines} ({$percentageComplete}%)</info>");
                $newTime  = microtime(true);
                $spent    = number_format(($newTime - $time), 2);
                $total    = number_format(($newTime - $firstTime), 2);
                $time     = $newTime;
                $memUsage = memory_get_peak_usage() / (1024 * 1024) ;
                $output->writeln("1000 lines processed in {$spent}s. {$readLines} processed in: {$total}s. MemUsage: {$memUsage}MBs.");
            }
        }

        $insertRatingQuery->execute();

        $output->writeln('<info>Finished importing ratings...</info>');
    }

    protected function getLineCount($fileHandler)
    {
        rewind($fileHandler);

        $lines = 0;

        while(fgets($fileHandler)) {
            ++$lines;
        }

        rewind($fileHandler);

        return $lines;
    }
}
