<?php

namespace App\Form;

use App\Entity\Offers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OffersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $titleConstraints = [
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
                'pattern' => "/[a-zA-Z0-9 !.,_-]+/",
                'message' => "Entrez un nom valide svp"
            ]),
        ];

        $descConstraints = [
            new NotBlank([
                'message' => 'Veuillez entrer une description, svp',
            ]),
        ];


        $builder
            ->add('title', TextType::class, [
                'constraints' => $titleConstraints
            ])
            ->add('location', ChoiceType::class, [
                'choices' => [
                    // 'Auvergne-Rhône-Alpes' => 'Auvergne-Rhône-Alpes',
                    // 'Bourgogne-Franche-Comté' => 'Bourgogne-Franche-Comté',
                    // 'Bretagne' => 'Bretagne',
                    // 'Centre-Val de Loire' => 'Centre-Val de Loire',
                    // 'Corse' => 'Corse',
                    // 'Grand Est' => 'Grand Est',
                    // 'Hauts-de-France' => 'Hauts-de-France',
                    // 'Île-de-France' => 'Île-de-France',
                    // 'Normandie' => 'Normandie',
                    // 'Nouvelle-Aquitaine' => 'Nouvelle-Aquitaine',
                    'Occitanie' => 'Occitanie',
                    // 'Pays de la Loire' => 'Pays de la Loire', 
                    // 'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur'
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'attending_'.strtolower($key)];
                },
            ])
            ->add('domain', ChoiceType::class, [
                'choices' => [
                    'Administration & législation' => 'Grande distribution',
                    'Bâtiment & construction' => 'Vente & Commerce',
                    'Communication' => 'Restauration',
                    'Culture' => 'Artisanat',
                    'Economie & gestion' => 'Marketing & Communication',
                    'Environnement & nature' => 'Assistanat & secrétariat',
                    'Hôtellerie & alimentation' => 'Immobilier',
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'attending_'.strtolower($key)];
                },
            ]);
            $builder
            ->add('dateStart', DateType::Class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                // 'constraints' => $dateConstraints
            ])
            ->add('dateEnd', DateType::Class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                // 'constraints' => $dateConstraints
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => $descConstraints
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offers::class,
        ]);
    }
}
