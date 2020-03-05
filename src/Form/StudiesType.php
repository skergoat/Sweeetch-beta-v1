<?php

namespace App\Form;

use App\Entity\Studies;
use App\Form\SessionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class StudiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $titleConstraints = [
            // new NotBlank([
            //     'message' => 'Veuillez entrer un titre, svp',
            // ]),
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

        // $descConstraints = [
        //     new NotBlank([
        //         'message' => 'Veuillez entrer une description, svp',
        //     ]),
        // ];

        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => $titleConstraints
            ])
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
            ->add('description', TextareaType::class, [
                'label' => '',
                'required' => false,
                // 'constraints' => $descConstraints
            ])
            ->add('sessions', CollectionType::class, array(
                'entry_type' => SessionType::class,
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
            'data_class' => Studies::class,
        ]);
    }
}
