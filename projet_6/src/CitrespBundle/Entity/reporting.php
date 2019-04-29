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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return reporting
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
}
