<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplyRepository")
 */
class Apply
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Offers", inversedBy="applies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $offers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="applies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hired;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $refused;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffers(): ?Offers
    {
        return $this->offers;
    }

    public function setOffers(?Offers $offers): self
    {
        $this->offers = $offers;

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

    public function setHired(?bool $hired): self
    {
        $this->hired = $hired;

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(?bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getRefused(): ?bool
    {
        return $this->refused;
    }

    public function setRefused(?bool $refused): self
    {
        $this->refused = $refused;

        return $this;
    }
}
