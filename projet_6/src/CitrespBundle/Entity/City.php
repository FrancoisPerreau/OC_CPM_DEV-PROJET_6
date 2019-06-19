<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\CityRepository")
 */
class City
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="zipcode", type="integer")
     * @Assert\NotBlank()
     */
    private $zipcode;

    /**
     * @Gedmo\Slug(fields={"name", "zipcode"})
     * @ORM\Column(length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="gps_lat", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $gpsLat;

    /**
     * @var string
     *
     * @ORM\Column(name="gps_lng", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $gpsLng;



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
     * Set name.
     *
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set zipcode.
     *
     * @param int $zipcode
     *
     * @return City
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return int
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }


    public function getSlug()
    {
        return $this->slug;
    }


    public function getCityLabel()
    {
      return $this->name . ' (' . $this->zipcode . ')';
    }


    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return City
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }


    /**
     * Set gpsLat.
     *
     * @param string $gpsLat
     *
     * @return City
     */
    public function setGpsLat($gpsLat)
    {
        $this->gpsLat = $gpsLat;

        return $this;
    }

    /**
     * Get gpsLat.
     *
     * @return string
     */
    public function getGpsLat()
    {
        return $this->gpsLat;
    }

    /**
     * Set gpsLng.
     *
     * @param string $gpsLng
     *
     * @return City
     */
    public function setGpsLng($gpsLng)
    {
        $this->gpsLng = $gpsLng;

        return $this;
    }

    /**
     * Get gpsLng.
     *
     * @return string
     */
    public function getGpsLng()
    {
        return $this->gpsLng;
    }
}
