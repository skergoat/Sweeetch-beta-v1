<?php

namespace App\Form;

use App\Entity\ProofHabitation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProofHabitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $card = $options['data'] ?? null;
        $isEdit = $card && $card->getId(); 

        $imageConstraints = [
            // new Image([
            //     'maxSize' => '5M'
            // ])
            new NotBlank(),
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProofHabitation::class,
        ]);
    }
}
