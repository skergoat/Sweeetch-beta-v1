<?php

namespace App\Form;

use App\Entity\Resume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ResumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $resume = $options['data'] ?? null;
        $isEdit = $resume && $resume->getId(); 

        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];
        $builder
            ->add('file', FileType::class, [
                'mapped' => false,
                'constraints' => $imageConstraints
            ]);
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
