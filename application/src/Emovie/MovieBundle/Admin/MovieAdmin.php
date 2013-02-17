<?php
namespace Emovie\MovieBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MovieAdmin extends Admin
{
    protected function configureFormFields(FormMapper $mapper)
    {
        $mapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->add('name')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $mapper)
    {
        $mapper
            ->add('name')
        ;
    }

    protected function configureShowFields(ShowMapper $mapper)
    {
        $mapper
            ->add('name')
        ;
    }
}
