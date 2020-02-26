<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     *     message="Entrez un prénom valide svp"
     * )
     */
    private $firstname;

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
    private $address;

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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Regex(
     *     pattern="/^0[1-68]([-. ]?[0-9]{2}){4}$/",
     *     message="Entrez un numéro valide svp"
     * )
     */
    private $telNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Regex(
     *     pattern="/^(([-. ]?[0-9]{3}){3})([-. ]?[0-9]{5})$/",
     *     message="Entrez un numéro de siret valide svp"
     * )
     */
    private $siret;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="company", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ requis")
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/[a-zA-Z0-9- ]/",
     *     message="Entrez un nom valide svp"
     * )
     */
    private $companyName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Offers", mappedBy="company")
     */
    private $offers;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setTelNumber(string $telNumber): self
    {
        $this->telNumber = $telNumber;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

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

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return Collection|Offers[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offers $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setCompany($this);
        }

        return $this;
    }

    public function removeOffer(Offers $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getCompany() === $this) {
                $offer->setCompany(null);
            }
        }

        return $this;
    }
}
