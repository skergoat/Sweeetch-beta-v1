<?php

namespace App\Entity;

use App\Repository\ExperienceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExperienceRepository::class)
 */
class Experience
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
    private $single;

    /**
     * @ORM\ManyToOne(targetEntity=Offers::class, inversedBy="experience")
     */
    private $offers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSingle(): ?string
    {
        return $this->single;
    }

    public function setSingle(?string $single): self
    {
        $this->single = $single;

        return $this;
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
}
