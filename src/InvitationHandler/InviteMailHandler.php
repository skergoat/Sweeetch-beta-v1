<?php

namespace App\InvitationHandler;

use App\Invitation\SendInviteMail;
use App\Service\Mailer\InviteMailer;
use App\Repository\InvitationRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InviteMailHandler implements MessageHandlerInterface
{

    private $repository;
    private $mailer;

    public function __construct(InvitationRepository $repository, InviteMailer $mailer)
    {
        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    public function __invoke(SendInviteMail $message)
    {
        $mail = $this->repository->findBy(
            array(),                 // Pas de critère
            array('id' => 'asc'), // On trie par date décroissante
            5,                  // On sélectionne $limit annonces
            6                        // À partir du premier
          );
        
        //   dd($mail);

        foreach($mail as $mail)
        {
            $this->mailer->invite($mail->getEmails());
        }
    }
}