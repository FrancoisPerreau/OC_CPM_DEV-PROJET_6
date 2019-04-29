<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=100, unique=true)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=122, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=122)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=122)
     */
    private $role;

    /**
     * @var bool
     *
     * @ORM\Column(name="mailable", type="boolean")
     */
    private $mailable;

    /**
     * @var bool
     *
     * @ORM\Column(name="remember", type="boolean")
     */
    private $remember;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pseudo.
     *
     * @param string $pseudo
     *
     * @return User
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo.
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set role.
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set mailable.
     *
     * @param bool $mailable
     *
     * @return User
     */
    public function setMailable($mailable)
    {
        $this->mailable = $mailable;

        return $this;
    }

    /**
     * Get mailable.
     *
     * @return bool
     */
    public function getMailable()
    {
        return $this->mailable;
    }

    /**
     * Set remember.
     *
     * @param bool $remember
     *
     * @return User
     */
    public function setRemember($remember)
    {
        $this->remember = $remember;

        return $this;
    }

    /**
     * Get remember.
     *
     * @return bool
     */
    public function getRemember()
    {
        return $this->remember;
    }
}
