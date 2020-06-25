<?php

namespace App\Form;

use App\Entity\Offers;
use App\Form\ExperienceType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
                    'Grande distribution' => 'Grande distribution',
                    'Vente & Commerce' => 'Vente & Commerce',
                    'Restauration' => 'Restauration',
                    'Artisanat' => 'Artisanat',
                    'Marketing & Communication' => 'Marketing & Communication',
                    'Assistanat & secrétariat' => 'Assistanat & secrétariat',
                    'Immobilier' => 'Immobilier',
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
            ])
            ->add('dateEnd', DateType::Class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => $descConstraints
            ])
            ->add('skills', CollectionType::class, array(
                'entry_type' => SkillsType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false
            ))
            ->add('experience', CollectionType::class, array(
                'entry_type' => ExperienceType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offers::class,
        ]);
    }
}
