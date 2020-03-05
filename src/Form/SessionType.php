<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('title', TextType::class, [
                'required' => false, 
                'constraints' => [
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
                ]
            ])
            ->add('date_from', DateType::Class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('date_to', DateType::Class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
