<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lumbendil
 * Date: 30/03/13
 * Time: 0:37
 * To change this template use File | Settings | File Templates.
 */

namespace Emovie\MovieBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MovieAdminController extends CRUDController
{
    public function createAction()
    {
        if ($this->getRequest()->query->has('rt-id')) {
            $this->admin->setRottenTomatoesId($this->getRequest()->query->get('rt-id'));
        }

        return parent::createAction();
    }

    public function importAction()
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $request = $this->getRequest();

        $form = $this->createFormBuilder()
            ->add('search', 'search')
            ->getForm();

        $formView = $form->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        $templateData = array(
            'form' => $formView
        );

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $searchResults = $this->admin->searchRottenTomatoesMovies($form->getData()['search']);

                $templateData['search_results'] = $searchResults;
            }
        }

        return $this->render('EmovieMovieBundle:CRUD:import.html.twig', $templateData);
    }
}
