<?php

namespace App\Form;

use App\Entity\SearchEntity\BlogSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre article'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    
        // on modifie les paramettre (pour les rendre lisibles) dans l'url lors d'une recheche
        public function getBlockPrefix()
        {
            return '';
        }
}
