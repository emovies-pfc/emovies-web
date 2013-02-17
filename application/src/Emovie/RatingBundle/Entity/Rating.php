<?php
namespace Emovie\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Emovie\MovieBundle\Entity\Movie;
use Emovie\UserBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="rating")
 *
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Emovie\MovieBundle\Entity\Movie")
     * @ORM\JoinColumn(name="movie_id", referencedColumnName="id")
     *
     * @var \Emovie\MovieBundle\Entity\Movie
     */
    protected $movie;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Emovie\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var \Emovie\UserBundle\Entity\User
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $score;

    public function __construct(User $user, Movie $movie)
    {
        $this->user  = $user;
        $this->movie = $movie;
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return \Emovie\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \Emovie\MovieBundle\Entity\Movie
     */
    public function getMovie()
    {
        return $this->movie;
    }
}
