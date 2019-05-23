<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\commentRepository")
 */
class Comment
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

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
     * @ORM\ManyToOne(targetEntity="CitrespBundle\Entity\Reporting", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     */
    private $reporting;


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
     * Set dateCreated.
     *
     * @param \DateTime $dateCreated
     *
     * @return comment
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
     * Set content.
     *
     * @param string $content
     *
     * @return comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set reportedCount.
     *
     * @param int|null $reportedCount
     *
     * @return comment
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
     * @return comment
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
     * Set reporting.
     *
     * @param \CitrespBundle\Entity\Reporting $reporting
     *
     * @return Comment
     */
    public function setReporting(\CitrespBundle\Entity\Reporting $reporting)
    {
        $this->reporting = $reporting;

        return $this;
    }

    /**
     * Get reporting.
     *
     * @return \CitrespBundle\Entity\Reporting
     */
    public function getReporting()
    {
        return $this->reporting;
    }
}
