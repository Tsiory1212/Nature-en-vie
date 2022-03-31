<?php

namespace App\Form;

use App\Entity\SubscriptionPlan;
use App\Service\StripeService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionPlanType extends AbstractType
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'label' => 'Nom abonnement',
                'choices' => [
                    'Grand panier' => 'Grand panier',
                    'Panier moyen' => 'Panier moyen',
                    'Petit panier' => 'Petit panier'
                ]
            ])
            ->add('description')
            ->add('interval_unit', ChoiceType::class, [
                'label' => 'Cycle de paiment',
                'choices' => [
                    'JOUR' => 'day',
                    'SEMAINE' => 'week',
                    'MOIS' => 'month',
                    'AN' => 'year'
                ]
            ])
            ->add('detailed_description', CKEditorType::class)
            ->add('amount')
            // ->add('trialPeriodDays', IntegerType::class, [
            //     'required' => false, 
            //     'attr' => [
            //         'placeholder' => 'Nombre de jours d\'essai',
            //         'min' => 0,
            //         'max' => 999
            //     ]
            // ])
            // ->add('durationMonthSubscription', null, [
            //     'label' => 'Durrée de l\'engagement. (En mois)',
            //     'attr' => [
            //         'min' => 0,
            //         'max' => 999
            //     ]
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubscriptionPlan::class,
        ]);
    }

}
