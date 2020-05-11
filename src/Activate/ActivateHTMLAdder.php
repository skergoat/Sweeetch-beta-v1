<?php

// create "activate email" header message 

namespace App\Activate;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class ActivateHTMLAdder
{
  // Méthode pour ajouter le « bêta » à une réponse
  public function addActivate(Response $response, User $user)
  {
    $content = $response->getContent();

    switch($user->getRoles())
    {
        case ['ROLE_SCHOOL']:
        case ['ROLE_SUPER_SCHOOL']:
        case['ROLE_RECRUIT']:
        $color = '#85CBE6';
        $message = 'Validez votre email pour pouvoir recruter';
        break;

        case ['ROLE_STUDENT']:
        case ['ROLE_SUPER_STUDENT']:
        $color = '#FFCC66';
        $message = 'Validez votre email pour pouvoir postuler. <a href="/resend/' . $user->getStudent()->getId() . '">Renvoyer le mail</a>';
        break;

        case ['ROLE_COMPANY']:
        case ['ROLE_SUPER_COMPANY']:
        $color = '#FF737B';
        $message = 'Validez votre email pour pouvoir recruter';
        break;
    }

    // Code à rajouter
    // (Je mets ici du CSS en ligne, mais il faudrait utiliser un fichier CSS bien sûr !)
    $html = '<div style="position: relative; top: 0; background:'.$color.'; width: 100%; text-align: center; padding: 0.5em;z-index:999;opacity:0px;">'.$message.'</div><div style="position: absolute; top: 0; background:'.$color.'; width: 100%; text-align: center; padding: 0.5em;z-index:99999;">'.$message.'</div>';

    // Insertion du code dans la page, au début du <body>
    $content = str_replace(
      '<body>',
      '<body> '.$html,
      $content
    );

    // Modification du contenu dans la réponse
    $response->setContent($content);

    return $response;
  }
}