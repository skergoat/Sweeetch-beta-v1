<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // $passwordConstraints = [
        //     new NotBlank([
        //         'message' => 'veuillez entrer un putain email, svp'
        //     ]),
        // ];
        // $builder
        // ->add('password', PasswordType::class, [
        //     'required' => true,
        //     'constraints' => $passwordConstraints
        // ]);
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de pass doivent être identiques',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat Password'],
        ]);
        
        $emailConstraints = [
            new NotBlank([
                'message' => 'veuillez entrer un email, svp'
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
            'data_class' => User::class,
        ]);
    }
}
