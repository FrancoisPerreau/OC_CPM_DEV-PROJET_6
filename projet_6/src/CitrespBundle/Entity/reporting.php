<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


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
     * @Assert\Length(
     *      min = 5,
     *      max = 1000,
     *      minMessage = "La description doit faire au moins 5 caractères.",
     *      maxMessage = "La description ne doit pas faire plus de 1 000 caractères."
     * )
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
     * * @Assert\Length(
     *      min = 5,
     *      max = 250,
     *      minMessage = "L'adresse doit faire au moins {{ limit }} caractères.",
     *      maxMessage = "L'adresse ne doit pas faire plus de {{ limit }} caractères."
     * )
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
     * @Assert\Valid()
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\User", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\Category"))
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $category;

    /**
     * @ORM\OneToOne(targetEntity="CitrespBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\Status"), cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="resolved", type="boolean")
     */
    private $resolved;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateResolved", type="datetime", nullable=true)
     */
    private $dateResolved;


    private $autocompleteInput;






    public function __construct()
    {
      $this->dateCreated = new \Datetime('now');
      $this->moderate = 0;
      $this->status = new Status;
      $this->resolved = false;
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

    /**
     * Set image.
     *
     * @param \CitrespBundle\Entity\Image|null $image
     *
     * @return Reporting
     */
    public function setImage(\CitrespBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return \CitrespBundle\Entity\Image|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set status.
     *
     * @param \CitrespBundle\Entity\Status $status
     *
     * @return Reporting
     */
    public function setStatus(\CitrespBundle\Entity\Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return \CitrespBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set resolved.
     *
     * @param bool $resolved
     *
     * @return Reporting
     */
    public function setResolved($resolved)
    {
        $this->resolved = $resolved;

        return $this;
    }

    /**
     * Get resolved.
     *
     * @return bool
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * Set dateResolved.
     *
     * @param \DateTime|null $dateResolved
     *
     * @return Reporting
     */
    public function setDateResolved($dateResolved = null)
    {
        $this->dateResolved = $dateResolved;

        return $this;
    }

    /**
     * Get dateResolved.
     *
     * @return \DateTime|null
     */
    public function getDateResolved()
    {
        return $this->dateResolved;
    }



    /**
     * Set autocompleteInput.
     *
     * @param string $gpsLng
     *
     * 
     */
    public function setAutocompleteInput($autocompleteInput)
    {
        $this->autocompleteInput = $autocompleteInput;

        return $this;
    }

    /**
     * Get autocompleteInput.
     *
     * @return string
     */
    public function getAutocompleteInput()
    {
        return $this->autocompleteInput;
    }
}
