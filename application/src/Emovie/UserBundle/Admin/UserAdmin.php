<?php
namespace Emovie\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class UserAdmin extends Admin
{
    protected function configureFormFields(FormMapper $mapper)
    {
        $mapper
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'text')
        ;
    }

    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->add('username')
            ->add('email')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $mapper)
    {
        $mapper
            ->add('username')
            ->add('email')
        ;
    }

    protected function configureShowFields(ShowMapper $mapper)
    {
        $mapper
            ->add('username')
            ->add('email')
        ;
    }
}
