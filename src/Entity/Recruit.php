<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecruitRepository")
 */
class Recruit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Studies", inversedBy="recruits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $studies;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="recruits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hired;

    /**
     * @ORM\Column(type="boolean")
     */
    private $agree;

    /**
     * @ORM\Column(type="boolean")
     */
    private $refused;

    /**
     * @ORM\Column(type="boolean")
     */
    private $unavailable;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_recruit;

  /**
     * @ORM\Column(type="boolean")
     */
    private $finished;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_finished;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudies(): ?Studies
    {
        return $this->studies;
    }

    public function setStudies(?Studies $studies): self
    {
        $this->studies = $studies;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getHired(): ?bool
    {
        return $this->hired;
    }

    public function setHired(bool $hired): self
    {
        $this->hired = $hired;

        return $this;
    }

    public function getAgree(): ?bool
    {
        return $this->agree;
    }

    public function setAgree(bool $agree): self
    {
        $this->agree = $agree;

        return $this;
    }

    public function getRefused(): ?bool
    {
        return $this->refused;
    }

    public function setRefused(bool $refused): self
    {
        $this->refused = $refused;

        return $this;
    }

    public function getUnavailable(): ?bool
    {
        return $this->unavailable;
    }

    public function setUnavailable(bool $unavailable): self
    {
        $this->unavailable = $unavailable;

        return $this;
    }

    public function getDateRecruit(): ?\DateTimeInterface
    {
        return $this->date_recruit;
    }

    public function setDateRecruit(\DateTimeInterface $date_recruit): self
    {
        $this->date_recruit = $date_recruit;
    
        return $this;
    }
    
    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getDateFinished(): ?\DateTimeInterface
    {
        return $this->date_finished;
    }

    public function setDateFinished(\DateTimeInterface $date_finished): self
    {
        $this->date_finished = $date_finished;

        return $this;
    }
}
