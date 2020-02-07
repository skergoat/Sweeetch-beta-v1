<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adress;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $drivingLicense;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $disabled;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", inversedBy="student", cascade={"persist", "remove"})
     */
    private $profile;

      /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resume", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $resume;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\IdCard", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $idCard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\StudentCard", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $studentCard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProofHabitation", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proofHabitation;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getTelNumber(): ?string
    {
        return $this->telNumber;
    }

    public function setTelNumber(string $telNumber): self
    {
        $this->telNumber = $telNumber;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDrivingLicense(): ?bool
    {
        return $this->drivingLicense;
    }

    public function setDrivingLicense(?bool $drivingLicense): self
    {
        $this->drivingLicense = $drivingLicense;

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(?bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(Resume $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getIdCard(): ?IdCard
    {
        return $this->idCard;
    }

    public function setIdCard(IdCard $idCard): self
    {
        $this->idCard = $idCard;

        // set the owning side of the relation if necessary
        if ($idCard->getStudent() !== $this) {
            $idCard->setStudent($this);
        }

        return $this;
    }

    public function getStudentCard(): ?StudentCard
    {
        return $this->studentCard;
    }

    public function setStudentCard(?StudentCard $studentCard): self
    {
        $this->studentCard = $studentCard;

        return $this;
    }

    public function getProofHabitation(): ?ProofHabitation
    {
        return $this->proofHabitation;
    }

    public function setProofHabitation(ProofHabitation $proofHabitation): self
    {
        $this->proofHabitation = $proofHabitation;

        return $this;
    }

}
