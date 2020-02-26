<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $passwordConstraints = [
            new NotNull([
                'message' => 'veuillez entrer un mot de passe, svp'
            ]),
            new Length([
                'min' => '2',
                'max' => '25',
                'minMessage' => "{{ limit }} caractères minimum",
                'maxMessage' => "{{ limit }} caractères maximum"
            ]),
            new Regex([
                'pattern' => '/^(?=.*[A-Z]+)(?=.*[a-z]+)(?=.*[0-9]+)\S{8,30}$/',
                'message' => 'Le mot de passe doit contenir au moins 1 majuscule, 1 caractère spécial et 1 chiffre'
            ]),   
        ];
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat Password'],
            'constraints' => $passwordConstraints
        ]);
        
        $emailConstraints = [
            new NotNull([
                'message' => 'veuillez entrer un email, svp'
            ]),
            new Regex([
                'pattern' => '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
                'message' => 'email non valide'
            ]),
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
                new UniqueEntity(['fields' => ['email'], 'entityClass' => User::class, 'message' => 'Email déjà utilisé'])
            ],
            'data_class' => User::class,
        ]);
    }
}
