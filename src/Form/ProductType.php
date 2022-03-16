<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Classement;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceId', null, [
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('name')
            ->add('price', null, [
                'attr' => [
                    'step' => '0.01',
                    'type' => 'number',
                    'min' => 0
                ]
            ])
            ->add('weight', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'required' => false,
                'attr' => ['min' => 0]
            ])
            ->add('detail', TextareaType::class, [
                'required' => false
            ])
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'choice_label' => 'name',
            ])
            ->add('classement', EntityType::class, [
                'class' => Classement::class,
                'required' => false,
                'choice_label' => 'name',
            ])
            ->add('gamme', ChoiceType::class, [
                'choices' => $this->getGammeChoices()
            ])
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'forms'
        ]);
    }

    // ici on inverse la value et le key dans le rendu 
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
