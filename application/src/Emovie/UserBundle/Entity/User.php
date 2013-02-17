<?php
namespace Emovie\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true, unique=true)
     */
    protected $movielensId;

    /**
     * @param int $movielensId
     */
    public function setMovielensId($movielensId)
    {
        $this->movielensId = $movielensId;
    }

    /**
     * @return int
     */
    public function getMovielensId()
    {
        return $this->movielensId;
    }
}
