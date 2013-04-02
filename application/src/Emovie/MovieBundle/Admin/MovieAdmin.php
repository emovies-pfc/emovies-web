<?php
namespace Emovie\MovieBundle\Admin;

use Emovie\MovieBundle\Entity\Movie;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\RottenTomatoes\RottenTomatoesClient;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MovieAdmin extends Admin
{
    /**
     * @var RottenTomatoesClient
     */
    private $rottenTomatoesClient;
    private $rottenTomatoesId;

    /**
     * @param RottenTomatoesClient $client
     */
    public function setRottenTomatoesClient(RottenTomatoesClient $client)
    {
        $this->rottenTomatoesClient = $client;
    }

    public function setRottenTomatoesId($id)
    {
        $this->rottenTomatoesId = $id;
    }

    public function getNewInstance()
    {
        /** @var $instance Movie */
        $instance = parent::getNewInstance();

        if (!$this->rottenTomatoesId) {
            return $instance;
        }

        try {
        $movieInfo = $this->rottenTomatoesClient
            ->getCommand('MovieInfo', array('id' => $this->rottenTomatoesId))
            ->execute();

            $instance->setRottenTomatoesId($movieInfo['id']);
            $instance->setName($movieInfo['title']);
            $instance->setYear($movieInfo['year']);
            $instance->setRuntime($movieInfo['runtime']);
            $instance->setCriticsConsensus($movieInfo['critics_consensus']);
            $instance->setSynopsis($movieInfo['synopsis']);

        } catch (BadResponseException $e) {
            $this->request->getSession()->getFlashBag()->add('sonata_flash_warning', 'Invalid Rotten Tomatoes ID.');
        }

        return $instance;
    }

    public function searchRottenTomatoesMovies($query)
    {
        return $this->rottenTomatoesClient->getCommand('SearchMovies', array('q' => $query))->execute();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('import');
    }

    protected function configureFormFields(FormMapper $mapper)
    {
        $mapper
            ->add('name')
            ->add('year')
            ->add('runtime')
            ->add('criticsConsensus')
            ->add('synopsis')
            ->add('rottenTomatoesId', 'hidden')
        ;
    }

    protected function configureListFields(ListMapper $mapper)
    {
        $mapper
            ->addIdentifier('id')
            ->add('name')
            ->add('rottenTomatoesId', 'boolean', array('label' => 'Is from Rotten Tomatoes?'))
            ->add('year')
            ->add('runtime')
            ->add('synopsis', 'boolean', array('label' => 'Has synopsis?'))
            ->add('criticsConsensus', 'boolean', array('label' => 'Has critics consensus?'))
            ->add('_action', 'actions', array( 'actions' =>
                array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $mapper)
    {
        $mapper
            ->add('name')
            ->add('year')
            ->add('runtime')
        ;
    }

    protected function configureShowFields(ShowMapper $mapper)
    {
        $mapper
            ->add('id')
            ->add('name')
            ->add('rottenTomatoesId')
            ->add('year')
            ->add('runtime')
            ->add('criticsConsensus')
            ->add('synopsis')
        ;
    }
}
