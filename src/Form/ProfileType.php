<?php

namespace App\Form;

use App\Entity\Profile;
use App\Form\LanguageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('domain', ChoiceType::class, [
                'choices' => [
                    'Administration & législation' => 'Administration & législation',
                    'Bâtiment & construction' => 'Bâtiment & construction',
                    'Communication' => 'Communication',
                    'Culture' => 'Culture',
                    'Economie & gestion' => 'Economie & gestion',
                    'Environnement & nature' => 'Environnement & nature',
                    'Hôtellerie & alimentation' => 'Hôtellerie & alimentation',
                    'Informatique & télécommunication' => 'Informatique & télécommunication',
                    'Santé & bien-être' => 'Santé & bien-être',
                    'Sciences' => 'Sciences',
                    'Sciences humaines & sociales' => 'Sciences humaines & sociales',
                    'Sécurité' => 'Sécurité', 
                    'Technique & industrie' => 'Technique & industrie',
                    'Tourisme, sports & loisirs' => 'Tourisme, sports & loisirs',
                    'Transports & logistique' => 'Transports & logistique'
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'attending_'.strtolower($key)];
                },
            ])

            ->add('area', ChoiceType::class, [
                'choices' => [
                    'Auvergne-Rhône-Alpes' => 'Auvergne-Rhône-Alpes',
                    'Bourgogne-Franche-Comté' => 'Bourgogne-Franche-Comté',
                    'Bretagne' => 'Bretagne',
                    'Centre-Val de Loire' => 'Centre-Val de Loire',
                    'Corse' => 'Corse',
                    'Grand Est' => 'Grand Est',
                    'Hauts-de-France' => 'Hauts-de-France',
                    'Île-de-France' => 'Île-de-France',
                    'Normandie' => 'Normandie',
                    'Nouvelle-Aquitaine' => 'Nouvelle-Aquitaine',
                    'Occitanie' => 'Occitanie',
                    'Pays de la Loire' => 'Pays de la Loire', 
                    'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur'
                ],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'attending_'.strtolower($key)];
                },
            ])

            ->add('languages', CollectionType::class, array(
                'entry_type' => LanguagesType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false
            ))
            
            ->add('education', CollectionType::class, array(
                'entry_type' => EducationType::class,
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
            'data_class' => Profile::class,
        ]);
    }
}
