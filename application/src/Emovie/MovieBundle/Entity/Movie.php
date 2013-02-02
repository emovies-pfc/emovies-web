<?php
namespace Emovie\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="movie")
 *
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=512, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true, unique=true)
     */
    protected $movielensId;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setMovielensId($movielensId)
    {
        $this->movielensId = $movielensId;
    }

    public function getMovielensId()
    {
        return $this->movielensId;
    }
}
