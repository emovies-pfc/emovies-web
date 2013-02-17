<?php
namespace Emovie\MovieLensBundle\Importer;

use Emovie\MovieLensBundle\File\MovieLensFile;
use Doctrine\ORM\EntityManager;
use Emovie\UserBundle\Entity\User;
use Doctrine\ORM\Query;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class RatingImporter implements Importer
{
    const MAX_BATCH_SIZE = 100000;

    protected $manager;
    protected $connection;
    protected $movies = array();

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->connection = $this->manager->getConnection();
    }

    public function importFromFile(MovieLensFile $file)
    {
        $totalLines     = $file->getTotalRecords();

        $totalBatches = ceil($totalLines/self::MAX_BATCH_SIZE);

        for ($batch = 0; $batch < $totalBatches; ++$batch) {
            $batchSize = min(self::MAX_BATCH_SIZE, $totalLines - $batch * self::MAX_BATCH_SIZE);

            $this->processBatch($file, $batchSize);
        }
    }

    private function processBatch(MovieLensFile $file, $batchSize)
    {
        $previousUserMovielensId = null;
        $userId = null;

        $movieQuery = $this->getMovieQuery();
        $userQuery = $this->getUserQuery();

        $query = 'INSERT IGNORE INTO rating(movie_id, user_id, score) VALUES ' . substr(str_repeat('(?,?,?),', $batchSize), 0, -1);

        $insertRatingQuery =
            $this->connection->prepare($query);

        $currentParam   = 0;

        for ($i = 0; $i < $batchSize; ++$i) {
            list($movielensUserId, $movielensMovieId, $score, $timestamp) = $file->getNextRecord();

            if (array_key_exists($movielensMovieId, $this->movies)) {
                $movieId = $this->movies[$movielensMovieId];
            } else {
                $movieId = $movieQuery->execute(array('movielensId' => $movielensMovieId));
                $this->movies[$movielensMovieId] = $movieId;
            }

            if ($movielensUserId != $previousUserMovielensId) {
                try {
                    $userId  = $userQuery->execute(array('movielensId' => $movielensUserId));
                } catch (\Doctrine\ORM\NoResultException $exception) {
                    $userId = $this->createUser($movielensUserId);
                }
            }

            $insertRatingQuery->bindValue(++$currentParam, $movieId);
            $insertRatingQuery->bindValue(++$currentParam, $userId);
            $insertRatingQuery->bindValue(++$currentParam, $score);
        }

        $this->manager->clear();
        $insertRatingQuery->execute();
    }

    private function getUserQuery()
    {
        return $this->manager
            ->createQueryBuilder()
            ->select('u.id')
            ->from('EmovieUserBundle:User', 'u')
            ->where('u.movielensId = :movielensId')
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_SINGLE_SCALAR);
    }

    private function getMovieQuery()
    {
        return $this->manager
            ->createQueryBuilder()
            ->select('m.id')
            ->from('EmovieMovieBundle:Movie', 'm')
            ->where('m.movielensId = :movielensId')
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param $movielensId
     *
     * @return integer The ID of the created user.
     */
    private function createUser($movielensId)
    {
        $user = new User();

        $user->setMovielensId($movielensId);
        $user->setEmail("movielens_$movielensId@movielens.com");
        $user->setUsername("movielens_$movielensId");
        $user->setPlainPassword("movielens_$movielensId");

        $this->manager->persist($user);
        $this->manager->flush($user);

        return $user->getId();
    }
}
