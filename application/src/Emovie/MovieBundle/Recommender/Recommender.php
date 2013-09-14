<?php


namespace Emovie\MovieBundle\Recommender;


use Doctrine\ORM\EntityManager;
use Emovie\MovieBundle\Entity\Movie;
use FOS\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Debug\Stopwatch;

class Recommender
{
    private $entityManager;
    private $gearmanClient;
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    public function __construct(EntityManager $entityManager, \GearmanClient $gearmanClient, Stopwatch $stopwatch = null)
    {
        $this->entityManager = $entityManager;
        $this->gearmanClient = $gearmanClient;
        $this->stopwatch = $stopwatch;
    }

    /**
     * @param User $user
     * @return Movie[]
     */
    public function recommend(User $user)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('recommend', 'recommend');
        }

        $recommendations = json_decode($this->gearmanClient->doNormal('recommend', $user->getId()), true);

        if ($this->stopwatch) {
            $this->stopwatch->stop('recommend');
        }

        if (is_null($recommendations)) {
            return array();
        }

        $movieIds = array_map(function($element) {return $element['itemID']; }, $recommendations);

        $this->entityManager->getConnection()->close();
        $this->entityManager->getConnection()->connect();
        $movies = $this->entityManager->getRepository('EmovieMovieBundle:Movie')->findBy(['id' => $movieIds]);

        usort($movies, function(Movie $movie1, Movie $movie2) use ($movieIds) {
                return array_search($movie1->getId(), $movieIds) - array_search($movie2->getId(), $movieIds);
            });



        return $movies;
    }
}