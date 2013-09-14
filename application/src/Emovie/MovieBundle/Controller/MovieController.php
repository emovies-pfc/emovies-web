<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lumbendil
 * Date: 8/04/13
 * Time: 22:42
 * To change this template use File | Settings | File Templates.
 */

namespace Emovie\MovieBundle\Controller;

use Emovie\MovieBundle\Entity\Movie;
use Emovie\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieController extends Controller
{
    /**
     * @Route("movie/{id}")
     * @Template()
     */
    public function showAction(Movie $movie)
    {
        return ['movie' => $movie];
    }

    /**
     * @Route("/recommend")
     * @Template()
     */
    public function recommendAction()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException;
        }

        $movies = $this->get('recommender')->recommend($user);

        return ['movies' => $movies];
    }
}
