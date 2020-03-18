<?php

namespace App\Form;

use App\Entity\Offers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FindOffersType extends AbstractType
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Offers::class,
        ]);
    }
}
