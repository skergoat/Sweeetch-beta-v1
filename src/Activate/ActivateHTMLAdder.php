<?php

namespace App\Activate;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class ActivateHTMLAdder
{
  // Méthode pour ajouter le « bêta » à une réponse
  public function addActivate(Response $response, User $user)
  {
    $content = $response->getContent();

    // dd($user->getRoles());

    switch($user->getRoles())
    {
        case ['ROLE_SCHOOL']:
        case ['ROLE_SUPER_SCHOOL']:
        case['ROLE_RECRUIT']:
        $color = 'green';
        $message = 'hello';
        break;

        case ['ROLE_STUDENT']:
        case ['ROLE_SUPER_STUDENT']:
        $color = 'blue';
        $message = 'coucou';
        break;

        case ['ROLE_COMPANY']:
        case ['ROLE_SUPER_COMPANY']:
        $color = 'red';
        $message = 'hey !';
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

    // dd($response);


    return $response;
  }
}