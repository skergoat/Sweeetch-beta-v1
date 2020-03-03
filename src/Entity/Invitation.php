<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationRepository")
 */
class Invitation
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
    private $Emails;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Emailsent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $opened;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clicked;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subscribed;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $signupIP;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $signupTimestamp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $confirmationIP;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $confirmationTimestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmails(): ?string
    {
        return $this->Emails;
    }

    public function setEmails(string $Emails): self
    {
        $this->Emails = $Emails;

        return $this;
    }

    public function getEmailsent(): ?string
    {
        return $this->Emailsent;
    }

    public function setEmailsent(string $Emailsent): self
    {
        $this->Emailsent = $Emailsent;

        return $this;
    }

    public function getOpened(): ?string
    {
        return $this->opened;
    }

    public function setOpened(string $opened): self
    {
        $this->opened = $opened;

        return $this;
    }

    public function getClicked(): ?string
    {
        return $this->clicked;
    }

    public function setClicked(string $clicked): self
    {
        $this->clicked = $clicked;

        return $this;
    }

    public function getSubscribed(): ?string
    {
        return $this->subscribed;
    }

    public function setSubscribed(string $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getSignupIP(): ?string
    {
        return $this->signupIP;
    }

    public function setSignupIP(string $signupIP): self
    {
        $this->signupIP = $signupIP;

        return $this;
    }

    public function getSignupTimestamp(): ?string
    {
        return $this->signupTimestamp;
    }

    public function setSignupTimestamp(string $signupTimestamp): self
    {
        $this->signupTimestamp = $signupTimestamp;

        return $this;
    }

    public function getConfirmationIP(): ?string
    {
        return $this->confirmationIP;
    }

    public function setConfirmationIP(string $confirmationIP): self
    {
        $this->confirmationIP = $confirmationIP;

        return $this;
    }

    public function getConfirmationTimestamp(): ?string
    {
        return $this->confirmationTimestamp;
    }

    public function setConfirmationTimestamp(string $confirmationTimestamp): self
    {
        $this->confirmationTimestamp = $confirmationTimestamp;

        return $this;
    }
}
