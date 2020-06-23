<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotNull(message="Champ requis")
     * @Assert\Length(
     *      min = 2,
     *      max = 14,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/[a-zA-Z- ]/",
     *     message="Entrez un prénom valide svp"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Length(
     *      min = 2,
     *      max = 14,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/[a-zA-Z- ]/",
     *     message="Entrez un nom valide svp"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Regex(
     *     pattern="/[a-zA-Z0-9- ]/",
     *     message="Entrez une adresse valide svp"
     * )
     */
    private $adress;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Regex(
     *     pattern="/^\d{5}(?:[-\s]\d{4})?$/",
     *     message="Entrez un code postal valide svp"
     * )
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 14,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum"
     * )
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Regex(
     *     pattern="/^0[1-9]([-. ]?[0-9]{2}){4}$/",
     *     message="Entrez un numéro valide svp"
     * )
     */
    private $telNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/[a-zA-Z- ]/",
     *     message="Entrez une ville valide svp"
     * )
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
     * @Assert\NotBlank(message="Champ requis")
     */
    private $resume;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\IdCard", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $idCard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\StudentCard", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $studentCard;

    // /**
    //  * @ORM\OneToOne(targetEntity="App\Entity\ProofHabitation", inversedBy="student", cascade={"persist", "remove"})
    //  * @ORM\JoinColumn(nullable=false)
    //  * @Assert\NotBlank(message="Champ requis")
    //  */
    // private $proofHabitation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Apply", mappedBy="student")
     */
    private $applies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Recruit", mappedBy="student", orphanRemoval=true)
     */
    private $recruits;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Pictures", inversedBy="student", cascade={"persist", "remove"})
     */
    private $pictures;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $interest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hobbies;


    public function __construct()
    {
        $this->offers = new ArrayCollection();
        $this->applies = new ArrayCollection();
        $this->recruits = new ArrayCollection();
    }



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

    // public function getProofHabitation(): ?ProofHabitation
    // {
    //     return $this->proofHabitation;
    // }

    // public function setProofHabitation(ProofHabitation $proofHabitation): self
    // {
    //     $this->proofHabitation = $proofHabitation;

    //     return $this;
    // }

    /**
     * @return Collection|Apply[]
     */
    public function getApplies(): Collection
    {
        return $this->applies;
    }

    public function addApply(Apply $apply): self
    {
        if (!$this->applies->contains($apply)) {
            $this->applies[] = $apply;
            $apply->setStudent($this);
        }

        return $this;
    }

    public function removeApply(Apply $apply): self
    {
        if ($this->applies->contains($apply)) {
            $this->applies->removeElement($apply);
            // set the owning side to null (unless already changed)
            if ($apply->getStudent() === $this) {
                $apply->setStudent(null);
            }
        }

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
            $recruit->setStudent($this);
        }

        return $this;
    }

    public function removeRecruit(Recruit $recruit): self
    {
        if ($this->recruits->contains($recruit)) {
            $this->recruits->removeElement($recruit);
            // set the owning side to null (unless already changed)
            if ($recruit->getStudent() === $this) {
                $recruit->setStudent(null);
            }
        }

        return $this;
    }

    public function getPictures(): ?Pictures
    {
        return $this->pictures;
    }

    public function setPictures(?Pictures $pictures): self
    {
        $this->pictures = $pictures;

        return $this;
    }

    public function getInterest(): ?string
    {
        return $this->interest;
    }

    public function setInterest(string $interest): self
    {
        $this->interest = $interest;

        return $this;
    }

    public function getHobbies(): ?string
    {
        return $this->hobbies;
    }

    public function setHobbies(?string $hobbies): self
    {
        $this->hobbies = $hobbies;

        return $this;
    }

}
