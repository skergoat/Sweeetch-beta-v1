<?php

namespace App\Form;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Form\PicturesType;
use App\Form\UserEditFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UpdateCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyName', TextType::class, [
                'constraints' => [ 
                  new NotBlank(['message' => "Champ requis"]), 
                  new Length([
                      'min' => '2',
                      'max' => '100',
                      'minMessage' => "{{ limit }} caractères minimum",
                      'maxMessage' => "{{ limit }} caractères maximum"
                  ]),
                  new Regex([
                     'pattern' => "/[a-zA-Z0-9- ]/",
                     'message' => "Entrez un nom valide svp"
                  ])
                ],  
            ])
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('address', TextType::class)
            ->add('zipCode', TextType::class)
            ->add('city', TextType::class)
            ->add('telNumber', TextType::class)
            ->add('siret', TextType::class)
            ->add('website', TextType::class)
            ->add('user', UserEditFormType::class);

            $imageConstraints = [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/*',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain',
                    ],
                    'mimeTypesMessage' => 'Document invalide',
                    'maxSizeMessage' => 'poids max : 5M',
                ])
            ];
    
            $builder
                ->add('pictures', FileType::class, [
                    'required' => false,
                    'mapped' => false,
                    'label' => '',
                    'constraints' => $imageConstraints
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
