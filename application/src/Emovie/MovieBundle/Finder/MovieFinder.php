<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lumbendil
 * Date: 8/04/13
 * Time: 22:17
 * To change this template use File | Settings | File Templates.
 */

namespace Emovie\MovieBundle\Finder;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityRepository;

/**
 * @DI\Service("emovie_movie.finder")
 */
class MovieFinder
{
    private $movieRepository;

    /**
     * @DI\InjectParams({
     *     "repository" = @DI\Inject("emovie_movie.movie.repository")
     * })
     */
    public function __construct(EntityRepository $repository)
    {
        $this->movieRepository = $repository;
    }

    public function searchByTerm($term)
    {
        $query = $this->movieRepository
            ->createQueryBuilder('m')
            ->andWhere('m.name LIKE :search')
            ->getQuery()
            ->setParameter('search', '%' . $term . '%')
        ;

        return $query->execute();
    }
}
