<?php

namespace App\Form;

use App\Entity\Student;
use App\Form\IdCardType;
use App\Form\ResumeType;
use App\Form\StudentType;
use App\Form\StudentCardType;
use App\Form\ProofHabitationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UpdateStudentDocType extends StudentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $imageConstraints = [
            new File([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/*',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain'
                ]
            ])
        ];

        $builder
        ->add('resumes', FileType::class, [
            'required' => false,
            'mapped' => false,
            'constraints' => $imageConstraints
        ])
        ->add('idCards', FileType::class, [
            'required' => false,
            'mapped' => false,
            'constraints' => $imageConstraints
        ])
        ->add('studentCards', FileType::class, [
            'required' => false,
            'mapped' => false,
            'constraints' => $imageConstraints
        ])
        ->add('proofHabitations', FileType::class, [
            'required' => false,
            'mapped' => false,
            'constraints' => $imageConstraints
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
