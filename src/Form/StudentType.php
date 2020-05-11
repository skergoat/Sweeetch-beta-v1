<?php

namespace App\Form;

use App\Form\UserType;
use App\Entity\Student;
use App\Form\IdCardType;
use App\Form\ResumeType;
use App\Form\ProofHabitationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
// use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->add('adress', TextType::class)
            ->add('zipCode', TextType::class)
            ->add('city', TextType::class)
            ->add('telNumber', TextType::class)
            ->add('user', UserType::class)
            ->add('driving_license', CheckboxType::class, [
                'required' => false
            ])
            ->add('disabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('resume', ResumeType::class)
            ->add('idCard', IdCardType::class)
            // ->add('studentCard', StudentCardType::class)
            ->add('proofHabitation', ProofHabitationType::class)
            // ->add('recaptcha', EWZRecaptchaType::class, array(
            //     'attr' => array(
            //         'options' => array(
            //             'theme' => 'light',
            //             'type'  => 'image',
            //             'size'  => 'invisible',
            //             'defer' => true,
            //             'async' => true,
            //             'callback' => 'onReCaptchaSuccess',
            //             'bind' => 'contact_submit',
            //         )
            //     ),
            //     'mapped'      => false,
            //     'constraints' => array(
            //         new RecaptchaTrue()
            //     )
            //     )
            // )
        ;   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
