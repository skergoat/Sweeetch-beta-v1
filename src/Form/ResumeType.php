<?php

namespace App\Form;

use App\Entity\Resume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resume = $options['data'] ?? null;
        $isEdit = $resume && $resume->getId(); 

        $imageConstraints = [
            // new Image([
            //     'maxSize' => '5M'
            // ])
            new NotBlank([
                'message' => 'veuillez uploader un fichier, svp'
            ]),
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
            ->add('file', FileType::class, [
                'mapped' => false,
                'constraints' => $imageConstraints
            ]);

            // ->add('file2', FileType::class, [
            //     'mapped' => false,
            //     'constraints' => $imageConstraints
            // ]);
            // ->add('description')
            // ->add('student')

        // if (!$isEdit || !$resume->getUrl()) {
        //     $imageConstraints[] = new NotNull([
        //         'message' => 'Please upload an image',
        //     ]);
        // }  
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
        ]);
    }
}
