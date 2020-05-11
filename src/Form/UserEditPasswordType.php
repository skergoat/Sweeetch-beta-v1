<?php

namespace App\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserEditPasswordType extends UserType
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
            // new Regex([
            //     'pattern' => '/^(?=.*[A-Z]+)(?=.*[a-z]+)(?=.*[0-9]+)\S{8,30}$/',
            //     'message' => 'Le mot de passe doit contenir au moins 1 majuscule, 1 caractère spécial et 1 chiffre'
            // ])
        ];
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat Password'],
            // 'constraints' => $passwordConstraints
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
