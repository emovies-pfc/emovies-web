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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
}
