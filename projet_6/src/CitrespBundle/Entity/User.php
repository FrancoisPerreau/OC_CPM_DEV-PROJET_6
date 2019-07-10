<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FosUser;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\UserRepository")
 * @UniqueEntity("username", message="Ce nom est déjà utilisé")
 * @UniqueEntity("email", message="Cet e-mail est déjà utilisé")
 *
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
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\City", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;



    public function __construct()
    {
        parent::__construct();

        // Add USER_ROLE
        // $this->roles = ["ROLE_CR"];
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
