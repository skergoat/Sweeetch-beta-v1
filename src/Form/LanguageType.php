<?php

namespace App\Form;

use App\Entity\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LanguageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
            ->add('languageName', TextType::class)
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'A0 : Débutant' => 'A0 : Débutant',
                    'A1 : Élémentaire' => 'A1 : Élémentaire',
                    'A2 : Pré-intermédiaire' => 'A2 : Pré-intermédiaire',
                    'B1 : Intermédiaire' => 'B1 : Intermédiaire',
                    'B2 : Intermédiaire supérieur' => 'B2 : Intermédiaire supérieur',
                    'C1 : Avancé' => 'C1 : Avancé',
                    'C2 : Compétent/Courant' => 'C2 : Compétent/Courant',
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'attending_'.strtolower($key)];
                },
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Language::class,
        ]);
    }
}
