<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="zipcode", type="integer")
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="gps_coordinates", type="string", length=150)
     */
    private $GpsCoordinates;

    /**
     * @Gedmo\Slug(fields={"name", "zipcode"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;




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

    /**
     * Set gpsCoordinates.
     *
     * @param string $gpsCoordinates
     *
     * @return City
     */
    public function setGpsCoordinates($gpsCoordinates)
    {
        $this->GpsCoordinates = $gpsCoordinates;

        return $this;
    }

    /**
     * Get gpsCoordinates.
     *
     * @return string
     */
    public function getGpsCoordinates()
    {
        return $this->GpsCoordinates;
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

}
