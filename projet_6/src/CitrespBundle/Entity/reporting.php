<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * reporting
 *
 * @ORM\Table(name="reporting")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\reportingRepository")
 */
class Reporting
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
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reportedCount", type="integer", nullable=true)
     */
    private $reportedCount;

    /**
     * @var bool
     *
     * @ORM\Column(name="moderate", type="boolean")
     */
    private $moderate;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=250)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="gps_lat", type="string", length=150)
     */
    private $gpsLat;

    /**
     * @var string
     *
     * @ORM\Column(name="gps_lng", type="string", length=150)
     */
    private $gpsLng;

    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\City", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\User", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\Category"))
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;



    public function __construct()
    {
      $this->dateCreated = new \Datetime('now');
      $this->moderate = 0;
    }





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
     * Set description.
     *
     * @param string|null $description
     *
     * @return reporting
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreated.
     *
     * @param \DateTime $dateCreated
     *
     * @return reporting
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set reportedCount.
     *
     * @param int|null $reportedCount
     *
     * @return reporting
     */
    public function setReportedCount($reportedCount = null)
    {
        $this->reportedCount = $reportedCount;

        return $this;
    }

    /**
     * Get reportedCount.
     *
     * @return int|null
     */
    public function getReportedCount()
    {
        return $this->reportedCount;
    }

    /**
     * Set moderate.
     *
     * @param bool $moderate
     *
     * @return reporting
     */
    public function setModerate($moderate)
    {
        $this->moderate = $moderate;

        return $this;
    }

    /**
     * Get moderate.
     *
     * @return bool
     */
    public function getModerate()
    {
        return $this->moderate;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Reporting
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set gpsLat.
     *
     * @param string $gpsLat
     *
     * @return Reporting
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
     * @return Reporting
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

    /**
     * Set city.
     *
     * @param \CitrespBundle\Entity\City $city
     *
     * @return Reporting
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

    /**
     * Set user.
     *
     * @param \CitrespBundle\Entity\User|null $user
     *
     * @return Reporting
     */
    public function setUser(\CitrespBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \CitrespBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set category.
     *
     * @param \CitrespBundle\Entity\Category $category
     *
     * @return Reporting
     */
    public function setCategory(\CitrespBundle\Entity\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return \CitrespBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
