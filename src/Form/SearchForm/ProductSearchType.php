<?php

namespace App\Form\SearchForm;

use App\Entity\Category;
use App\Entity\Classement;
use App\Entity\Product;
use App\Entity\SearchEntity\ProductSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' =>  'Nom du produit'
                ]
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'choice_value' => 'name',
                
            ])
            ->add('classement', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Classement::class,
                'choice_label' => 'name',
                'choice_value' => 'name',
            ])
            ->add('gamme', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => $this->getGammeChoices()
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' =>  'Prix maximal'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductSearch::class,
            'method' => 'get',
            'csrf_protection' => false        
        ]);
    }

    // on modifie les paramettre (pour les rendre lisibles) dans l'url lors d'une recheche
    public function getBlockPrefix()
    {
        return '';
    }


    private function getGammeChoices()
    {
        $choices = Product::GAMME;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}
