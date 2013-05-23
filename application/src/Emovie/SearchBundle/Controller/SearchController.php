<?php

namespace Emovie\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SearchController extends Controller
{
    /**
     * @Route("/search")
     * @Template()
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $term = $request->query->get('q');
        $movies = $this->get('emovie_movie.finder')->searchByTerm($term);

        return array('term' => $term, 'movies' => $movies);
    }
}
