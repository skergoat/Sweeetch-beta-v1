<?php

namespace App\Form;

use App\Entity\Education;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EducationType extends AbstractType
{
    // * @Assert\NotBlank(message="Champ requis")
    // * @Assert\Length(
    // *      min = 2,
    // *      max = 50,
    // *      minMessage = "{{ limit }} caractères minimum",
    // *      maxMessage = "{{ limit }} caractères maximum"
    // * )
    // * @Assert\Regex(
    // *     pattern="/[a-zA-Z0-9- ]/",
    // *     message="Entrez un nom valide svp"
    // * )


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $educConstraints = [
            new NotBlank([
                'message' => 'Veuillez entrer un titre, svp',
            ]),
            new Length([
                'min' => '2',
                'max' => '50',
                'minMessage' => "{{ limit }} caractères minimum",
                'maxMessage' => "{{ limit }} caractères maximum"
            ]),
            new Regex([
                'pattern' => "/[a-zA-Z0-9- ]/",
                'message' => "Entrez un nom valide svp"
            ]),
        ];

        // $dateConstraints = [
        //     new NotBlank([
        //         'message' => 'Veuillez entrer une date, svp',
        //     ]),
        //     new Date([
        //         'message' => 'date invalide',
        //     ]),
        // ];

        $builder
            ->add('title', TextType::class, [
                'constraints' => $educConstraints
            ])
            ->add('school', TextType::class, [
                'constraints' => $educConstraints
            ])
            ->add('date_start', DateType::Class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('date_end', DateType::Class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('current', CheckboxType::Class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Education::class,
        ]);
    }
}
