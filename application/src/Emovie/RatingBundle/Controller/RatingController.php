<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lumbendil
 * Date: 8/04/13
 * Time: 22:55
 * To change this template use File | Settings | File Templates.
 */

namespace Emovie\RatingBundle\Controller;


use Emovie\MovieBundle\Entity\Movie;
use Emovie\RatingBundle\Entity\Rating;
use Emovie\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use JMS\SecurityExtraBundle\Annotation as Security;

class RatingController extends Controller
{
    /**
     * @Route("/esi/widget/{movie}")
     * @Template
     */
    public function widgetAction(Movie $movie)
    {
        if (!($user = $this->getUser())) {
            throw $this->createNotFoundException();
        }

        $form = $this->getRatingForm($user, $movie);
        return ['movie' => $movie, 'form' => $form->createView()];
    }

    /**
     * @Route("/rating/submit/movie/{movie}")
     * @Security\PreAuthorize("isAuthenticated()")
     */
    public function submitAction(Movie $movie)
    {
        $redirectUrl = $this->get('router')->generate(
            'emovie_movie_movie_show', array('id' => $movie->getId())
        );

        $response = $this->redirect($redirectUrl);

        // This case shouldn't happen due to the security annotation.
        if (!($user = $this->getUser())) {
            return $response;
        }

        $form = $this->getRatingForm($user, $movie);

        $request = $this->getRequest();
        $session = $request->getSession();
        $flashBag = $request->getSession()->getFlashBag();

        $form->bind($request);

        if ($form->isValid()) {
            $rating = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($rating);
            $manager->flush();

            $flashBag->add('success', 'Successfully rated the movie.');
        } else {
            // TODO: Better error handling.
            $flashBag->add('error', 'An error ocurred when rating the movie.');
        }

        return $response;
    }

    private function getRatingForm(User $user, Movie $movie)
    {
        $rating = $this->getDoctrine()
            ->getRepository('EmovieRatingBundle:Rating')
            ->findOneBy(array('movie' => $movie->getId(), 'user' => $user->getId()))
        ;

        if (!$rating) {
            $rating = new Rating($user, $movie);
        }

        return $this->createFormBuilder($rating)
            ->add('score')
            ->getForm();
    }
}
