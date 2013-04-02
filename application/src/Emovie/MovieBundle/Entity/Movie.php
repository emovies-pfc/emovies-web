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
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    protected $year;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    protected $runtime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $criticsConsensus;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $rottenTomatoesId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    protected $synopsis;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $criticsConsensus
     */
    public function setCriticsConsensus($criticsConsensus)
    {
        $this->criticsConsensus = $criticsConsensus;
    }

    /**
     * @return string
     */
    public function getCriticsConsensus()
    {
        return $this->criticsConsensus;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $rottenTomatoesId
     */
    public function setRottenTomatoesId($rottenTomatoesId)
    {
        $this->rottenTomatoesId = $rottenTomatoesId;
    }

    /**
     * @return int
     */
    public function getRottenTomatoesId()
    {
        return $this->rottenTomatoesId;
    }

    /**
     * @param string $synopsis
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    /**
     * @return string
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * @param int $runtime
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
    }

    /**
     * @return int
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
}
