<?php

namespace App\Form;

use App\Entity\Student;
use App\Form\UserEditFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UpdateStudentGeneralType extends AbstractType
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
            // ->add('email', TextType::class, [
            //     'mapped' => false
            // ])
            ->add('user', UserEditFormType::class)
            ->add('driving_license', RadioType::class, [
                'required' => false
            ])
            ->add('disabled', RadioType::class, [
                'required' => false
            ])
            // ->add('resume', ResumeType::class)
            // ->add('idCard', IdCardType::class)
            // ->add('studentCard', StudentCardType::class)
            // ->add('proofHabitation', ProofHabitationType::class)
        ;   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
