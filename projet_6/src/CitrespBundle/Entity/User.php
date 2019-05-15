<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FosUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\UserRepository")
 */
class User extends FosUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\City")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;




    public function __construct()
    {
        parent::__construct();

        // Add USER_ROLE
        $this->addRole("ROLE_USER");
    }


    

    /**
     * Set city.
     *
     * @param \CitrespBundle\Entity\City $city
     *
     * @return User
     */
    public function setCity(\CitrespBundle\Entity\City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return \CitrespBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }
}
