<?php

namespace App\Form;

use App\Entity\Profile;
use App\Form\LanguageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class)
            ->add('area', TextType::class)
            ->add('languages', CollectionType::class, array(
                'entry_type' => LanguageType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
            ))
            ->add('education', CollectionType::class, array(
                'entry_type' => EducationType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
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
