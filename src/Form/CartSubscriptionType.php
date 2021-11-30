<?php

namespace App\Form;

use App\Entity\CartSubscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartSubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameSubscriptionPlan', null, ['label' => 'Nom abonnement'])
            ->add('descriptionSubscriptionPlan')
            ->add('priceSubscription')
            ->add('durationMonthSubscription')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CartSubscription::class,
        ]);
    }
}
