<?php

namespace App\Form;

use App\Entity\Estate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EstateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control my-2'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'divisor' => 100,
                'attr' => [
                    'class' => 'form-control my-2'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control my-2'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control my-2'
                ]
            ])
            ->add('image', FileType::class, [
                'data_class' => null,
                'label' => 'Image',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '16384k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veullez charger une image valide'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control my-2'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter",
                'attr' => [
                    'class' => 'btn btn-primary my-2'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Estate::class,
        ]);
    }
}
