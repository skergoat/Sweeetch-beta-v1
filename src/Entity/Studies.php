<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudiesRepository")
 */
class Studies
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $Description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\School", inversedBy="studies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $school;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domain;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recruit", mappedBy="studies", orphanRemoval=true)
     */
    private $recruits;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title3;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description3;

    public function __construct()
    {
        $this->recruits = new ArrayCollection();
    }

    // /**
    //  * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="studies", orphanRemoval=true, cascade={"persist"})
    //  */
    // private $sessions;

    // public function __construct()
    // {
    //     $this->sessions = new ArrayCollection();
    // }

    //  /**
    //  * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="profile", orphanRemoval=true, cascade={"persist"})
    //  */
    // private $sessions;

    // public function __construct()
    // {
    //     $this->sessions = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection|Recruit[]
     */
    public function getRecruits(): Collection
    {
        return $this->recruits;
    }

    public function addRecruit(Recruit $recruit): self
    {
        if (!$this->recruits->contains($recruit)) {
            $this->recruits[] = $recruit;
            $recruit->setStudies($this);
        }

        return $this;
    }

    public function removeRecruit(Recruit $recruit): self
    {
        if ($this->recruits->contains($recruit)) {
            $this->recruits->removeElement($recruit);
            // set the owning side to null (unless already changed)
            if ($recruit->getStudies() === $this) {
                $recruit->setStudies(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection|Session[]
    //  */
    // public function getSessions(): Collection
    // {
    //     return $this->sessions;
    // }

    // public function addSession(Session $session): self
    // {
    //     if (!$this->sessions->contains($session)) {
    //         $this->sessions[] = $session;
    //         $session->setStudies($this);
    //     }

    //     return $this;
    // }

    // public function removeSession(Session $session): self
    // {
    //     if ($this->sessions->contains($session)) {
    //         $this->sessions->removeElement($session);
    //         // set the owning side to null (unless already changed)
    //         if ($session->getStudies() === $this) {
    //             $session->setStudies(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection|Session[]
    //  */
    // public function getSessions(): Collection
    // {
    //     return $this->sessions;
    // }

    // public function addSession(Language $session): self
    // {
    //     if (!$this->sessions->contains($session)) {
    //         $this->sessions[] = $session;
    //         $session->setStudies($this);
    //     }

    //     return $this;
    // }

    // public function removeSession(Session $session): self
    // {
    //     if ($this->sessions->contains($session)) {
    //         $this->sessions->removeElement($session);
    //         // set the owning side to null (unless already changed)
    //         if ($session->getStudies() === $this) {
    //             $session->setStudies(null);
    //         }
    //     }

    //     return $this;
    // }


    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getTitle1(): ?string
    {
        return $this->title1;
    }

    public function setTitle1(?string $title1): self
    {
        $this->title1 = $title1;

        return $this;
    }

    public function getTitle2(): ?string
    {
        return $this->title2;
    }

    public function setTitle2(?string $title2): self
    {
        $this->title2 = $title2;

        return $this;
    }

    public function getDescription2(): ?string
    {
        return $this->description2;
    }

    public function setDescription2(?string $description2): self
    {
        $this->description2 = $description2;

        return $this;
    }

    public function getTitle3(): ?string
    {
        return $this->title3;
    }

    public function setTitle3(?string $title3): self
    {
        $this->title3 = $title3;

        return $this;
    }

    public function getDescription3(): ?string
    {
        return $this->description3;
    }

    public function setDescription3(?string $description3): self
    {
        $this->description3 = $description3;

        return $this;
    }
}
