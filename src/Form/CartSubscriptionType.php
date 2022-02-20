<?php

namespace App\Form;

use App\Entity\CartSubscription;
use App\Service\Paypal\PaypalService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartSubscriptionType extends AbstractType
{
    protected $paypalService;

    public function __construct(PaypalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idProductPlanPaypal', ChoiceType::class, [
                'choices' =>  $this->getProducts(),
                'required' => false,
                'label' => 'Nom du produit repertorié dans PayPal'
            ])
            ->add('nameSubscriptionPlan', ChoiceType::class, [
                'label' => 'Nom abonnement',
                'choices' => [
                    'Grand panier' => 'Grand panier',
                    'Moyen panier' => 'Moyen panier',
                    'Petit panier' => 'Petit panier'
                ]
            ])
            ->add('descriptionSubscriptionPlan')
            ->add('interval_unit', ChoiceType::class, [
                'choices' => [
                    'SEMAINE' => 'WEEK',
                    'MOIS' => 'MONTH'
                ]
            ])
            ->add('detailedDescription', CKEditorType::class)
            ->add('priceSubscription')
            ->add('durationMonthSubscription', null, ['attr' => [
                'min' => 0,
                'max' => 24
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CartSubscription::class,
        ]);
    }

    // Permet de recupérer les produits dans PayPal
    public function getProducts()
    {
        $choices = $this->paypalService->getAllProducts();
        $output = [];
        foreach ($choices as $value) {
            $output[$value['name']] = $value['id'];
        }
        return $output;
    }
}
