<?php

namespace App\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UserEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $emailConstraints = [
            new NotNull([
                'message' => 'veuillez entrer un mot de passe, svp'
            ]),
            new Regex([
                'pattern' => '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
                'message' => 'email non valide'
            ])
        ];
        $builder
        ->add('email', EmailType::class, [
            'constraints' => $emailConstraints
        ]);  
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity(['fields' => ['email'], 'entityClass' => User::class, 'message' => 'Email déjà utilisé']),
            ],
            'data_class' => User::class,
        ]);
    }
}
