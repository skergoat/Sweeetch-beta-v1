<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     */
    private $date_from;

    /**
     * @ORM\Column(type="date")
     */
    private $date_to;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Studies", inversedBy="sessions")
     */
    private $studies;

    // /**
    //  * @ORM\ManyToOne(targetEntity="App\Entity\Studies", inversedBy="sessions")
    //  */
    // private $studies;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->date_from;
    }

    public function setDateFrom(\DateTimeInterface $date_from): self
    {
        $this->date_from = $date_from;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->date_to;
    }

    public function setDateTo(\DateTimeInterface $date_to): self
    {
        $this->date_to = $date_to;

        return $this;
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

    // public function getStudies(): ?Studies
    // {
    //     return $this->studies;
    // }

    // public function setStudies(?Studies $studies): self
    // {
    //     $this->studies = $studies;

    //     return $this;
    // }
}
