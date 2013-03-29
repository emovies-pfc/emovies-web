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

class MovieAdminController extends CRUDController
{
    public function createAction()
    {
        if ($this->getRequest()->query->has('rt-id')) {
            $this->admin->setRottenTomatoesId($this->getRequest()->query->get('rt-id'));
        }

        return parent::createAction();
    }
}
