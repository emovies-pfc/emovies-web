<?php

namespace Emovie\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EmovieRatingBundle:Default:index.html.twig', array('name' => $name));
    }
}
