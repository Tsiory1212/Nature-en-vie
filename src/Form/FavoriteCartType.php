<?php

namespace App\Form;

use App\Entity\FavoriteCart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FavoriteCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [ 
                    'placeholder' => 'Donner un nom à ce panier'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [ 
                    'placeholder' => 'Mettez quelques descriptions'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FavoriteCart::class,
        ]);
    }
}
