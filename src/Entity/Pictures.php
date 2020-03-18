<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PicturesRepository")
 */
class Pictures
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Company", mappedBy="pictures", cascade={"persist", "remove"})
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalFilename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mimeType;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\School", mappedBy="pictures", cascade={"persist", "remove"})
     */
    private $school;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Student", mappedBy="pictures", cascade={"persist", "remove"})
     */
    private $student;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        // set (or unset) the owning side of the relation if necessary
        $newPictures = null === $company ? null : $this;
        if ($company->getPictures() !== $newPictures) {
            $company->setPictures($newPictures);
        }

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

        // set (or unset) the owning side of the relation if necessary
        $newPictures = null === $school ? null : $this;
        if ($school->getPictures() !== $newPictures) {
            $school->setPictures($newPictures);
        }

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        // set (or unset) the owning side of the relation if necessary
        $newPictures = null === $student ? null : $this;
        if ($student->getPictures() !== $newPictures) {
            $student->setPictures($newPictures);
        }

        return $this;
    }
}
